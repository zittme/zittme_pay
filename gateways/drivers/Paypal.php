<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * 페이팔 (REST Orders v2, 리다이렉트 승인).
 *
 * 흐름:
 *   1) 사용자가 결제하기를 누르면 서버가 페이팔 주문을 만들고(buildRedirect)
 *      승인 페이지(approve 링크)로 브라우저를 보낸다. SDK 는 쓰지 않는다.
 *   2) 사용자가 페이팔에서 승인하면 return_url(우리 콜백)로 돌아온다.
 *      쿼리에 token(페이팔 주문 ID)이 실려 온다.
 *   3) 서버가 capture API 를 호출해야 돈이 넘어온다 ← approve()
 *
 * 통화: 페이팔은 KRW 정산을 지원하지 않는다. 관리자 설정의 결제 통화(기본 USD)와
 * 환율(1 결제통화당 KRW)로 주문 금액을 환산해 보낸다. 적용 환율과 환산 금액은
 * 주문 extra_vars 에 남겨 환불 때 같은 기준으로 되돌린다.
 *
 * 테스트/운영은 전역 test_mode 로 갈린다 (sandbox / live 엔드포인트).
 */
class Paypal extends Base
{
	protected const API_LIVE = 'https://api-m.paypal.com';
	protected const API_SANDBOX = 'https://api-m.sandbox.paypal.com';

	/**
	 * 소수 자리를 쓰지 않는 통화. 페이팔 스펙상 정수로 보내야 한다.
	 */
	protected const ZERO_DECIMAL = ['JPY', 'TWD', 'HUF'];

	/**
	 * 액세스 토큰 캐시. [token, 만료시각]
	 */
	protected static array $_token = ['', 0];

	public function getName(): string
	{
		return 'paypal';
	}

	public function isConfigured(): bool
	{
		return trim((string)$this->config->paypal_client_id) !== ''
			&& trim((string)$this->config->paypal_secret) !== ''
			&& $this->exchangeRate() > 0;
	}

	/**
	 * 결제창 SDK 가 아니라 서버 리다이렉트로 진행한다.
	 */
	public function requiresClientPayment(): bool
	{
		return false;
	}

	public function requiresRedirect(): bool
	{
		return true;
	}

	/**
	 * 결제 화면 표시용 정보만 돌려준다. 페이팔 주문 생성은 buildRedirect 에서 한다.
	 * (이 메서드는 결제 화면을 그릴 때마다 불리므로 여기서 API 를 부르면 안 된다)
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$fx = $this->convert($order);
		return [
			'currency' => $fx['currency'],
			'value' => $fx['value'],
			'rate' => $fx['rate'],
		];
	}

	/**
	 * 페이팔 주문을 만들고 승인 페이지 주소를 돌려준다.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array ['redirect_url' => ..., 'pg_order_id' => ...] 또는 ['error' => 메시지]
	 */
	public function buildRedirect(object $order, string $state): array
	{
		$fx = $this->convert($order);

		$return_url = Base::buildActionUrl('procZittme_payCallback', [
			'gateway' => 'paypal',
			'state' => $state,
		]);
		$cancel_url = Base::buildActionUrl('procZittme_payCallback', [
			'gateway' => 'paypal',
			'state' => $state,
			'failed' => '1',
		]);

		[$ok, $status_code, $body, $parsed] = $this->api('/v2/checkout/orders', 'POST', [
			'intent' => 'CAPTURE',
			'purchase_units' => [[
				'reference_id' => (string)$order->order_code,
				'invoice_id' => (string)$order->order_code,
				'description' => mb_substr((string)$order->title, 0, 120) ?: (string)$order->order_code,
				'amount' => [
					'currency_code' => $fx['currency'],
					'value' => $fx['value'],
				],
			]],
			'payment_source' => [
				'paypal' => [
					'experience_context' => [
						'user_action' => 'PAY_NOW',
						'shipping_preference' => 'NO_SHIPPING',
						'return_url' => $return_url,
						'cancel_url' => $cancel_url,
					],
				],
			],
		]);

		if (!$ok)
		{
			return ['error' => $this->errorMessage($parsed, $status_code), 'raw' => $body];
		}

		$approve_url = '';
		foreach ((array)($parsed['links'] ?? []) as $link)
		{
			if (in_array((string)($link['rel'] ?? ''), ['payer-action', 'approve'], true))
			{
				$approve_url = (string)($link['href'] ?? '');
				break;
			}
		}
		if ($approve_url === '' || empty($parsed['id']))
		{
			return ['error' => lang('zittme_pay.msg_pg_error'), 'raw' => $body];
		}

		return [
			'redirect_url' => $approve_url,
			'pg_order_id' => (string)$parsed['id'],
			'fx' => $fx,
		];
	}

