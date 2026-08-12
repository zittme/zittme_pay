<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * 나이스페이 (신형 v1 API, 서버 승인 모델).
 *
 * 흐름:
 *   1) 결제 화면이 JS SDK(AUTHNICE.requestPay)로 결제창을 띄운다
 *   2) 인증이 끝나면 나이스페이가 returnUrl 로 브라우저를 POST 로 돌려보낸다
 *      (authResultCode, tid, orderId, amount 가 실려 온다)
 *   3) 서버가 승인 API 를 호출해야 비로소 돈이 넘어온다 ← approve()
 *
 * 주의: 2번에서 돌아온 amount 는 브라우저를 거쳐 온 값이라 믿을 수 없다. 승인은 반드시
 *    서버가 들고 있는 $order->amount 로 걸고, 응답의 amount 를 다시 대조한다.
 *
 * 테스트/운영은 전역 test_mode 로 갈린다 (sandbox-api / api 엔드포인트).
 * 샌드박스 키는 나이스페이 개발자센터(developers.nicepay.co.kr) 가입으로 발급받는다.
 */
class Nicepay extends Base
{
	protected const API_LIVE = 'https://api.nicepay.co.kr';
	protected const API_SANDBOX = 'https://sandbox-api.nicepay.co.kr';
	protected const SDK_URL = 'https://pay.nicepay.co.kr/v1/js/';

	/**
	 * 나이스페이 결제 상태 → 우리 상태.
	 *
	 * 가상계좌 발급 직후는 ready 다. 입금 통지(웹훅)가 온 뒤 조회에서 paid 로 바뀐다.
	 */
	protected const STATUS_MAP = [
		'paid' => Order::STATUS_PAID,
		'ready' => Order::STATUS_PENDING,
		'cancelled' => Order::STATUS_CANCELLED,
		'partialCancelled' => Order::STATUS_PARTIAL_CANCELLED,
		'failed' => Order::STATUS_FAILED,
		'expired' => Order::STATUS_EXPIRED,
	];

	public function getName(): string
	{
		return 'nicepay';
	}

	public function isConfigured(): bool
	{
		return trim((string)$this->config->nicepay_client_id) !== ''
			&& trim((string)$this->config->nicepay_secret_key) !== '';
	}

	public function requiresClientPayment(): bool
	{
		return true;
	}

	public function getClientScript(): string
	{
		return self::SDK_URL;
	}

