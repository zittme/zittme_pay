<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Currency;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * Conekta (멕시코 · 중남미, Orders API + Hosted Checkout).
 *
 * 흐름:
 *   1) 결제하기를 누르면 서버가 Conekta 주문을 만들고(buildRedirect) 호스티드 결제
 *      페이지(checkout.url)로 브라우저를 보낸다.
 *   2) 결제가 끝나면 success_url(우리 콜백)로 돌아온다. 콜백은 주문을 다시 조회해
 *      payment_status 가 paid 면 결제 완료로 확정한다 ← approve()
 *   3) OXXO(현금) · SPEI(계좌이체)는 돌아온 시점에 pending_payment 다. 주문을 입금 대기로
 *      두고, 입금이 되면 웹훅 order.paid 가 와서 query() 재조회 뒤 확정한다.
 *
 * 통화: 주문 통화가 MXN 등 Conekta 가 받는 통화면 그대로 보내고, KRW 주문은 설정의
 * 결제 통화로 공용 환율에 따라 환산한다(페이팔과 같은 규칙).
 *
 * 테스트/운영은 키로 갈린다 (key_test… / key_live…). 엔드포인트는 하나다.
 */
class Conekta extends Base
{
	protected const API_BASE = 'https://api.conekta.io';
	protected const API_VERSION = '2.1.0';

	/**
	 * Conekta 가 결제를 받는 통화.
	 */
	protected const SUPPORTED_CURRENCIES = ['MXN', 'USD'];

	/**
	 * 호스티드 결제 페이지에 열 수 있는 결제수단.
	 */
	public const METHODS = ['card', 'cash', 'bank_transfer'];

	/**
	 * 호스티드 결제 페이지 만료 (초).
	 */
	protected const CHECKOUT_TTL = 86400 * 3;

	public function getName(): string
	{
		return 'conekta';
	}

	public function isConfigured(): bool
	{
		return trim((string)$this->config->conekta_private_key) !== '' && count($this->methods()) > 0;
	}

	public function requiresClientPayment(): bool
	{
		return false;
	}

	public function requiresRedirect(): bool
	{
		return true;
	}

	/**
	 * 현금·계좌이체가 켜져 있으면 돌아온 시점에 입금 대기일 수 있다.
	 * 실제 상태는 approve() 가 조회 결과로 정한다.
	 */
	public function getInitialStatus(): string
	{
		return Order::STATUS_PENDING;
	}

	public static function currencyChoices(): array
	{
		return self::SUPPORTED_CURRENCIES;
	}

	public function supportsCurrency(string $currency): bool
	{
		$currency = strtoupper($currency);
		if ($currency === 'KRW')
		{
			return ($this->config->conekta_allow_krw ?? 'N') === 'Y' && $this->exchangeRate() > 0;
		}
		return in_array($currency, self::SUPPORTED_CURRENCIES, true);
	}

	/**
	 * 결제 화면 표시용 정보. Conekta 주문 생성은 buildRedirect 에서 한다.
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$fx = $this->convert($order);
		return [
			'currency' => $fx['currency'],
			'value' => $fx['value'],
			'rate' => $fx['rate'],
			'methods' => $this->methods(),
		];
	}

	/**
	 * Conekta 주문을 만들고 호스티드 결제 페이지 주소를 돌려준다.
	 *
	 * @return array ['redirect_url' => ..., 'pg_order_id' => ...] 또는 ['error' => 메시지]
	 */
	public function buildRedirect(object $order, string $state): array
	{
		$fx = $this->convert($order);

		$success_url = Base::buildActionUrl('procZittme_payCallback', [
			'gateway' => 'conekta',
			'state' => $state,
		]);
		$failure_url = Base::buildActionUrl('procZittme_payCallback', [
			'gateway' => 'conekta',
			'state' => $state,
			'failed' => '1',
		]);

		// Conekta 는 customer_info 의 name · email · phone 을 모두 요구한다
		$phone = preg_replace('/[^0-9+]/', '', (string)($order->payer_phone ?? ''));
		$customer = [
			'name' => mb_substr(trim((string)($order->payer_name ?? '')) ?: 'Customer', 0, 80),
			'email' => trim((string)($order->payer_email ?? '')) ?: 'noreply@' . $this->hostName(),
			'phone' => $phone !== '' ? $phone : '+5200000000',
		];

		[$ok, $status_code, $body, $parsed] = $this->api('/orders', 'POST', [
			'currency' => $fx['currency'],
			'customer_info' => $customer,
			'line_items' => [[
				'name' => mb_substr((string)$order->title ?: (string)$order->order_code, 0, 250),
				'unit_price' => $fx['minor'],
				'quantity' => 1,
			]],
			'metadata' => [
				'order_code' => (string)$order->order_code,
			],
			'checkout' => [
				'type' => 'HostedPayment',
				'allowed_payment_methods' => $this->methods(),
				'success_url' => $success_url,
				'failure_url' => $failure_url,
				'expires_at' => time() + self::CHECKOUT_TTL,
			],
		]);

		if (!$ok)
		{
			return ['error' => $this->errorMessage($parsed, $status_code), 'raw' => $body];
		}

		$url = (string)($parsed['checkout']['url'] ?? '');
		if ($url === '' || empty($parsed['id']))
		{
			return ['error' => lang('zittme_pay.msg_pg_error'), 'raw' => $body];
		}

		return [
			'redirect_url' => $url,
			'pg_order_id' => (string)$parsed['id'],
			'fx' => $fx,
		];
	}

