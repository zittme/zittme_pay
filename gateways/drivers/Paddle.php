<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * Paddle Billing (해외 결제, Merchant of Record).
 *
 * 흐름:
 *   1) 결제하기를 누르면 서버가 Paddle 거래(transaction)를 만든다 ← prepareClientPayment()
 *      금액은 카탈로그 상품이 아니라 주문마다 만드는 가격(non-catalog price)으로 싣고,
 *      custom_data 에 짓미페이 order_code 를 담는다.
 *   2) 브라우저가 Paddle.js 오버레이 체크아웃을 그 거래 ID 로 연다.
 *   3) 결제가 끝나면(checkout.completed) 콜백으로 돌아온다. 콜백은 저장해 둔 거래 ID 로
 *      API 를 다시 조회해 paid · completed 일 때만 확정한다 ← approve()
 *   4) 웹훅: transaction.completed · transaction.paid 는 결제 확정, adjustment.created ·
 *      adjustment.updated 는 환불·차지백. Paddle-Signature 를 검증한 뒤에도 본문 값은 쓰지 않고
 *      API 재조회 결과만 반영한다.
 *
 * 세금: Paddle 이 판매자(MoR)라 국가별 세금은 체크아웃에서 Paddle 이 더한다(또는 포함한다).
 * 주문 금액은 세전 소계와 대조하고, 환불 금액은 실제 결제 총액 대비 비율로 환산한다.
 *
 * 통화: USD 전용. 금액은 짓미페이 주문과 같이 최소 단위(센트)다.
 */
class Paddle extends Base
{
	protected const API_LIVE = 'https://api.paddle.com';
	protected const API_SANDBOX = 'https://sandbox-api.paddle.com';
	protected const API_VERSION = '1';
	protected const CLIENT_SCRIPT = 'https://cdn.paddle.com/paddle/v2/paddle.js';

	protected const SUPPORTED_CURRENCIES = ['USD'];

	/**
	 * 서명 시각 허용 오차(초). 이보다 오래된 웹훅은 재전송 공격으로 보고 버린다.
	 */
	public const SIGNATURE_TOLERANCE = 300;

	/**
	 * 받아서 처리하는 웹훅 이벤트. 나머지는 200 으로 답하고 무시한다.
	 */
	public const WEBHOOK_EVENTS = [
		'transaction.completed',
		'transaction.paid',
		'adjustment.created',
		'adjustment.updated',
	];

	/**
	 * 결제 완료로 보는 거래 상태.
	 */
	protected const PAID_STATUSES = ['paid', 'completed'];

	/**
	 * 결제가 아직 진행 중인 거래 상태.
	 */
	protected const OPEN_STATUSES = ['draft', 'ready', 'billed'];

	/**
	 * 콜백 시점에 거래가 아직 진행 중이면 입금 대기로 두는 시간(초). 그 사이 웹훅이 확정한다.
	 */
	protected const PENDING_TTL = 86400;

	public function getName(): string
	{
		return 'paddle';
	}

	/**
	 * API 키 · 클라이언트 토큰 · 웹훅 시크릿이 모두 있어야 쓸 수 있다.
	 * 웹훅이 없으면 환불·차지백이 장부에 반영되지 않는다.
	 */
	public function isConfigured(): bool
	{
		return trim((string)($this->config->paddle_api_key ?? '')) !== ''
			&& trim((string)($this->config->paddle_client_token ?? '')) !== ''
			&& trim((string)($this->config->paddle_webhook_secret ?? '')) !== '';
	}

	public function requiresClientPayment(): bool
	{
		return true;
	}

	/**
	 * 해외 구매자는 휴대폰 번호가 없어도 된다. 청구 정보는 Paddle 체크아웃이 받는다.
	 */
	public function requiresPayerPhone(): bool
	{
		return false;
	}

	public function getClientScript(): string
	{
		return self::CLIENT_SCRIPT;
	}

	public static function currencyChoices(): array
	{
		return self::SUPPORTED_CURRENCIES;
	}

	public function supportsCurrency(string $currency): bool
	{
		return in_array(strtoupper($currency), self::SUPPORTED_CURRENCIES, true);
	}

	public function isSandbox(): bool
	{
		return ($this->config->paddle_mode ?? 'sandbox') !== 'live';
	}