	/**
	 * capture. 돌아온 token(페이팔 주문 ID)으로 결제를 확정한다.
	 *
	 * @param object $order
	 * @param array $params 콜백 쿼리 (token, PayerID)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		$pg_order_id = trim((string)($params['token'] ?? ''));
		if ($pg_order_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->api(
			'/v2/checkout/orders/' . rawurlencode($pg_order_id) . '/capture', 'POST', '{}'
		);

		// 이미 capture 된 주문에 다시 capture 를 걸면 422 ORDER_ALREADY_CAPTURED 가 온다.
		// 콜백이 중복 도착한 경우이므로 조회로 대체해 멱등하게 처리한다.
		// 금액 재대조는 하지 않는다 — 첫 승인 때 이미 대조를 통과했고, 그 사이 환율이
		// 갱신되면 기대 환산액이 달라져 정상 결제가 불일치로 오판될 수 있다.
		if (!$ok && $this->issueCode($parsed) === 'ORDER_ALREADY_CAPTURED')
		{
			return $this->query($pg_order_id);
		}

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), [
				'tid' => $pg_order_id,
				'raw' => $body,
			]);
		}

		return $this->toResult($parsed, $body, $order);
	}

	/**
	 * (부분)환불.
	 *
	 * 결제 통화 기준으로 되돌린다. 부분취소 금액(KRW)은 결제 당시 저장해 둔 환율로 환산한다.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount 0 이면 전액 (KRW)
	 * @return Result
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$stored = $this->storedFx($order);
		$capture_id = $stored['capture_id'];
		if ($capture_id === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$data = [
			'note_to_payer' => mb_substr($reason !== '' ? $reason : lang('zittme_pay.cancel_default_reason'), 0, 255),
		];

		// 금액을 넣지 않으면 페이팔은 남은 전액을 환불한다. 부분취소일 때만 금액을 넘긴다.
		if ($amount > 0 && $amount < (int)$order->amount)
		{
			$order_currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';
			if ($order_currency !== 'KRW')
			{
				// 외화 주문 — 취소 금액도 이미 통화 최소단위 정수다
				$data['amount'] = [
					'currency_code' => $order_currency,
					'value' => $this->formatValue(
						\Zittme\Modules\Zittme_pay\Models\Currency::fromMinor($amount, $order_currency), $order_currency
					),
				];
			}
			else
			{
				// KRW 주문 — 결제 당시 저장해 둔 환율로 되돌린다
				$rate = $stored['rate'] > 0 ? $stored['rate'] : $this->exchangeRate();
				$data['amount'] = [
					'currency_code' => $stored['currency'],
					'value' => $this->formatValue($amount / $rate, $stored['currency']),
				];
			}
		}

		[$ok, $status_code, $body, $parsed] = $this->api(
			'/v2/payments/captures/' . rawurlencode($capture_id) . '/refund', 'POST', $data
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), [
				'tid' => $capture_id,
				'raw' => $body,
			]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => (string)($parsed['id'] ?? $capture_id),
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => ($amount > 0 && $amount < (int)$order->amount)
				? Order::STATUS_PARTIAL_CANCELLED
				: Order::STATUS_CANCELLED,
			'raw' => $body,
		]);
	}

	/**
	 * 단건 조회. 웹훅 본문을 믿지 않고 재확인할 때와 중복 capture 처리에 쓴다.
	 *
	 * @param string $tid 페이팔 주문 ID
	 * @param ?object $order
	 * @return Result
	 */
	public function query(string $tid, ?object $order = null): Result
	{
		$tid = trim($tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->api('/v2/checkout/orders/' . rawurlencode($tid), 'GET');

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return $this->toResult($parsed, $body, $order);
	}

	/* ---------------------------------------------------------------------
	 * 내부
	 * ------------------------------------------------------------------- */

	/**
	 * 페이팔 응답을 표준 결과로 옮긴다. capture 금액과 통화를 환산 기대값과 대조한다.
	 */
	protected function toResult(array $parsed, string $body, ?object $order = null): Result
	{
		$pg_order_id = (string)($parsed['id'] ?? '');
		$status = (string)($parsed['status'] ?? '');

		if ($status !== 'COMPLETED')
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ' (' . $status . ')', [
				'tid' => $pg_order_id,
				'raw' => $body,
			]);
		}

