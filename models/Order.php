<?php

namespace Zittme\Modules\Zittme_pay\Models;

/**
 * 결제 주문.
 *
 * 결제 대상을 source_module + source_srl 로만 가리키므로, 이 모듈은 커머스든 예약이든
 * 요청자의 테이블 구조를 전혀 몰라도 된다.
 *
 * 상태 전이는 반드시 transition() 을 거친다. 직접 update 로 상태를 바꾸면
 * 콜백과 웹훅이 겹쳐 도착했을 때 중복 승인이 난다.
 */
class Order
{
	/** 주문이 만들어졌고 아직 결제수단을 고르지 않았다. */
	public const STATUS_READY = 'ready';
	/** 결제가 진행 중이다. 무통장은 입금 대기, 카드는 승인 대기. */
	public const STATUS_PENDING = 'pending';
	/** 승인 완료. 돈을 받았다. */
	public const STATUS_PAID = 'paid';
	public const STATUS_CANCELLED = 'cancelled';
	public const STATUS_PARTIAL_CANCELLED = 'partial_cancelled';
	public const STATUS_FAILED = 'failed';
	public const STATUS_EXPIRED = 'expired';
	/**
	 * 구매확정. 요청자 모듈이 "확정됐다" 고 알려 준 상태다.
	 *
	 * 확정 조건(배송완료 며칠 후, 이용일 경과 등)은 요청자 모듈만 안다. 이 모듈은 그 조건을
	 * 판단하지 않고 통보만 받는다. 대신 확정된 결제는 취소가 잠긴다.
	 */
	public const STATUS_CONFIRMED = 'confirmed';

	/**
	 * 아직 돈이 오가지 않아 손대도 되는 상태.
	 */
	public const OPEN_STATUSES = [self::STATUS_READY, self::STATUS_PENDING];

	/**
	 * 취소·환불이 가능한 상태.
	 */
	public const CANCELLABLE_STATUSES = [self::STATUS_PAID, self::STATUS_PARTIAL_CANCELLED];

	/**
	 * 구매확정을 걸 수 있는 상태.
	 */
	public const CONFIRMABLE_STATUSES = [self::STATUS_PAID, self::STATUS_PARTIAL_CANCELLED];

	/**
	 * 관리자가 강제로 취소할 때 허용되는 상태. 확정된 건이 여기 포함된다.
	 */
	public const FORCE_CANCELLABLE_STATUSES = [
		self::STATUS_PAID,
		self::STATUS_PARTIAL_CANCELLED,
		self::STATUS_CONFIRMED,
	];

	/** 수동 환불이 필요 없는 상태. */
	public const REFUND_NONE = '';
	/** 돌려주기로 했지만 아직 송금하지 않음. */
	public const REFUND_PENDING = 'pending';
	/** 송금 완료. */
	public const REFUND_DONE = 'done';

	/**
	 * 주문 1건.
	 *
	 * @param int $order_srl
	 * @return ?object
	 */
	public static function get(int $order_srl): ?object
	{
		if ($order_srl <= 0)
		{
			return null;
		}
		$output = executeQuery('zittme_pay.getOrder', (object)['order_srl' => $order_srl]);
		return ($output->toBool() && $output->data) ? self::decorate($output->data) : null;
	}

	/**
	 * 주문번호로 찾는다. 결제 화면과 PG 콜백이 쓰는 통로다.
	 *
	 * @param string $order_code
	 * @return ?object
	 */
	public static function getByCode(string $order_code): ?object
	{
		if ($order_code === '')
		{
			return null;
		}
		$output = executeQuery('zittme_pay.getOrderByCode', (object)['order_code' => $order_code]);
		return ($output->toBool() && $output->data) ? self::decorate($output->data) : null;
	}