	/**
	 * 결제 화면 표시용 값. 거래 생성은 결제를 시작할 때(prepareClientPayment) 한다.
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		return [
			'environment' => $this->isSandbox() ? 'sandbox' : 'production',
			'currency' => $this->orderCurrency($order),
		];
	}

	/**
	 * Paddle 거래를 만들고 오버레이 체크아웃에 넘길 값을 돌려준다.
	 *
	 * @return array 성공: transactionId 등, 실패: ['error' => 메시지]
	 */
	public function prepareClientPayment(object $order, string $state): array
	{
		$currency = $this->orderCurrency($order);
		if (!$this->supportsCurrency($currency))
		{
			return ['error' => lang('zittme_pay.msg_paddle_currency')];
		}

		$title = mb_substr(trim((string)$order->title) ?: (string)$order->order_code, 0, 150);

		[$ok, $status_code, $body, $parsed] = $this->api('/transactions', 'POST', [
			'items' => [[
				'quantity' => 1,
				'price' => [
					'description' => $title,
					'name' => $title,
					'unit_price' => [
						'amount' => (string)(int)$order->amount,
						'currency_code' => $currency,
					],
					'product' => [
						'name' => $title,
						'tax_category' => 'standard',
					],
				],
			]],
			'currency_code' => $currency,
			'collection_mode' => 'automatic',
			'custom_data' => [
				'order_code' => (string)$order->order_code,
			],
		]);

		if (!$ok)
		{
			return ['error' => $this->errorMessage($parsed, $status_code), 'raw' => $body];
		}

		$txn_id = (string)($parsed['data']['id'] ?? '');
		if ($txn_id === '')
		{
			return ['error' => lang('zittme_pay.msg_pg_error'), 'raw' => $body];
		}

		$email = trim((string)($order->payer_email ?? ''));
		return [
			'environment' => $this->isSandbox() ? 'sandbox' : 'production',
			'clientToken' => trim((string)$this->config->paddle_client_token),
			'transactionId' => $txn_id,
			'customerEmail' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '',
			'locale' => $this->checkoutLocale(),
			'returnUrl' => Base::buildActionUrl('procZittme_payCallback', [
				'gateway' => 'paddle',
				'state' => $state,
			]),
			'pg_order_id' => $txn_id,
			'raw' => $body,
		];
	}

	/**
	 * 복귀 콜백. 브라우저가 보낸 값은 쓰지 않고, 결제 시작 때 저장한 거래 ID 로 재조회한다.
	 */
	public function approve(object $order, array $params): Result
	{
		$txn_id = trim((string)($order->pg_tid ?? ''));
		if ($txn_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		// checkout.completed 직후에는 거래 상태 반영이 잠깐 늦을 수 있다
		$result = $this->query($txn_id, $order);
		for ($i = 0; $i < 2 && $result->success && $result->status === Order::STATUS_PENDING; $i++)
		{
			sleep(1);
			$result = $this->query($txn_id, $order);
		}

		if ($result->success && !in_array($result->status, [Order::STATUS_PAID, Order::STATUS_PENDING], true))
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed'), ['tid' => $txn_id, 'raw' => $result->raw]);
		}
		return $result;
	}