	/**
	 * 결제창(AUTHNICE.requestPay)에 넘길 값.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$state = $state !== '' ? $state : (string)\Context::get('pay_state');

		return [
			'clientId' => trim((string)$this->config->nicepay_client_id),
			'method' => 'card',
			'orderId' => (string)$order->order_code,
			'amount' => (int)$order->amount,
			'goodsName' => mb_substr((string)$order->title, 0, 40) ?: (string)$order->order_code,
			'buyerName' => (string)$order->payer_name,
			'buyerEmail' => (string)$order->payer_email,
			'buyerTel' => preg_replace('/[^0-9]/', '', (string)$order->payer_phone),
			'returnUrl' => Base::buildActionUrl('procZittme_payCallback', [
				'gateway' => 'nicepay',
				'state' => $state,
			]),
		];
	}

	/**
	 * 서버 승인.
	 *
	 * @param object $order
	 * @param array $params 콜백 값 (authResultCode, tid, amount)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		$auth_code = trim((string)($params['authResultCode'] ?? ''));
		$tid = trim((string)($params['tid'] ?? ''));
		if ($auth_code !== '0000' || $tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ($auth_code !== '' ? ' (' . $auth_code . ')' : ''));
		}

		// 서버 금액으로 승인을 건다. 브라우저가 보낸 값이 아니다.
		$amount = (int)$order->amount;
		$edi_date = date('c');
		[$ok, $status_code, $body, $parsed] = $this->request(
			$this->apiBase() . '/v1/payments/' . rawurlencode($tid),
			'POST',
			[
				'amount' => $amount,
				'ediDate' => $edi_date,
				'signData' => hash('sha256', $tid . $amount . $edi_date . $this->secretKey()),
			],
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return $this->toResult($parsed, $body, $order);
	}

	/**
	 * (부분)취소.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount 0 이면 전액
	 * @return Result
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$tid = trim((string)$order->pg_tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$edi_date = date('c');
		$data = [
			'reason' => mb_substr($reason !== '' ? $reason : lang('zittme_pay.cancel_default_reason'), 0, 100),
			'orderId' => (string)$order->order_code,
			'ediDate' => $edi_date,
			'signData' => hash('sha256', $tid . $edi_date . $this->secretKey()),
		];
		// 금액을 넣지 않으면 나이스페이는 전액 취소로 처리한다. 부분취소일 때만 금액을 넘긴다.
		if ($amount > 0 && $amount < (int)$order->amount)
		{
			$data['cancelAmt'] = $amount;
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			$this->apiBase() . '/v1/payments/' . rawurlencode($tid) . '/cancel',
			'POST',
			$data,
			$this->authHeaders()
		);

		if (!$ok || (string)($parsed['resultCode'] ?? '') !== '0000')
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => (string)($parsed['tid'] ?? $tid),
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => self::STATUS_MAP[(string)($parsed['status'] ?? '')] ?? '',
			'raw' => $body,
		]);
	}

	/**
	 * 단건 조회. 웹훅 본문을 믿지 않고 재확인할 때 쓴다 (보안 3원칙 2).
	 *
	 * @param string $tid
	 * @return Result
	 */
	public function query(string $tid): Result
	{
		$tid = trim($tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			$this->apiBase() . '/v1/payments/' . rawurlencode($tid),
			'GET',
			null,
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return $this->toResult($parsed, $body);
	}

	/**
	 * 나이스페이 응답을 표준 결과로 옮긴다.
	 *
	 * @param array $parsed
	 * @param string $body
	 * @param ?object $order 있으면 금액을 한 번 더 대조한다
	 * @return Result
	 */
	protected function toResult(array $parsed, string $body, ?object $order = null): Result
	{
		$result_code = (string)($parsed['resultCode'] ?? '');
		$np_status = (string)($parsed['status'] ?? '');
		$mapped = self::STATUS_MAP[$np_status] ?? '';
		$total = (int)($parsed['amount'] ?? 0);
		$tid = (string)($parsed['tid'] ?? '');

		if ($result_code !== '0000')
		{
			return Result::fail($this->errorMessage($parsed, 200), ['tid' => $tid, 'raw' => $body]);
		}

		if ($order !== null && $total !== (int)$order->amount)
		{
			return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
				'tid' => $tid,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		if ($mapped === '')
		{
			return Result::fail(lang('zittme_pay.msg_unknown_pg_status') . ' (' . $np_status . ')', [
				'tid' => $tid,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		if (in_array($mapped, [Order::STATUS_FAILED, Order::STATUS_EXPIRED], true))
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed'), [
				'tid' => $tid,
				'amount' => $total,
				'status' => $mapped,
				'raw' => $body,
			]);
		}

		$extra = [];
		if (!empty($parsed['receiptUrl']))
		{
			$extra['receipt_url'] = (string)$parsed['receiptUrl'];
		}
		if (!empty($parsed['vbank']))
		{
			$vb = $parsed['vbank'];
			$extra['vbank'] = [
				'bank' => (string)($vb['vbankName'] ?? ''),
				'account' => (string)($vb['vbankNumber'] ?? ''),
				'holder' => (string)($vb['vbankHolder'] ?? ''),
				'due_date' => (string)($vb['vbankExpDate'] ?? ''),
			];
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $tid,
			'amount' => $total,
			'pay_method' => (string)($parsed['payMethod'] ?? ''),
			'status' => $mapped,
			'raw' => $body,
			'extra' => $extra,
		]);
	}

	/**
	 * Basic 인증 — clientId:secretKey.
	 */
	protected function authHeaders(): array
	{
		$auth = trim((string)$this->config->nicepay_client_id) . ':' . $this->secretKey();
		return [
			'Authorization' => 'Basic ' . base64_encode($auth),
			'Content-Type' => 'application/json',
		];
	}

	protected function secretKey(): string
	{
		return trim((string)$this->config->nicepay_secret_key);
	}

	protected function apiBase(): string
	{
		return ($this->config->test_mode === 'Y') ? self::API_SANDBOX : self::API_LIVE;
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$message = trim((string)($parsed['resultMsg'] ?? ''));
		$code = trim((string)($parsed['resultCode'] ?? ''));

		if ($message !== '')
		{
			return $code !== '' ? ($message . ' (' . $code . ')') : $message;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