	/**
	 * 복귀 콜백. 주문을 다시 조회해 상태를 정한다.
	 *
	 * 호스티드 페이지는 쿼리에 주문 ID 를 실어 주지 않을 수 있으므로
	 * buildRedirect 때 저장해 둔 pg_tid 를 쓴다.
	 */
	public function approve(object $order, array $params): Result
	{
		$pg_order_id = trim((string)($params['checkout_id'] ?? $params['order_id'] ?? $order->pg_tid ?? ''));
		if ($pg_order_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}
		return $this->query($pg_order_id, $order);
	}

	/**
	 * 단건 조회. 콜백 확정과 웹훅 재확인에 쓴다.
	 *
	 * paid 면 결제 완료, pending_payment 면 입금 대기(OXXO·SPEI), 그 외는 실패.
	 */
	public function query(string $tid, ?object $order = null): Result
	{
		$tid = trim($tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/orders/' . rawurlencode($tid), 'GET');
		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return $this->toResult($parsed, $body, $order);
	}

	/**
	 * (부분)환불. 카드 결제만 API 로 되돌릴 수 있다. 현금·계좌이체는 수동 환불이다.
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$stored = $this->storedFx($order);
		$pg_order_id = trim((string)($order->pg_tid ?? ''));
		if ($pg_order_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}
		if ($stored['method'] !== '' && $stored['method'] !== 'card')
		{
			return Result::fail(lang('zittme_pay.msg_conekta_manual_refund'));
		}

		$full = !($amount > 0 && $amount < (int)$order->amount);
		$order_currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';
		if ($full)
		{
			$refund_minor = $stored['minor'];
		}
		elseif ($order_currency !== 'KRW')
		{
			$refund_minor = $amount;
		}
		else
		{
			$rate = $stored['rate'] > 0 ? $stored['rate'] : $this->exchangeRate();
			$refund_minor = Currency::toMinor($amount / $rate, $stored['currency']);
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/orders/' . rawurlencode($pg_order_id) . '/refunds', 'POST', [
			'amount' => (int)$refund_minor,
			'reason' => 'requested_by_client',
		]);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $pg_order_id, 'raw' => $body]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => $pg_order_id,
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => $full ? Order::STATUS_CANCELLED : Order::STATUS_PARTIAL_CANCELLED,
			'raw' => $body,
		]);
	}

	/* ---------------------------------------------------------------------
	 * 내부
	 * ------------------------------------------------------------------- */

	/**
	 * Conekta 주문 응답을 표준 결과로 옮긴다. 금액·통화를 기대값과 대조한다.
	 */
	protected function toResult(array $parsed, string $body, ?object $order = null): Result
	{
		$pg_order_id = (string)($parsed['id'] ?? '');
		$payment_status = (string)($parsed['payment_status'] ?? '');
		$currency = strtoupper((string)($parsed['currency'] ?? ''));
		$minor = (int)($parsed['amount'] ?? 0);

		$charge = (array)($parsed['charges']['data'][0] ?? []);
		$method = (string)($charge['payment_method']['type'] ?? $charge['payment_method']['object'] ?? '');
		$method = $this->normalizeMethod($method);

		if ($order !== null)
		{
			$fx = $this->convert($order);
			if ($currency !== $fx['currency'] || $minor !== (int)$fx['minor'])
			{
				return Result::fail(lang('zittme_pay.msg_amount_mismatch'), ['tid' => $pg_order_id, 'raw' => $body]);
			}
		}

		$extra = [
			'conekta' => [
				'currency' => $currency,
				'minor' => $minor,
				'rate' => $order !== null ? $this->convert($order)['rate'] : 0,
				'method' => $method,
				'charge_id' => (string)($charge['id'] ?? ''),
			],
		];

		if ($payment_status === 'paid')
		{
			return Result::ok([
				'message' => lang('zittme_pay.msg_approve_success'),
				'tid' => $pg_order_id,
				'amount' => $order !== null ? (int)$order->amount : 0,
				'pay_method' => 'conekta',
				'status' => Order::STATUS_PAID,
				'raw' => $body,
				'extra' => $extra,
			]);
		}

		if ($payment_status === 'pending_payment')
		{
			// OXXO 참조번호 · SPEI 가상계좌 등 입금 안내. 결과 화면이 보여 준다
			$extra['conekta']['reference'] = (string)($charge['payment_method']['reference'] ?? $charge['payment_method']['clabe'] ?? '');
			$extra['conekta']['bank'] = (string)($charge['payment_method']['bank'] ?? '');
			$expires = (int)($charge['payment_method']['expires_at'] ?? 0);
			if ($expires > 0)
			{
				$extra['due_date'] = date('YmdHis', $expires);
			}
			return Result::ok([
				'message' => lang('zittme_pay.msg_conekta_pending'),
				'tid' => $pg_order_id,
				'amount' => $order !== null ? (int)$order->amount : 0,
				'pay_method' => 'conekta',
				'status' => Order::STATUS_PENDING,
				'raw' => $body,
				'extra' => $extra,
			]);
		}

		if (in_array($payment_status, ['refunded', 'partially_refunded'], true))
		{
			return Result::ok([
				'message' => lang('zittme_pay.msg_cancel_success'),
				'tid' => $pg_order_id,
				'amount' => $order !== null ? (int)$order->amount : 0,
				'status' => $payment_status === 'refunded' ? Order::STATUS_CANCELLED : Order::STATUS_PARTIAL_CANCELLED,
				'raw' => $body,
			]);
		}

		if ($payment_status === 'expired')
		{
			return Result::ok([
				'message' => lang('zittme_pay.msg_payment_not_completed'),
				'tid' => $pg_order_id,
				'amount' => 0,
				'status' => Order::STATUS_EXPIRED,
				'raw' => $body,
			]);
		}

		return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ' (' . $payment_status . ')', [
			'tid' => $pg_order_id,
			'raw' => $body,
		]);
	}