		$capture = (array)($parsed['purchase_units'][0]['payments']['captures'][0] ?? []);
		$captured_value = (string)($capture['amount']['value'] ?? '');
		$captured_currency = (string)($capture['amount']['currency_code'] ?? '');
		$capture_id = (string)($capture['id'] ?? '');

		if ($order !== null)
		{
			$fx = $this->convert($order);
			if ($captured_currency !== $fx['currency']
				|| $this->formatValue((float)$captured_value, $captured_currency) !== $fx['value'])
			{
				return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
					'tid' => $pg_order_id,
					'raw' => $body,
				]);
			}
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $pg_order_id,
			'amount' => $order !== null ? (int)$order->amount : 0,
			'pay_method' => 'paypal',
			'status' => Order::STATUS_PAID,
			'raw' => $body,
			'extra' => [
				'paypal' => [
					'capture_id' => $capture_id,
					'currency' => $captured_currency,
					'value' => $captured_value,
					'rate' => $this->exchangeRate(),
					'payer_email' => (string)($parsed['payer']['email_address'] ?? ''),
				],
			],
		]);
	}

	/**
	 * 페이팔이 직접 결제할 수 있는 통화.
	 */
	protected const SUPPORTED_CURRENCIES = [
		'USD', 'EUR', 'JPY', 'GBP', 'AUD', 'CAD', 'SGD', 'HKD', 'TWD', 'CNY',
		'CHF', 'CZK', 'DKK', 'HUF', 'ILS', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP',
		'PLN', 'SEK', 'THB', 'BRL',
	];

	/**
	 * KRW 는 환율이 있어야 환산 결제가 되고, 외화 주문은 지원 통화면 그대로 결제한다.
	 */
	public function supportsCurrency(string $currency): bool
	{
		$currency = strtoupper($currency);
		if ($currency === 'KRW')
		{
			return $this->exchangeRate() > 0;
		}
		return in_array($currency, self::SUPPORTED_CURRENCIES, true);
	}

	/**
	 * 주문 금액을 페이팔에 보낼 통화·금액으로 옮긴다.
	 *
	 * 외화 주문(주문 통화가 KRW 가 아님)은 금액이 이미 통화 최소단위 정수라 환산 없이
	 * 자릿수만 되돌린다. KRW 주문은 관리자 설정의 결제 통화로 공용 환율에 따라 환산한다.
	 *
	 * @return array ['currency' => 'USD', 'value' => '12.34', 'rate' => 1350.0]
	 */
	protected function convert(object $order): array
	{
		$amount = (int)$order->amount;
		$order_currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';

		if ($order_currency !== 'KRW')
		{
			$value = \Zittme\Modules\Zittme_pay\Models\Currency::fromMinor($amount, $order_currency);
			return ['currency' => $order_currency, 'value' => $this->formatValue($value, $order_currency), 'rate' => 1.0];
		}

		$currency = strtoupper(trim((string)$this->config->paypal_currency)) ?: 'USD';
		$rate = $this->exchangeRate();
		$value = $rate > 0 ? ($amount / $rate) : 0;
		return ['currency' => $currency, 'value' => $this->formatValue($value, $currency), 'rate' => $rate];
	}

	/**
	 * 통화별 자리수에 맞춘 금액 문자열.
	 */
	protected function formatValue(float $value, string $currency): string
	{
		if (in_array($currency, self::ZERO_DECIMAL, true))
		{
			return (string)(int)round($value);
		}
		return number_format(round($value, 2), 2, '.', '');
	}

	/**
	 * 결제 통화 환율 (1 결제통화당 KRW). 공용 환율(Currency)이 단일 출처다.
	 * (Currency 가 구 paypal_exchange_rate 값도 이어받으므로 여기서 따로 읽지 않는다)
	 */
	protected function exchangeRate(): float
	{
		$currency = strtoupper(trim((string)$this->config->paypal_currency)) ?: 'USD';
		return \Zittme\Modules\Zittme_pay\Models\Currency::getRate($currency);
	}

	/**
	 * 결제 당시 저장한 환산 정보. 환불 때 같은 기준을 쓰기 위한 것이다.
	 */
	protected function storedFx(object $order): array
	{
		$extra = json_decode((string)($order->extra_vars ?? ''), true);
		$pp = is_array($extra) ? (array)($extra['paypal'] ?? []) : [];
		return [
			'capture_id' => (string)($pp['capture_id'] ?? ''),
			'currency' => (string)($pp['currency'] ?? (strtoupper(trim((string)$this->config->paypal_currency)) ?: 'USD')),
			'rate' => (float)($pp['rate'] ?? 0),
		];
	}

	/**
	 * 인증 붙여서 API 호출. 토큰이 없거나 만료됐으면 새로 받는다.
	 */
	protected function api(string $path, string $method, $data = null): array
	{
		$token = $this->accessToken();
		if ($token === '')
		{
			return [false, 0, lang('zittme_pay.msg_pg_unreachable'), []];
		}

		return $this->request($this->apiBase() . $path, $method, $data, [
			'Authorization' => 'Bearer ' . $token,
			'Content-Type' => 'application/json',
		]);
	}

	/**
	 * OAuth2 client_credentials 토큰.
	 */
	protected function accessToken(): string
	{
		if (self::$_token[0] !== '' && self::$_token[1] > time() + 60)
		{
			return self::$_token[0];
		}

		$auth = base64_encode(trim((string)$this->config->paypal_client_id) . ':' . trim((string)$this->config->paypal_secret));
		[$ok, , , $parsed] = $this->request(
			$this->apiBase() . '/v1/oauth2/token',
			'POST',
			['grant_type' => 'client_credentials'],
			['Authorization' => 'Basic ' . $auth, 'Content-Type' => 'application/x-www-form-urlencoded']
		);

		if (!$ok || empty($parsed['access_token']))
		{
			return '';
		}

		self::$_token = [(string)$parsed['access_token'], time() + (int)($parsed['expires_in'] ?? 0)];
		return self::$_token[0];
	}

	protected function apiBase(): string
	{
		return ($this->config->test_mode === 'Y') ? self::API_SANDBOX : self::API_LIVE;
	}

	/**
	 * 페이팔 오류 응답의 issue 코드. (details[0].issue)
	 */
	protected function issueCode(array $parsed): string
	{
		return (string)($parsed['details'][0]['issue'] ?? '');
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$detail = trim((string)($parsed['details'][0]['description'] ?? ''));
		$message = trim((string)($parsed['message'] ?? ''));
		$name = trim((string)($parsed['name'] ?? ''));

		$text = $detail !== '' ? $detail : $message;
		if ($text !== '')
		{
			return $name !== '' ? ($text . ' (' . $name . ')') : $text;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
