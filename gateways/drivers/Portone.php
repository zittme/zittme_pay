<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * 포트원 (V2).
 *
 * 드라이버 하나로 포트원에 연결된 여러 PG 를 쓴다. 어느 PG 로 나갈지는 포트원 콘솔의
 * 채널에서 정하므로, 여기서는 채널 키만 받는다.
 *
 * 흐름:
 *   1) 결제 화면이 브라우저 SDK 로 결제창을 띄운다 (buildRequest 가 넘긴 값으로)
 *      paymentId 는 우리 주문번호(order_code)를 그대로 쓴다.
 *   2) 결제가 끝나면 SDK 콜백(PC) 또는 redirectUrl(모바일)로 돌아온다.
 *   3) 포트원 결제창의 결과는 믿지 않는다. 서버가 조회 API 로 상태·금액을 확인해야
 *      비로소 승인이다 ← approve()
 *
 * 테스트/운영은 채널 키로 갈린다 (포트원 콘솔에서 테스트 채널을 따로 만든다).
 */
class Portone extends Base
{
	protected const API_BASE = 'https://api.portone.io';
	protected const SDK_URL = 'https://cdn.portone.io/v2/browser-sdk.js';

	/**
	 * 포트원 결제 상태 → 우리 상태.
	 *
	 * 가상계좌 발급(VIRTUAL_ACCOUNT_ISSUED)은 아직 돈이 들어오지 않았다. 입금 웹훅이
	 * 온 뒤 조회에서 PAID 로 바뀐다.
	 */
	protected const STATUS_MAP = [
		'PAID' => Order::STATUS_PAID,
		'VIRTUAL_ACCOUNT_ISSUED' => Order::STATUS_PENDING,
		'READY' => Order::STATUS_PENDING,
		'PENDING' => Order::STATUS_PENDING,
		'CANCELLED' => Order::STATUS_CANCELLED,
		'PARTIAL_CANCELLED' => Order::STATUS_PARTIAL_CANCELLED,
		'FAILED' => Order::STATUS_FAILED,
	];

	public function getName(): string
	{
		return 'portone';
	}

	public function isConfigured(): bool
	{
		return trim((string)$this->config->portone_store_id) !== ''
			&& trim((string)$this->config->portone_channel_key) !== ''
			&& trim((string)$this->config->portone_api_secret) !== '';
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
	 * 결제창에 넘길 값. redirectUrl 은 모바일에서 리다이렉트로 돌아올 때 쓰인다.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$state = $state !== '' ? $state : (string)\Context::get('pay_state');
		$currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';

		return [
			'storeId' => trim((string)$this->config->portone_store_id),
			'channelKey' => trim((string)$this->config->portone_channel_key),
			'paymentId' => (string)$order->order_code,
			'orderName' => mb_substr((string)$order->title, 0, 100) ?: (string)$order->order_code,
			'totalAmount' => (int)$order->amount,
			'currency' => 'CURRENCY_' . $currency,
			'customer' => [
				'fullName' => (string)$order->payer_name,
				'email' => (string)$order->payer_email,
				'phoneNumber' => preg_replace('/[^0-9]/', '', (string)$order->payer_phone),
			],
			'redirectUrl' => Base::buildActionUrl('procZittme_payCallback', [
				'gateway' => 'portone',
				'state' => $state,
			]),
		];
	}