	/**
	 * 단건 조회. 콜백 확정과 웹훅 재확인에 쓴다.
	 *
	 * 승인된 환불·차지백이 있으면 취소 상태로, 없으면 거래 상태에 따라 결제 완료 · 대기 · 실패.
	 */
	public function query(string $tid, ?object $order = null): Result
	{
		$tid = trim($tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/transactions/' . rawurlencode($tid) . '?include=adjustments', 'GET');
		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return $this->toResult((array)($parsed['data'] ?? []), $body, $order);
	}

	/**
	 * 환불. Paddle 은 환불 요청(adjustment)을 만들고 검토 후 승인한다.
	 *
	 * 요청이 접수되면 장부는 바로 취소로 둔다. 승인 결과는 adjustment.updated 웹훅으로 오며,
	 * 거절되면 로그에 남는다.
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$txn_id = trim((string)($order->pg_tid ?? ''));
		if ($txn_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/transactions/' . rawurlencode($txn_id) . '?include=adjustments', 'GET');
		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $txn_id, 'raw' => $body]);
		}
		$txn = (array)($parsed['data'] ?? []);

		$order_amount = (int)$order->amount;
		$already = (int)($order->cancelled_amount ?? 0);
		$amount = ($amount > 0) ? $amount : ($order_amount - $already);
		$reaches_total = ($already + $amount) >= $order_amount;
		$reason = trim($reason) !== '' ? mb_substr(trim($reason), 0, 250) : 'Requested by merchant';

		$payload = [
			'action' => 'refund',
			'transaction_id' => $txn_id,
			'reason' => $reason,
		];

		if ($reaches_total && $already === 0)
		{
			$payload['type'] = 'full';
		}
		else
		{
			$item_id = (string)($txn['details']['line_items'][0]['id'] ?? '');
			if ($item_id === '')
			{
				return Result::fail(lang('zittme_pay.msg_pg_error'), ['tid' => $txn_id, 'raw' => $body]);
			}
			$gross = $reaches_total
				? max(0, self::grandTotal($txn) - self::adjustedTotal($txn, ['approved', 'pending_approval']))
				: self::toGross($amount, $order_amount, self::grandTotal($txn));
			if ($gross <= 0)
			{
				return Result::fail(lang('zittme_pay.msg_invalid_cancel_amount'), ['tid' => $txn_id]);
			}
			$payload['type'] = 'partial';
			$payload['items'] = [[
				'item_id' => $item_id,
				'type' => 'partial',
				'amount' => (string)$gross,
			]];
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/adjustments', 'POST', $payload);
		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $txn_id, 'raw' => $body]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_paddle_refund_requested'),
			'tid' => $txn_id,
			'amount' => $amount,
			'status' => $reaches_total ? Order::STATUS_CANCELLED : Order::STATUS_PARTIAL_CANCELLED,
			'raw' => $body,
			'extra' => [
				'paddle_adjustment_id' => (string)($parsed['data']['id'] ?? ''),
			],
		]);
	}

	/* ---------------------------------------------------------------------
	 * 웹훅
	 * ------------------------------------------------------------------- */

	public function handlesSignedWebhook(): bool
	{
		return true;
	}

	/**
	 * 웹훅 서명을 검증하고 주문을 찾을 값을 꺼낸다.
	 *
	 * @param string $raw 요청 본문 원문 (서명 대상이므로 가공하지 않은 그대로)
	 * @param array $headers 소문자 헤더 이름 => 값
	 * @return ?array 서명 불일치면 null. 처리 대상이 아니면 ['ignore' => true],
	 *                아니면 ['order_code' => ..., 'tid' => ..., 'event' => ...]
	 */
	public function parseWebhook(string $raw, array $headers): ?array
	{
		$secret = trim((string)($this->config->paddle_webhook_secret ?? ''));
		$signature = (string)($headers['paddle-signature'] ?? '');
		if ($secret === '' || !self::verifySignature($signature, $raw, $secret, time()))
		{
			return null;
		}

		$body = json_decode($raw, true);
		if (!is_array($body))
		{
			return ['ignore' => true];
		}

		$event = (string)($body['event_type'] ?? '');
		$data = is_array($body['data'] ?? null) ? $body['data'] : [];
		if (!in_array($event, self::WEBHOOK_EVENTS, true))
		{
			return ['ignore' => true, 'event' => $event];
		}

		if (strpos($event, 'transaction.') === 0)
		{
			return [
				'event' => $event,
				'tid' => (string)($data['id'] ?? ''),
				'order_code' => (string)($data['custom_data']['order_code'] ?? ''),
			];
		}

		// adjustment 에는 custom_data 가 없다. 거래를 조회해 주문번호를 얻는다
		$txn_id = (string)($data['transaction_id'] ?? '');
		if ($txn_id === '')
		{
			return ['ignore' => true, 'event' => $event];
		}
		[$ok, , , $parsed] = $this->api('/transactions/' . rawurlencode($txn_id), 'GET');
		return [
			'event' => $event,
			'tid' => $txn_id,
			'order_code' => $ok ? (string)($parsed['data']['custom_data']['order_code'] ?? '') : '',
		];
	}

	/**
	 * Paddle-Signature 헤더 검증.
	 *
	 * 헤더 형식은 "ts=1671552777;h1=<hex>" 이고, 시크릿을 바꾸는 동안에는 h1 이 여러 개 온다.
	 * 서명 대상은 "{ts}:{본문 원문}", 알고리즘은 HMAC-SHA256.
	 */
	public static function verifySignature(string $header, string $raw, string $secret, int $now, int $tolerance = self::SIGNATURE_TOLERANCE): bool
	{
		if ($header === '' || $secret === '')
		{
			return false;
		}

		$ts = '';
		$hashes = [];
		foreach (explode(';', $header) as $part)
		{
			$pair = explode('=', trim($part), 2);
			if (count($pair) !== 2)
			{
				continue;
			}
			if ($pair[0] === 'ts')
			{
				$ts = $pair[1];
			}
			elseif ($pair[0] === 'h1')
			{
				$hashes[] = strtolower($pair[1]);
			}
		}

		if (!ctype_digit($ts) || !count($hashes))
		{
			return false;
		}
		if ($tolerance > 0 && abs($now - (int)$ts) > $tolerance)
		{
			return false;
		}

		$expected = hash_hmac('sha256', $ts . ':' . $raw, $secret);
		foreach ($hashes as $hash)
		{
			if (hash_equals($expected, $hash))
			{
				return true;
			}
		}
		return false;
	}

	/* ---------------------------------------------------------------------
	 * 내부
	 * ------------------------------------------------------------------- */

	/**
	 * Paddle 거래를 표준 결과로 옮긴다. 주문번호 · 통화 · 세전 금액을 주문과 대조한다.
	 */
	protected function toResult(array $txn, string $body, ?object $order = null): Result
	{
		$txn_id = (string)($txn['id'] ?? '');
		$status = (string)($txn['status'] ?? '');
		$currency = strtoupper((string)($txn['currency_code'] ?? ''));

		if ($order !== null)
		{
			$order_code = (string)($txn['custom_data']['order_code'] ?? '');
			if ($order_code !== (string)$order->order_code
				|| $currency !== $this->orderCurrency($order)
				|| self::itemsSubtotal($txn) !== (int)$order->amount)
			{
				return Result::fail(lang('zittme_pay.msg_amount_mismatch'), ['tid' => $txn_id, 'raw' => $body]);
			}
		}

		$order_amount = $order !== null ? (int)$order->amount : 0;
		$grand_total = self::grandTotal($txn);
		$refunded_gross = self::adjustedTotal($txn, ['approved']);

		$extra = [
			'paddle' => [
				'currency' => $currency,
				'grand_total' => $grand_total,
				'status' => $status,
				'method' => (string)($txn['payments'][0]['method_details']['type'] ?? ''),
				'customer_id' => (string)($txn['customer_id'] ?? ''),
			],
		];

		if ($refunded_gross > 0 && in_array($status, self::PAID_STATUSES, true))
		{
			$refunded = ($grand_total > 0 && $refunded_gross >= $grand_total)
				? $order_amount
				: min($order_amount, self::toNet($refunded_gross, $order_amount, $grand_total));
			$extra['refunded_amount'] = $refunded;
			return Result::ok([
				'message' => lang('zittme_pay.msg_cancel_success'),
				'tid' => $txn_id,
				'amount' => $order_amount,
				'status' => $refunded >= $order_amount ? Order::STATUS_CANCELLED : Order::STATUS_PARTIAL_CANCELLED,
				'raw' => $body,
				'extra' => $extra,
			]);
		}

		if (in_array($status, self::PAID_STATUSES, true))
		{
			return Result::ok([
				'message' => lang('zittme_pay.msg_approve_success'),
				'tid' => $txn_id,
				'amount' => $order_amount,
				'pay_method' => 'paddle',
				'status' => Order::STATUS_PAID,
				'raw' => $body,
				'extra' => $extra,
			]);
		}

		if (in_array($status, self::OPEN_STATUSES, true))
		{
			$extra['due_date'] = date('YmdHis', time() + self::PENDING_TTL);
			return Result::ok([
				'message' => lang('zittme_pay.msg_paddle_pending'),
				'tid' => $txn_id,
				'amount' => $order_amount,
				'pay_method' => 'paddle',
				'status' => Order::STATUS_PENDING,
				'raw' => $body,
				'extra' => $extra,
			]);
		}

		if ($status === 'canceled')
		{
			return Result::ok([
				'message' => lang('zittme_pay.msg_payment_not_completed'),
				'tid' => $txn_id,
				'amount' => $order_amount,
				'status' => Order::STATUS_FAILED,
				'raw' => $body,
			]);
		}

		return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ' (' . $status . ')', [
			'tid' => $txn_id,
			'raw' => $body,
		]);
	}