	/**
	 * 요청자 모듈이 자기 대상의 결제를 찾는다.
	 *
	 * 재시도로 결제 주문이 여러 건 생겼을 수 있어 가장 최근 것을 준다.
	 *
	 * @param string $source_module
	 * @param int $source_srl
	 * @param array $status_list 특정 상태만 볼 때
	 * @return ?object
	 */
	public static function getBySource(string $source_module, int $source_srl, array $status_list = []): ?object
	{
		if ($source_module === '' || $source_srl <= 0)
		{
			return null;
		}

		$args = new \stdClass;
		$args->source_module = $source_module;
		$args->source_srl = $source_srl;
		$args->list_count = 1;
		if (count($status_list))
		{
			$args->status_list = $status_list;
		}

		$output = executeQueryArray('zittme_pay.getOrderBySource', $args);
		if (!$output->toBool() || !is_array($output->data) || !count($output->data))
		{
			return null;
		}
		return self::decorate(reset($output->data));
	}

	/**
	 * 관리자 목록.
	 *
	 * @param object $args
	 * @return object
	 */
	public static function getList(object $args): object
	{
		return executeQueryArray('zittme_pay.getOrderList', $args);
	}

	/**
	 * 주문을 만든다.
	 *
	 * @param object $args
	 * @return object 만들어진 주문
	 */
	public static function insert(object $args): object
	{
		$args->order_srl = $args->order_srl ?? getNextSequence();
		$args->regdate = $args->regdate ?? date('YmdHis');
		$args->status = $args->status ?? self::STATUS_READY;

		$output = executeQuery('zittme_pay.insertOrder', $args);
		if (!$output->toBool())
		{
			throw new \Zittme\Framework\Exception($output->getMessage());
		}
		return $args;
	}

	/**
	 * 상태와 무관한 값을 바꾼다. (결제수단 선택, PG 거래번호 기록 등)
	 *
	 * 상태는 여기서 바꾸지 않는다 — transition() 을 쓸 것.
	 *
	 * @param int $order_srl
	 * @param array $fields
	 * @return bool
	 */
	public static function update(int $order_srl, array $fields): bool
	{
		if ($order_srl <= 0 || !count($fields))
		{
			return false;
		}
		$args = (object)$fields;
		$args->order_srl = $order_srl;
		return executeQuery('zittme_pay.updateOrder', $args)->toBool();
	}

	/**
	 * ★ 상태 전이. 멱등성의 핵심.
	 *
	 * "지금 상태가 $from_status_list 중 하나일 때만" $to_status 로 옮긴다. 검사와 변경이
	 * 한 SQL 안에서 원자적으로 일어나므로, 콜백과 웹훅이 동시에 들어와도 한쪽만 이긴다.
	 *
	 * @param int $order_srl
	 * @param array $from_status_list 이 상태들에서만 옮긴다
	 * @param string $to_status
	 * @param array $fields 함께 기록할 값 (paid_date, pg_tid …)
	 * @return bool 내가 실제로 상태를 바꿨으면 true, 이미 남이 처리했으면 false
	 */
	public static function transition(int $order_srl, array $from_status_list, string $to_status, array $fields = []): bool
	{
		if ($order_srl <= 0 || !count($from_status_list) || $to_status === '')
		{
			return false;
		}

		$args = (object)$fields;
		$args->order_srl = $order_srl;
		$args->from_status_list = $from_status_list;
		$args->status = $to_status;

		$output = executeQuery('zittme_pay.updateOrderStatusIf', $args);
		if (!$output->toBool())
		{
			return false;
		}

		// 실제로 한 줄이 바뀌었을 때만 "내가 이겼다". 0 이면 이미 다른 요청이 처리한 것이다.
		return \DB::getInstance()->getAffectedRows() > 0;
	}