	/**
	 * Conekta 의 결제수단 표기를 설정 값(card / cash / bank_transfer)으로 맞춘다.
	 */
	protected function normalizeMethod(string $type): string
	{
		$type = strtolower($type);
		if (strpos($type, 'card') !== false)
		{
			return 'card';
		}
		if (strpos($type, 'cash') !== false || strpos($type, 'oxxo') !== false)
		{
			return 'cash';
		}
		if (strpos($type, 'spei') !== false || strpos($type, 'bank') !== false)
		{
			return 'bank_transfer';
		}
		return $type;
	}

	/**
	 * 설정에서 켠 결제수단. 비어 있으면 전부.
	 */
	public function methods(): array
	{
		$raw = $this->config->conekta_methods ?? null;
		$list = is_array($raw) ? $raw : preg_split('/[\s,]+/', (string)$raw, -1, PREG_SPLIT_NO_EMPTY);
		$list = array_values(array_intersect(self::METHODS, array_map('strval', (array)$list)));
		return count($list) ? $list : self::METHODS;
	}

	/**
	 * 주문 금액을 Conekta 에 보낼 통화·최소단위 금액으로 옮긴다.
	 *
	 * @return array ['currency' => 'MXN', 'minor' => 38250, 'value' => '382.50', 'rate' => 환율]
	 */
	protected function convert(object $order): array
	{
		$amount = (int)$order->amount;
		$order_currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';

		if ($order_currency !== 'KRW')
		{
			return [
				'currency' => $order_currency,
				'minor' => $amount,
				'value' => Currency::format(Currency::fromMinor($amount, $order_currency), $order_currency),
				'rate' => 1.0,
			];
		}

		$currency = strtoupper(trim((string)$this->config->conekta_currency)) ?: 'MXN';
		$rate = $this->exchangeRate();
		$value = $rate > 0 ? ($amount / $rate) : 0;
		$minor = Currency::toMinor($value, $currency);
		return [
			'currency' => $currency,
			'minor' => $minor,
			'value' => Currency::format(Currency::fromMinor($minor, $currency), $currency),
			'rate' => $rate,
		];
	}