	/**
	 * 거래 품목의 세전 금액 합계 (단가 × 수량). 우리가 만든 가격이므로 주문 금액과 같아야 한다.
	 */
	public static function itemsSubtotal(array $txn): int
	{
		$sum = 0;
		foreach ((array)($txn['items'] ?? []) as $item)
		{
			$sum += (int)($item['price']['unit_price']['amount'] ?? 0) * max(1, (int)($item['quantity'] ?? 1));
		}
		return $sum;
	}

	/**
	 * 구매자가 실제로 낸 총액 (세금 포함).
	 */
	public static function grandTotal(array $txn): int
	{
		return (int)($txn['details']['totals']['grand_total'] ?? $txn['details']['totals']['total'] ?? 0);
	}

	/**
	 * 환불 · 차지백으로 돌려준 총액 (세금 포함). 차지백 취소(chargeback_reverse)는 뺀다.
	 *
	 * @param array $statuses 셀 조정 상태 (approved, pending_approval …)
	 */
	public static function adjustedTotal(array $txn, array $statuses): int
	{
		$sum = 0;
		foreach ((array)($txn['adjustments'] ?? []) as $adj)
		{
			if (!in_array((string)($adj['status'] ?? ''), $statuses, true))
			{
				continue;
			}
			$total = (int)($adj['totals']['total'] ?? 0);
			$action = (string)($adj['action'] ?? '');
			if ($action === 'refund' || $action === 'chargeback')
			{
				$sum += $total;
			}
			elseif ($action === 'chargeback_reverse')
			{
				$sum -= $total;
			}
		}
		return max(0, $sum);
	}

