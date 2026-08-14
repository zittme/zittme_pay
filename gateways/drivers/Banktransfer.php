<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * 무통장입금.
 *
 * PG 가 개입하지 않는다. 우리가 계좌를 알려 주고, 사람이 입금하고, 관리자가 확인해 승인한다.
 * 그래서 결제창도 없고(requiresClientPayment=false) 외부 통신도 없다.
 *
 * 이 드라이버가 중요한 이유는 따로 있다. PG 계약과 키 발급을 기다리지 않고도
 * "주문 생성 → 결제수단 선택 → 승인 → 트리거 → 요청자 모듈 반영 → 취소" 전 구간을
 * 실제로 돌려 볼 수 있다. 커머스·예약 연동 검증이 PG 일정에 묶이지 않는다.
 */
class Banktransfer extends Base
{
	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'banktransfer';
	}

	/**
	 * PG 가 없으니 통화 제약도 없다. 어느 통화 주문이든 계좌 입금으로 받는다.
	 *
	 * @param string $currency
	 * @return bool
	 */
	public function supportsCurrency(string $currency): bool
	{
		return true;
	}

	/**
	 * 입금받을 계좌가 하나라도 등록돼 있어야 쓸 수 있다.
	 *
	 * @return bool
	 */
	public function isConfigured(): bool
	{
		foreach ($this->accounts() as $account)
		{
			if (($account['bank'] ?? '') !== '' && ($account['account'] ?? '') !== '')
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 결제창이 없다. 서버에서 바로 처리한다.
	 *
	 * @return bool
	 */
	public function requiresClientPayment(): bool
	{
		return false;
	}

	/**
	 * 입금을 기다려야 하므로 곧바로 paid 가 되지 않는다.
	 *
	 * @return string
	 */
	public function getInitialStatus(): string
	{
		return Order::STATUS_PENDING;
	}

	/**
	 * 계좌이체는 되돌릴 PG 가 없다. 환불은 언제나 사람이 송금해야 한다.
	 *
	 * 그래서 취소는 항상 수동 환불 대기로 넘어간다.
	 *
	 * @param object $order
	 * @return bool
	 */
	public function supportsAutoCancel(object $order): bool
	{
		return false;
	}

	/**
	 * 결제 화면에 보여 줄 계좌 목록과 입금 기한.
	 *
	 * 콜백이 없는 결제수단이라 $state 는 쓰지 않는다.
	 *
	 * @param object $order
	 * @param string $state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		return [
			'accounts' => $this->accounts(),
			'due_date' => $this->calculateDueDate(),
			'due_days' => $this->dueDays(),
			'amount' => (int)$order->amount,
		];
	}

	/**
	 * 입금 예정 등록.
	 *
	 * 돈은 아직 오지 않았다. 여기서 하는 일은 "누가 어느 계좌로 언제까지 넣기로 했는지" 를
	 * 붙잡아 두는 것뿐이다. 실제 승인은 관리자가 입금을 확인했을 때 일어난다.
	 *
	 * @param object $order
	 * @param array $params
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		$accounts = $this->accounts();
		if (!count($accounts))
		{
			return Result::fail(lang('zittme_pay.msg_no_bank_account'));
		}

		// 관리자가 여러 계좌를 등록해 두었으면 사용자가 고른 것을 쓴다.
		$index = (int)($params['bank_index'] ?? 0);
		$account = $accounts[$index] ?? reset($accounts);

		$depositor = trim((string)($params['depositor_name'] ?? ''));
		if ($depositor === '')
		{
			$depositor = (string)$order->payer_name;
		}

		$due_date = $this->calculateDueDate();

		return Result::ok([
			'message' => lang('zittme_pay.msg_bank_registered'),
			'amount' => (int)$order->amount,
			'pay_method' => 'banktransfer',
			'raw' => json_encode(['account' => $account, 'depositor' => $depositor], \JSON_UNESCAPED_UNICODE),
			'extra' => [
				'bank' => $account['bank'] ?? '',
				'account' => $account['account'] ?? '',
				'holder' => $account['holder'] ?? '',
				'depositor_name' => mb_substr($depositor, 0, 80),
				'due_date' => $due_date,
			],
		]);
	}

	/**
	 * 취소.
	 *
	 * 계좌이체는 우리가 자동으로 되돌릴 수단이 없다. 실제 환불은 관리자가 손으로 송금한다.
	 * 여기서는 장부만 정리하고, 사람이 해야 할 일이 남았다는 사실을 메시지로 남긴다.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount
	 * @return Result
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$amount = $amount > 0 ? $amount : (int)$order->remain_amount;

		return Result::ok([
			'message' => lang('zittme_pay.msg_bank_manual_refund'),
			'amount' => $amount,
			'tid' => (string)$order->pg_tid,
			'raw' => json_encode([
				'gateway' => 'banktransfer',
				'manual_refund_required' => true,
				'reason' => $reason,
				'amount' => $amount,
			], \JSON_UNESCAPED_UNICODE),
		]);
	}

	/**
	 * 조회할 PG 가 없다.
	 *
	 * 웹훅 재확인용 메서드인데 무통장은 웹훅 자체가 오지 않는다. 혹시라도 불리면
	 * "확인할 수 없음" 으로 답해야지, 성공으로 답하면 검증을 우회하는 구멍이 된다.
	 *
	 * @param string $tid
	 * @return Result
	 */
	public function query(string $tid): Result
	{
		return Result::fail(lang('zittme_pay.msg_query_not_supported'));
	}

	/**
	 * 등록된 입금 계좌 목록.
	 *
	 * @return array
	 */
	public function accounts(): array
	{
		$accounts = $this->config->bank_accounts;
		if (!is_array($accounts))
		{
			return [];
		}

		$result = [];
		foreach ($accounts as $account)
		{
			$account = (array)$account;
			if (trim((string)($account['bank'] ?? '')) === '' || trim((string)($account['account'] ?? '')) === '')
			{
				continue;
			}
			$result[] = [
				'bank' => trim((string)$account['bank']),
				'account' => trim((string)$account['account']),
				'holder' => trim((string)($account['holder'] ?? '')),
			];
		}
		return $result;
	}

	/**
	 * 입금 기한(일).
	 *
	 * @return int
	 */
	public function dueDays(): int
	{
		return max(1, (int)$this->config->bank_due_days);
	}

	/**
	 * 입금 기한 시각 (라이믹스 표준 14자리).
	 *
	 * @return string
	 */
	public function calculateDueDate(): string
	{
		return date('YmdHis', strtotime('+' . $this->dueDays() . ' days'));
	}
}