	/**
	 * 취소 금액을 누적한다.
	 *
	 * 더하기를 DB 안에서 하고(operation="plus"), 남은 금액을 넘는 취소는 조건에서 걸러
	 * affected_rows 0 으로 떨어뜨린다. 초과 환불이 원천적으로 불가능하다.
	 *
	 * @param object $order
	 * @param int $cancel_amount 이번에 취소할 금액
	 * @param array $extra 함께 저장할 extra_vars
	 * @param array $from_status_list 이 상태들에서만 취소를 허용한다 (관리자 강제 취소는 확정 건까지 포함)
	 * @return bool 실제로 반영됐는가
	 */
	public static function addCancelledAmount(object $order, int $cancel_amount, array $extra = [], array $from_status_list = []): bool
	{
		$order_srl = (int)$order->order_srl;
		$amount = (int)$order->amount;
		if ($order_srl <= 0 || $cancel_amount <= 0 || $cancel_amount > $amount)
		{
			return false;
		}

		// 이번 취소를 더했을 때 총액을 넘지 않아야 한다.
		$max_cancellable = $amount - $cancel_amount;
		$already = (int)$order->cancelled_amount;
		$to_status = (($already + $cancel_amount) >= $amount) ? self::STATUS_CANCELLED : self::STATUS_PARTIAL_CANCELLED;

		$args = new \stdClass;
		$args->order_srl = $order_srl;
		$args->cancel_amount = $cancel_amount;
		$args->max_cancellable = $max_cancellable;
		$args->from_status_list = count($from_status_list) ? $from_status_list : self::CANCELLABLE_STATUSES;
		$args->status = $to_status;
		$args->cancelled_date = date('YmdHis');
		if (count($extra))
		{
			$args->extra_vars = json_encode($extra, \JSON_UNESCAPED_UNICODE);
		}

		$output = executeQuery('zittme_pay.addCancelledAmount', $args);
		if (!$output->toBool())
		{
			return false;
		}
		if (\DB::getInstance()->getAffectedRows() < 1)
		{
			return false;
		}

		// 위에서 정한 $to_status 는 "읽어둔" 취소액으로 계산한 값이라, 부분취소 두 건이
		// 동시에 들어오면 둘 다 "아직 남았다" 고 판단해 전액이 나갔는데도 partial_cancelled
		// 로 남는다. 금액은 DB 안에서 더하고 가드 조건이 있어 정확하지만 상태만 어긋난다.
		//
		// 그래서 반영이 끝난 뒤 실제 값을 다시 읽어 확정한다. 이 시점의 cancelled_amount 는
		// 이미 원자적으로 더해진 결과이므로 더 이상 경합하지 않는다.
		$fresh = self::get($order_srl);
		if ($fresh && (int)$fresh->cancelled_amount >= (int)$fresh->amount)
		{
			self::transition($order_srl, [self::STATUS_PARTIAL_CANCELLED], self::STATUS_CANCELLED, [
				'cancelled_date' => date('YmdHis'),
			]);
		}

		return true;
	}

	/**
	 * 구매확정.
	 *
	 * 확정 조건은 요청자 모듈만 안다. 여기서는 판단하지 않고 기록만 한다.
	 *
	 * @param int $order_srl
	 * @return bool 이번 호출이 실제로 확정했는가 (이미 확정돼 있으면 false)
	 */
	public static function confirm(int $order_srl): bool
	{
		return self::transition($order_srl, self::CONFIRMABLE_STATUSES, self::STATUS_CONFIRMED, [
			'confirm_date' => date('YmdHis'),
		]);
	}

	/**
	 * 수동 환불 대기로 올린다.
	 *
	 * PG 로 자동 취소할 수 없는 결제(무통장, 정산이 끝난 카드 등)를 취소했을 때 쓴다.
	 * 장부상으로는 취소됐지만 **돈은 아직 나가지 않았다**는 사실을 남기는 것이 목적이다.
	 * 이 기록이 없으면 관리자가 송금해야 한다는 것을 아무도 모르게 되고, 환불이 조용히 누락된다.
	 *
	 * 부분취소가 여러 번이면 금액이 누적된다. 그래서 PHP 가 아니라 DB 안에서 더한다.
	 *
	 * @param int $order_srl
	 * @param int $amount 이번에 송금해야 할 금액
	 * @return bool
	 */
	public static function addManualRefund(int $order_srl, int $amount): bool
	{
		if ($order_srl <= 0 || $amount <= 0)
		{
			return false;
		}

		$args = new \stdClass;
		$args->order_srl = $order_srl;
		$args->refund_amount = $amount;
		$args->refund_state = self::REFUND_PENDING;

		return executeQuery('zittme_pay.addManualRefund', $args)->toBool();
	}

	/**
	 * 수동 환불 완료 처리. 관리자가 실제로 송금한 뒤 누른다.
	 *
	 * @param int $order_srl
	 * @return bool 이번 호출이 실제로 완료 처리했는가
	 */
	public static function completeManualRefund(int $order_srl): bool
	{
		if ($order_srl <= 0)
		{
			return false;
		}

		$args = new \stdClass;
		$args->order_srl = $order_srl;
		$args->refund_state = self::REFUND_DONE;
		$args->from_refund_state = self::REFUND_PENDING;
		$args->refund_date = date('YmdHis');

		$output = executeQuery('zittme_pay.completeManualRefund', $args);
		if (!$output->toBool())
		{
			return false;
		}
		return \DB::getInstance()->getAffectedRows() > 0;
	}

