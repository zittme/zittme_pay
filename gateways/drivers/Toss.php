<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * 토스페이먼츠.
 *
 * 흐름:
 *   1) 결제 화면이 SDK 로 결제창을 띄운다 (buildRequest 가 넘긴 값으로)
 *   2) 사용자가 결제하면 토스가 successUrl 로 브라우저를 돌려보낸다
 *      (paymentKey, orderId, amount 를 쿼리로 달고 온다)
 *   3) 우리 서버가 confirm API 를 호출해야 비로소 승인이 확정된다 ← approve()
 *
 * 주의: 2번에서 돌아온 amount 는 브라우저를 거쳐 온 값이라 믿을 수 없다. 승인은 반드시
 *    서버가 들고 있는 $order->amount 로 건다. 대조는 컨트롤러에서 한 번 더 한다.
 *
 * 테스트/운영은 엔드포인트가 아니라 키로 갈린다(test_ck_… / live_ck_…). 그래서 이 드라이버는
 * 주소를 바꾸지 않고, 관리자가 넣은 키를 그대로 쓴다.
 */
class Toss extends Base
{
	protected const API_BASE = 'https://api.tosspayments.com/v1';
	protected const SDK_URL = 'https://js.tosspayments.com/v1/payment';

	/**
	 * 토스 결제 상태 → 우리 상태.
	 *
	 * 가상계좌는 승인 API 가 성공해도 아직 돈이 들어오지 않았다(WAITING_FOR_DEPOSIT).
	 * 그때 paid 로 올리면 입금 없이 주문이 확정되는 사고가 난다.
	 */
	protected const STATUS_MAP = [
		'DONE' => Order::STATUS_PAID,
		'WAITING_FOR_DEPOSIT' => Order::STATUS_PENDING,
		'READY' => Order::STATUS_PENDING,
		'IN_PROGRESS' => Order::STATUS_PENDING,
		'CANCELED' => Order::STATUS_CANCELLED,
		'PARTIAL_CANCELED' => Order::STATUS_PARTIAL_CANCELLED,
		'ABORTED' => Order::STATUS_FAILED,
		'EXPIRED' => Order::STATUS_EXPIRED,
	];

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'toss';
	}

	/**
	 * @return bool
	 */
	public function isConfigured(): bool
	{
		return trim((string)$this->config->toss_client_key) !== ''
			&& trim((string)$this->config->toss_secret_key) !== '';
	}

	/**
	 * @return bool
	 */
	public function requiresClientPayment(): bool
	{
		return true;
	}

	/**
	 * @return string
	 */
	public function getClientScript(): string
	{
		return self::SDK_URL;
	}

	/**
	 * 결제창에 넘길 값.
	 *
	 * successUrl 은 우리 콜백이다. state 를 함께 실어 보내 어느 결제였는지 알아본다.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$state = $state !== '' ? $state : (string)\Context::get('pay_state');

		return [
			'clientKey' => trim((string)$this->config->toss_client_key),
			'amount' => (int)$order->amount,
			'orderId' => (string)$order->order_code,
			'orderName' => mb_substr((string)$order->title, 0, 100) ?: (string)$order->order_code,
			'customerName' => (string)$order->payer_name,
			'customerEmail' => (string)$order->payer_email,
			'customerMobilePhone' => preg_replace('/[^0-9]/', '', (string)$order->payer_phone),
			'successUrl' => Base::buildActionUrl('procZittme_payCallback', [
				'gateway' => 'toss',
				'state' => $state,
			]),
			'failUrl' => Base::buildActionUrl('procZittme_payCallback', [
				'gateway' => 'toss',
				'state' => $state,
				'failed' => '1',
			]),
		];
	}

	/**
	 * 서버 승인 (confirm).
	 *
	 * 금액은 언제나 서버가 들고 있는 $order->amount 로 보낸다. 브라우저가 준 값은
	 * 대조용으로만 쓰고 승인에는 절대 쓰지 않는다.
	 *
	 * @param object $order
	 * @param array $params 콜백 쿼리 (paymentKey, orderId, amount)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		$payment_key = trim((string)($params['paymentKey'] ?? ''));
		if ($payment_key === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			self::API_BASE . '/payments/confirm',
			'POST',
			[
				'paymentKey' => $payment_key,
				'orderId' => (string)$order->order_code,
				// 서버 금액. 브라우저가 보낸 값이 아니다.
				'amount' => (int)$order->amount,
			],
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), [
				'tid' => $payment_key,
				'raw' => $body,
			]);
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
		$payment_key = trim((string)$order->pg_tid);
		if ($payment_key === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$data = ['cancelReason' => ($reason !== '' ? mb_substr($reason, 0, 200) : lang('zittme_pay.cancel_default_reason'))];

		// 금액을 넣지 않으면 토스는 전액 취소로 처리한다. 부분취소일 때만 금액을 넘긴다.
		if ($amount > 0 && $amount < (int)$order->amount)
		{
			$data['cancelAmount'] = $amount;
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			self::API_BASE . '/payments/' . rawurlencode($payment_key) . '/cancel',
			'POST',
			$data,
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), [
				'tid' => $payment_key,
				'raw' => $body,
			]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => (string)($parsed['paymentKey'] ?? $payment_key),
			'amount' => $amount > 0 ? $amount : (int)($parsed['totalAmount'] ?? $order->amount),
			'status' => self::STATUS_MAP[(string)($parsed['status'] ?? '')] ?? '',
			'raw' => $body,
		]);
	}

	/**
	 * 단건 조회. 웹훅을 받았을 때 본문을 믿지 않고 이 결과로 판단한다 (보안 3원칙 2).
	 *
	 * @param string $tid paymentKey
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
			self::API_BASE . '/payments/' . rawurlencode($tid),
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
	 * 주문번호로 조회. 웹훅이 paymentKey 없이 orderId 만 줄 때 쓴다.
	 *
	 * @param string $order_code
	 * @return Result
	 */
	public function queryByOrderCode(string $order_code): Result
	{
		$order_code = trim($order_code);
		if ($order_code === '')
		{
			return Result::fail(lang('zittme_pay.msg_order_not_found'));
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			self::API_BASE . '/payments/orders/' . rawurlencode($order_code),
			'GET',
			null,
			$this->authHeaders()
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['raw' => $body]);
		}

		return $this->toResult($parsed, $body);
	}

	/**
	 * 토스 응답을 표준 결과로 옮긴다.
	 *
	 * @param array $parsed
	 * @param string $body
	 * @param ?object $order 있으면 금액을 한 번 더 대조한다
	 * @return Result
	 */
	protected function toResult(array $parsed, string $body, ?object $order = null): Result
	{
		$toss_status = (string)($parsed['status'] ?? '');
		$mapped = self::STATUS_MAP[$toss_status] ?? '';
		$total = (int)($parsed['totalAmount'] ?? 0);
		$payment_key = (string)($parsed['paymentKey'] ?? '');

		// PG 가 확인해 준 금액이 우리 주문 금액과 다르면 승인으로 인정하지 않는다.
		// 여기까지 왔다는 것은 앞선 대조를 통과했다는 뜻이므로, 이 불일치는 사고 신호다.
		if ($order !== null && $total !== (int)$order->amount)
		{
			return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
				'tid' => $payment_key,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		if ($mapped === '')
		{
			return Result::fail(lang('zittme_pay.msg_unknown_pg_status') . ' (' . $toss_status . ')', [
				'tid' => $payment_key,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		// 결제가 깨진 상태로 끝난 경우도 "조회는 성공" 이지만 승인은 아니다.
		if (in_array($mapped, [Order::STATUS_FAILED, Order::STATUS_EXPIRED], true))
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed'), [
				'tid' => $payment_key,
				'amount' => $total,
				'status' => $mapped,
				'raw' => $body,
			]);
		}

		$extra = [];
		if (!empty($parsed['receipt']['url']))
		{
			$extra['receipt_url'] = (string)$parsed['receipt']['url'];
		}
		if (!empty($parsed['card']['issuerCode']))
		{
			$extra['card_issuer'] = (string)$parsed['card']['issuerCode'];
		}
		if (!empty($parsed['virtualAccount']))
		{
			$va = $parsed['virtualAccount'];
			$extra['vbank'] = [
				'bank' => (string)($va['bankCode'] ?? ''),
				'account' => (string)($va['accountNumber'] ?? ''),
				'holder' => (string)($va['customerName'] ?? ''),
				'due_date' => (string)($va['dueDate'] ?? ''),
			];
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $payment_key,
			'amount' => $total,
			'pay_method' => (string)($parsed['method'] ?? ''),
			'status' => $mapped,
			'raw' => $body,
			'extra' => $extra,
		]);
	}

	/**
	 * 인증 헤더.
	 *
	 * 토스는 시크릿 키를 Basic 인증의 사용자명 자리에 넣고 비밀번호는 비운다.
	 *
	 * @return array
	 */
	protected function authHeaders(): array
	{
		$secret = trim((string)$this->config->toss_secret_key);
		return [
			'Authorization' => 'Basic ' . base64_encode($secret . ':'),
			'Content-Type' => 'application/json',
		];
	}

	/**
	 * 토스 오류 응답에서 사람이 읽을 메시지를 뽑는다.
	 *
	 * @param array $parsed
	 * @param int $status_code
	 * @return string
	 */
	protected function errorMessage(array $parsed, int $status_code): string
	{
		$message = trim((string)($parsed['message'] ?? ''));
		$code = trim((string)($parsed['code'] ?? ''));

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