	/**
	 * 결제 통화 환율 (1 결제통화당 KRW). 공용 환율이 단일 출처다.
	 */
	protected function exchangeRate(): float
	{
		$currency = strtoupper(trim((string)$this->config->conekta_currency)) ?: 'MXN';
		return Currency::getRate($currency);
	}

	/**
	 * 결제 당시 저장한 환산 정보. 환불 때 같은 기준을 쓴다.
	 */
	protected function storedFx(object $order): array
	{
		$extra = json_decode((string)($order->extra_vars ?? ''), true);
		$ck = is_array($extra) ? (array)($extra['conekta'] ?? []) : [];
		return [
			'currency' => (string)($ck['currency'] ?? (strtoupper(trim((string)$this->config->conekta_currency)) ?: 'MXN')),
			'minor' => (int)($ck['minor'] ?? 0),
			'rate' => (float)($ck['rate'] ?? 0),
			'method' => (string)($ck['method'] ?? ''),
		];
	}

	/**
	 * 인증 붙여서 API 호출. Basic 인증(비밀 키:) 을 쓴다.
	 */
	protected function api(string $path, string $method, $data = null): array
	{
		$key = trim((string)$this->config->conekta_private_key);
		if ($key === '')
		{
			return [false, 0, lang('zittme_pay.msg_conekta_test_empty'), []];
		}

		return $this->request(self::API_BASE . $path, $method, $data === null ? null : json_encode($data, \JSON_UNESCAPED_UNICODE), [
			'Authorization' => 'Basic ' . base64_encode($key . ':'),
			'Accept' => 'application/vnd.conekta-v' . self::API_VERSION . '+json',
			'Content-Type' => 'application/json',
		]);
	}

	/**
	 * 결제까지 가지 않고 인증만 해 본다. 정상이면 빈 문자열, 아니면 사유.
	 */
	public function checkConnection(string $private_key = ''): string
	{
		if ($private_key !== '')
		{
			$this->config->conekta_private_key = $private_key;
		}

		[$ok, $status, $body, $parsed] = $this->api('/orders?limit=1', 'GET');
		if ($ok)
		{
			return '';
		}
		if ($status === 401 || $status === 403)
		{
			return lang('zittme_pay.msg_conekta_auth_failed');
		}
		if ($status === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return $this->errorMessage($parsed, $status);
	}

	/**
	 * 지금 어느 키로 부르고 있는지 (테스트 / 실거래). 키 접두사로 판단한다.
	 */
	public function modeLabel(): string
	{
		$key = trim((string)$this->config->conekta_private_key);
		return lang(strpos($key, 'key_test') === 0 ? 'zittme_pay.paypal_mode_sandbox' : 'zittme_pay.paypal_mode_live');
	}

	protected function hostName(): string
	{
		$host = (string)parse_url(\Zittme\Framework\URL::getCurrentDomainURL(), PHP_URL_HOST);
		return $host !== '' ? $host : 'localhost';
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$detail = trim((string)($parsed['details'][0]['message'] ?? $parsed['details'][0]['debug_message'] ?? ''));
		$type = trim((string)($parsed['type'] ?? ''));
		if ($detail !== '')
		{
			return $type !== '' ? ($detail . ' (' . $type . ')') : $detail;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