	/**
	 * 송금 대기 중인 결제 목록.
	 *
	 * @param object $args
	 * @return object
	 */
	public static function getManualRefundList(object $args): object
	{
		$args->refund_state = self::REFUND_PENDING;
		return executeQueryArray('zittme_pay.getOrderList', $args);
	}

	/**
	 * 입금 기한이 지난 무통장 주문을 만료시킨다.
	 *
	 * cron 없이 조회 경로에서 게으르게 돈다 (완전 설치형 원칙).
	 *
	 * @return int 만료 처리된 건수
	 */
	public static function expireOverdue(): int
	{
		$args = new \stdClass;
		$args->status_list = [self::STATUS_PENDING];
		$args->due_floor = '00000000000000';
		$args->now = date('YmdHis');
		$args->list_count = 50;

		$output = executeQueryArray('zittme_pay.getExpiredOrders', $args);
		if (!$output->toBool() || !is_array($output->data))
		{
			return 0;
		}

		$count = 0;
		foreach ($output->data as $order)
		{
			if (self::transition((int)$order->order_srl, [self::STATUS_PENDING], self::STATUS_EXPIRED))
			{
				$count++;
				Log::add([
					'order_srl' => (int)$order->order_srl,
					'order_code' => $order->order_code,
					'gateway' => $order->gateway,
					'action' => 'expire',
					'amount' => (int)$order->amount,
					'response_data' => ['due_date' => $order->due_date],
				]);
			}
		}
		return $count;
	}

	/**
	 * 새 주문번호를 만든다.
	 *
	 * 시각 + 난수. 시각만 쓰면 같은 초에 들어온 주문끼리 부딪히고, 난수만 쓰면
	 * PG 관리자에서 언제 결제인지 알아보기 어렵다.
	 *
	 * @param string $prefix
	 * @return string
	 */
	public static function generateOrderCode(string $prefix = 'ZP'): string
	{
		$prefix = preg_replace('/[^A-Za-z0-9_-]/', '', $prefix);
		$prefix = $prefix === '' ? 'ZP' : substr($prefix, 0, 8);
		return $prefix . date('YmdHis') . \Zittme\Framework\Security::getRandom(10, 'alnum');
	}

	/**
	 * 조회 결과에 계산된 값을 붙인다.
	 *
	 * extra_vars 는 JSON 이라 그대로 두면 템플릿에서 쓰기 어렵다.
	 *
	 * @param object $order
	 * @return object
	 */
	protected static function decorate(object $order): object
	{
		$extra = json_decode((string)$order->extra_vars, true);
		$order->extra = is_array($extra) ? $extra : [];
		$order->remain_amount = max(0, (int)$order->amount - (int)$order->cancelled_amount);
		$order->is_paid = in_array($order->status, [self::STATUS_PAID, self::STATUS_PARTIAL_CANCELLED, self::STATUS_CONFIRMED], true);
		$order->is_open = in_array($order->status, self::OPEN_STATUSES, true);

		// 구매확정 여부는 상태가 아니라 확정 시각으로 판단한다.
		// 확정된 뒤 관리자가 강제 취소하면 상태는 cancelled 로 바뀌지만,
		// "확정됐던 건" 이라는 사실 자체는 남아 있어야 한다.
		$order->is_confirmed = !empty($order->confirm_date);

		// 돌려주기로 했는데 아직 송금하지 않은 건.
		$order->needs_manual_refund = ($order->refund_state === self::REFUND_PENDING);

		return $order;
	}

	/**
	 * extra_vars 에 값을 합쳐 저장한다.
	 *
	 * @param object $order
	 * @param array $values
	 * @return array 합쳐진 결과
	 */
	public static function mergeExtra(object $order, array $values): array
	{
		$extra = is_array($order->extra ?? null) ? $order->extra : [];
		return array_merge($extra, $values);
	}
}