	/**
	 * 주문 금액(세전)을 결제 총액(세금 포함) 기준으로 옮긴다.
	 */
	public static function toGross(int $net, int $order_amount, int $grand_total): int
	{
		if ($order_amount <= 0 || $grand_total <= 0)
		{
			return $net;
		}
		return (int)round($net * $grand_total / $order_amount);
	}

	/**
	 * 결제 총액 기준 금액을 주문 금액(세전) 기준으로 되돌린다.
	 */
	public static function toNet(int $gross, int $order_amount, int $grand_total): int
	{
		if ($order_amount <= 0 || $grand_total <= 0)
		{
			return $gross;
		}
		return (int)round($gross * $order_amount / $grand_total);
	}

	protected function orderCurrency(object $order): string
	{
		return strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';
	}

	/**
	 * 체크아웃 표시 언어. Paddle 이 지원하지 않는 언어는 영어로 연다.
	 */
	protected function checkoutLocale(): string
	{
		$lang = strtolower((string)\Context::getLangType());
		$map = [
			'ko' => 'ko', 'en' => 'en', 'ja' => 'ja', 'zh-cn' => 'zh-Hans', 'zh-tw' => 'zh-Hans',
			'de' => 'de', 'fr' => 'fr', 'es' => 'es', 'ru' => 'ru', 'tr' => 'tr', 'vi' => 'vi',
		];
		return $map[$lang] ?? 'en';
	}

	/**
	 * 인증 붙여서 API 호출.
	 */
	protected function api(string $path, string $method, $data = null): array
	{
		$key = trim((string)($this->config->paddle_api_key ?? ''));
		if ($key === '')
		{
			return [false, 0, lang('zittme_pay.msg_paddle_key_empty'), []];
		}

		return $this->request(($this->isSandbox() ? self::API_SANDBOX : self::API_LIVE) . $path, $method,
			$data === null ? null : json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES), [
			'Authorization' => 'Bearer ' . $key,
			'Paddle-Version' => self::API_VERSION,
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
		]);
	}

	/**
	 * 결제까지 가지 않고 인증만 해 본다. 정상이면 빈 문자열, 아니면 사유.
	 */
	public function checkConnection(string $api_key = '', string $mode = ''): string
	{
		if ($api_key !== '')
		{
			$this->config->paddle_api_key = $api_key;
		}
		if ($mode !== '')
		{
			$this->config->paddle_mode = $mode === 'live' ? 'live' : 'sandbox';
		}

		[$ok, $status, , $parsed] = $this->api('/event-types', 'GET');
		if ($ok)
		{
			return '';
		}
		if ($status === 401 || $status === 403)
		{
			return lang('zittme_pay.msg_paddle_auth_failed');
		}
		if ($status === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return $this->errorMessage($parsed, $status);
	}

	public function modeLabel(): string
	{
		return $this->isSandbox() ? lang('zittme_pay.paypal_mode_sandbox') : lang('zittme_pay.paypal_mode_live');
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$detail = trim((string)($parsed['error']['detail'] ?? ''));
		$code = trim((string)($parsed['error']['code'] ?? ''));
		if ($detail !== '')
		{
			return $code !== '' ? ($detail . ' (' . $code . ')') : $detail;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