	/**
	 * 서버 확인 승인.
	 *
	 * 포트원 결제창의 성공 응답은 참고일 뿐이다. 조회 API 의 상태·금액이 우리 주문과
	 * 일치해야 승인으로 인정한다.
	 *
	 * @param object $order
	 * @param array $params 콜백 값 (paymentId)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		$payment_id = trim((string)($params['paymentId'] ?? ''));
		if ($payment_id === '')
		{
			// 리다이렉트 복귀에 paymentId 가 없으면 주문번호가 곧 paymentId 다
			$payment_id = (string)$order->order_code;
		}
		if ($payment_id !== (string)$order->order_code)
		{
			return Result::fail(lang('zittme_pay.msg_order_not_found'));
		}
		return $this->query($payment_id, $order);
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
		$payment_id = trim((string)$order->pg_tid) ?: (string)$order->order_code;

		$data = [
			'reason' => mb_substr($reason !== '' ? $reason : lang('zittme_pay.cancel_default_reason'), 0, 200),
		];
		// 금액을 넣지 않으면 포트원은 남은 전액을 취소한다. 부분취소일 때만 금액을 넘긴다.
		if ($amount > 0 && $amount < (int)$order->amount)
		{
			$data['amount'] = $amount;
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			self::API_BASE . '/payments/' . rawurlencode($payment_id) . '/cancel',
			'POST',
			$data,
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), [
				'tid' => $payment_id,
				'raw' => $body,
			]);
		}

		$cancel_status = (string)($parsed['cancellation']['status'] ?? '');
		if ($cancel_status !== '' && !in_array($cancel_status, ['SUCCEEDED', 'REQUESTED'], true))
		{
			return Result::fail(lang('zittme_pay.msg_cancel_failed') . ' (' . $cancel_status . ')', [
				'tid' => $payment_id,
				'raw' => $body,
			]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => $payment_id,
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => ($amount > 0 && $amount < (int)$order->amount)
				? Order::STATUS_PARTIAL_CANCELLED
				: Order::STATUS_CANCELLED,
			'raw' => $body,
		]);
	}

	/**
	 * 단건 조회. 웹훅을 받았을 때도 본문을 믿지 않고 이 결과로 판단한다 (보안 3원칙 2).
	 *
	 * @param string $tid paymentId (= order_code)
	 * @param ?object $order 있으면 금액·통화를 대조한다
	 * @return Result
	 */
	public function query(string $tid, ?object $order = null): Result
	{
		$tid = trim($tid);
		if ($tid === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			self::API_BASE . '/payments/' . rawurlencode($tid),
			'GET',
			null,
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		$po_status = (string)($parsed['status'] ?? '');
		$mapped = self::STATUS_MAP[$po_status] ?? '';
		$total = (int)($parsed['amount']['total'] ?? 0);
		$currency = strtoupper((string)($parsed['currency'] ?? 'KRW'));

		// PG 가 확인해 준 금액·통화가 우리 주문과 다르면 승인으로 인정하지 않는다.
		if ($order !== null)
		{
			$order_currency = strtoupper(trim((string)($order->currency ?? ''))) ?: 'KRW';
			if ($total !== (int)$order->amount || $currency !== $order_currency)
			{
				return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
					'tid' => $tid,
					'amount' => $total,
					'raw' => $body,
				]);
			}
		}

		if ($mapped === '')
		{
			return Result::fail(lang('zittme_pay.msg_unknown_pg_status') . ' (' . $po_status . ')', [
				'tid' => $tid,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		if ($mapped === Order::STATUS_FAILED)
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
		$method = (string)($parsed['method']['type'] ?? '');
		if (!empty($parsed['method']['provider']))
		{
			$method .= '/' . (string)$parsed['method']['provider'];
		}
		if ($po_status === 'VIRTUAL_ACCOUNT_ISSUED' && !empty($parsed['method']['bank']))
		{
			$extra['vbank'] = [
				'bank' => (string)($parsed['method']['bank'] ?? ''),
				'account' => (string)($parsed['method']['accountNumber'] ?? ''),
				'holder' => (string)($parsed['method']['remitteeName'] ?? ''),
				'due_date' => (string)($parsed['method']['expiredAt'] ?? ''),
			];
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $tid,
			'amount' => $total,
			'pay_method' => $method,
			'status' => $mapped,
			'raw' => $body,
			'extra' => $extra,
		]);
	}

	/**
	 * 인증 헤더. V2 는 API Secret 을 PortOne 스킴으로 넣는다.
	 */
	protected function authHeaders(): array
	{
		return [
			'Authorization' => 'PortOne ' . trim((string)$this->config->portone_api_secret),
			'Content-Type' => 'application/json',
		];
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$message = trim((string)($parsed['message'] ?? ''));
		$type = trim((string)($parsed['type'] ?? ''));

		if ($message !== '')
		{
			return $type !== '' ? ($message . ' (' . $type . ')') : $message;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
