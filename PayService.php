<?php

namespace Zittme\Modules\Zittme_pay;

use Zittme\Modules\Zittme_pay\Gateways\Base as Gateway;
use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;
use Zittme\Modules\Zittme_pay\Models\Log;
use Zittme\Modules\Zittme_pay\Models\Order;
use Zittme\Modules\Zittme_pay\Models\Ticket;

/**
 * 요청자 모듈이 쓰는 공개 API.
 *
 * 커머스·예약은 오직 이 클래스만 부른다. 게이트웨이 드라이버나 zittme_pay 의 테이블을
 * 직접 만지지 않는다. 그래야 PG 가 늘어나도 요청자 코드가 그대로 남는다.
 *
 *   use Zittme\Modules\Zittme_pay\PayService;
 *
 *   $result = PayService::createOrder([
 *       'source_module' => 'commerce',
 *       'source_srl'    => $commerce_order_srl,
 *       'amount'        => 51200,
 *       'title'         => '주문 #20260729-0001',
 *       'payer'         => ['name' => ..., 'phone' => ..., 'email' => ...],
 *       'return_url'    => getNotEncodedFullUrl('', 'act', 'dispCommerceOrderResult'),
 *   ]);
 *   // $result->pay_url 로 보내면 결제가 진행된다.
 *
 * 결제 완료는 되돌려 받지 않고 트리거로 통지한다 (느슨한 결합):
 *   zittme_pay.approved  / zittme_pay.cancelled
 *
 * 주의: 트리거를 받으려면 요청자 모듈의 conf/module.xml 에 eventHandler 를 선언한 뒤
 *    관리자에서 그 모듈을 1회 업데이트해야 한다. XML 선언만으로는 등록되지 않는다.
 */
class PayService
{
	/**
	 * 결제 모듈을 쓸 수 있는 상태인가.
	 *
	 * 부가 모듈이라 아예 설치되지 않았을 수도 있다. 요청자 모듈은 이 값을 보고
	 * 결제 기능만 비활성화하면 된다 — 없다고 해서 죽으면 안 된다.
	 *
	 * 주의: 모듈 폴더 자체가 없으면 이 클래스도 로드되지 않는다. 요청자 모듈은 반드시
	 *    클래스 존재 여부를 먼저 확인할 것:
	 *
	 *      if (class_exists('\Zittme\Modules\Zittme_pay\PayService') && PayService::isAvailable())
	 *
	 * @return bool
	 */
	public static function isAvailable(): bool
	{
		if (!\ModuleModel::getModuleInfoXml('zittme_pay'))
		{
			return false;
		}
		return ConfigModel::getConfig()->enabled === 'Y';
	}

	/**
	 * 결제 주문을 만들고 결제 페이지 주소를 돌려준다.
	 *
	 * @param array $args source_module, source_srl, amount, title, payer, return_url
	 * @return object {success, message, order_srl, order_code, pay_url, status}
	 */
	public static function createOrder(array $args): object
	{
		$result = (object)[
			'success' => false,
			'message' => '',
			'order_srl' => 0,
			'order_code' => '',
			'pay_url' => '',
			'status' => '',
		];

		$source_module = trim((string)($args['source_module'] ?? ''));
		$source_srl = (int)($args['source_srl'] ?? 0);
		$amount = (int)($args['amount'] ?? 0);

		if ($source_module === '' || $source_srl <= 0)
		{
			$result->message = lang('zittme_pay.msg_invalid_source');
			return $result;
		}
		if ($amount < 0)
		{
			$result->message = lang('zittme_pay.msg_invalid_amount');
			return $result;
		}
		if (!self::isAvailable())
		{
			$result->message = lang('zittme_pay.msg_pay_disabled');
			return $result;
		}

		$config = ConfigModel::getConfig();
		$payer = is_array($args['payer'] ?? null) ? $args['payer'] : [];

		$order = new \stdClass;
		$order->order_srl = getNextSequence();
		$order->order_code = Order::generateOrderCode((string)$config->order_prefix);
		$order->source_module = $source_module;
		$order->source_srl = $source_srl;
		$order->member_srl = (int)($args['member_srl'] ?? self::currentMemberSrl());
		$order->payer_name = mb_substr((string)($payer['name'] ?? ''), 0, 80);
		$order->payer_phone = mb_substr((string)($payer['phone'] ?? ''), 0, 40);
		$order->payer_email = mb_substr((string)($payer['email'] ?? ''), 0, 120);
		$order->title = mb_substr((string)($args['title'] ?? ''), 0, 250);
		$order->currency = (string)$config->currency;
		$order->amount = $amount;
		$order->cancelled_amount = 0;
		$order->gateway = '';
		$order->pg_tid = '';
		$order->status = Order::STATUS_READY;
		$order->return_url = self::sanitizeReturnUrl((string)($args['return_url'] ?? ''));
		$order->ipaddress = (string)($_SERVER['REMOTE_ADDR'] ?? '');
		$order->extra_vars = '';
		$order->regdate = date('YmdHis');
		$order->due_date = '';

		try
		{
			Order::insert($order);
		}
		catch (\Throwable $e)
		{
			$result->message = $e->getMessage();
			return $result;
		}

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'action' => 'ready',
			'amount' => $amount,
			'request_data' => [
				'source_module' => $source_module,
				'source_srl' => $source_srl,
				'title' => $order->title,
			],
		]);

		$result->success = true;
		$result->order_srl = (int)$order->order_srl;
		$result->order_code = $order->order_code;

		// 결제할 금액이 없는 주문. 포인트·쿠폰으로 전액이 상계된 경우가 여기 온다.
		// 결제창을 띄울 이유가 없으므로 그 자리에서 승인 처리하고 트리거까지 낸다.
		// 요청자 모듈은 "결제가 필요 없었다" 는 사실을 몰라도 되고, 언제나 트리거만 들으면 된다.
		if ($amount === 0)
		{
			$stored = Order::get((int)$order->order_srl);
			if ($stored && self::markPaid($stored, ['gateway' => 'free']))
			{
				$result->status = Order::STATUS_PAID;
				$result->pay_url = $order->return_url ?: getNotEncodedFullUrl('');
				return $result;
			}
		}

		$result->status = Order::STATUS_READY;
		$result->pay_url = self::getPayUrl($order->order_code);
		return $result;
	}

	/**
	 * 결제 페이지 주소.
	 *
	 * 이 주소에는 mid 가 없다. 그래서 dispZittme_payCheckout 은 standalone="true" 다
	 * (pitfall #52 — standalone 값과 링크의 mid 포함 여부는 항상 짝을 맞춘다).
	 *
	 * @param string $order_code
	 * @return string
	 */
	public static function getPayUrl(string $order_code): string
	{
		return getNotEncodedFullUrl('', 'module', 'zittme_pay', 'act', 'dispZittme_payCheckout', 'order_code', $order_code);
	}

	/**
	 * 요청자가 자기 대상의 결제 상태를 확인한다.
	 *
	 * @param string $source_module
	 * @param int $source_srl
	 * @return ?object
	 */
	public static function getOrderBySource(string $source_module, int $source_srl): ?object
	{
		return Order::getBySource($source_module, $source_srl);
	}

	/**
	 * 결제 주문 1건.
	 *
	 * @param int $order_srl
	 * @return ?object
	 */
	public static function getOrder(int $order_srl): ?object
	{
		return Order::get($order_srl);
	}

	/**
	 * 취소·환불. 부분취소가 가능하다.
	 *
	 * @param int $order_srl
	 * @param string $reason
	 * @param int $amount 0 이면 남은 전액
	 * @return object {success, message, cancelled_amount, status}
	 */
	public static function cancel(int $order_srl, string $reason = '', int $amount = 0, bool $force = false): object
	{
		$result = (object)[
			'success' => false,
			'message' => '',
			'cancelled_amount' => 0,
			'status' => '',
			'manual_refund' => false,
		];

		$order = Order::get($order_srl);
		if (!$order)
		{
			$result->message = lang('zittme_pay.msg_order_not_found');
			return $result;
		}

		$config = ConfigModel::getConfig();

		// 구매확정된 결제는 잠긴다. 확정은 요청자 모듈이 "이 거래는 끝났다" 고 선언한 것이므로,
		// 그 뒤의 취소는 사고이거나 예외 처리다. 관리자가 강제로만 열 수 있다.
		$from_status_list = Order::CANCELLABLE_STATUSES;
		if ($order->status === Order::STATUS_CONFIRMED)
		{
			if (!$force)
			{
				$result->message = lang('zittme_pay.msg_confirmed_not_cancellable');
				return $result;
			}
			if ($config->allow_force_cancel !== 'Y')
			{
				$result->message = lang('zittme_pay.msg_force_cancel_disabled');
				return $result;
			}
			$from_status_list = Order::FORCE_CANCELLABLE_STATUSES;
		}
		elseif (!in_array($order->status, Order::CANCELLABLE_STATUSES, true))
		{
			$result->message = lang('zittme_pay.msg_not_cancellable');
			return $result;
		}

		$remain = (int)$order->remain_amount;
		$cancel_amount = $amount > 0 ? $amount : $remain;
		if ($cancel_amount <= 0 || $cancel_amount > $remain)
		{
			$result->message = lang('zittme_pay.msg_invalid_cancel_amount');
			return $result;
		}

		if ($cancel_amount < $remain && $config->allow_partial_cancel !== 'Y')
		{
			$result->message = lang('zittme_pay.msg_partial_cancel_disabled');
			return $result;
		}

		$driver = Gateway::getDriver((string)$order->gateway);
		if (!$driver)
		{
			$result->message = lang('zittme_pay.msg_gateway_not_found');
			Log::fail([
				'order_srl' => $order_srl,
				'order_code' => $order->order_code,
				'gateway' => $order->gateway,
				'action' => 'cancel',
				'amount' => $cancel_amount,
				'response_data' => $result->message,
			]);
			return $result;
		}

		// 되돌릴 PG 가 있는지 드라이버에게 묻는다.
		// 무통장처럼 애초에 없거나, 카드라도 정산이 끝나 시효가 지났으면 자동 취소가 불가능하다.
		$auto = $driver->supportsAutoCancel($order);

		if ($auto)
		{
			// PG 에 먼저 취소를 건다. PG 가 거절하면 우리 장부도 건드리지 않는다.
			$pg = $driver->cancel($order, $reason, $cancel_amount);

			Log::add([
				'order_srl' => $order_srl,
				'order_code' => $order->order_code,
				'gateway' => $order->gateway,
				'action' => 'cancel',
				'amount' => $cancel_amount,
				'pg_tid' => $pg->tid ?: (string)$order->pg_tid,
				'request_data' => ['reason' => $reason, 'amount' => $cancel_amount],
				'response_data' => $pg->raw,
				'result' => $pg->success ? 'S' : 'F',
			]);

			if (!$pg->success)
			{
				$result->message = $pg->message ?: lang('zittme_pay.msg_cancel_failed');
				return $result;
			}
		}
		else
		{
			// 부를 PG 가 없다. 장부는 정리하되 **돈은 아직 나가지 않았다**.
			// 그 사실을 아래에서 수동 환불 대기로 남긴다.
			Log::add([
				'order_srl' => $order_srl,
				'order_code' => $order->order_code,
				'gateway' => $order->gateway,
				'action' => 'cancel',
				'amount' => $cancel_amount,
				'request_data' => ['reason' => $reason, 'amount' => $cancel_amount],
				'response_data' => 'auto cancel not supported by gateway — queued for manual refund',
			]);
		}

		$extra = Order::mergeExtra($order, [
			'cancel_history' => array_merge(
				(array)($order->extra['cancel_history'] ?? []),
				[['amount' => $cancel_amount, 'reason' => $reason, 'date' => date('YmdHis')]]
			),
		]);

		if (!Order::addCancelledAmount($order, $cancel_amount, $extra, $from_status_list))
		{
			// PG 취소는 됐는데 장부 반영이 실패한 경우. 사람이 봐야 하므로 실패 로그를 남긴다.
			$result->message = lang('zittme_pay.msg_cancel_record_failed');
			Log::fail([
				'order_srl' => $order_srl,
				'order_code' => $order->order_code,
				'gateway' => $order->gateway,
				'action' => 'cancel',
				'amount' => $cancel_amount,
				'response_data' => 'PG cancel succeeded but local ledger update failed',
			]);
			return $result;
		}

		// 자동 취소가 안 되는 결제였다면 "송금해야 할 돈" 으로 남긴다.
		// 이 기록이 없으면 관리자가 송금해야 한다는 사실을 아무도 모르게 되고 환불이 누락된다.
		if (!$auto)
		{
			Order::addManualRefund($order_srl, $cancel_amount);
			$result->manual_refund = true;
		}

		$updated = Order::get($order_srl);
		self::fireTrigger('zittme_pay.cancelled', $updated, [
			'cancelled_amount' => $cancel_amount,
			'manual_refund' => !$auto,
		]);

		$result->success = true;
		$result->cancelled_amount = $cancel_amount;
		$result->status = $updated ? $updated->status : '';
		$result->message = $auto
			? lang('zittme_pay.msg_cancel_success')
			: lang('zittme_pay.msg_cancel_manual_refund_queued');
		return $result;
	}

	/**
	 * 구매확정.
	 *
	 * 확정 조건(배송완료 며칠 후, 이용일 경과 등)은 **요청자 모듈만 안다.** 이 모듈은 그 조건을
	 * 판단하지 않고 통보만 받아 기록한다. 확정된 결제는 이후 취소가 잠긴다.
	 *
	 * @param int $order_srl
	 * @return object {success, message, status}
	 */
	public static function confirm(int $order_srl): object
	{
		$result = (object)['success' => false, 'message' => '', 'status' => ''];

		$order = Order::get($order_srl);
		if (!$order)
		{
			$result->message = lang('zittme_pay.msg_order_not_found');
			return $result;
		}
		if ($order->status === Order::STATUS_CONFIRMED)
		{
			// 이미 확정된 건. 두 번 불러도 탈이 없어야 하므로 성공으로 답한다.
			$result->success = true;
			$result->status = $order->status;
			$result->message = lang('zittme_pay.msg_already_confirmed');
			return $result;
		}
		if (!in_array($order->status, Order::CONFIRMABLE_STATUSES, true))
		{
			$result->message = lang('zittme_pay.msg_not_confirmable');
			return $result;
		}

		if (!Order::confirm($order_srl))
		{
			$result->message = lang('zittme_pay.msg_not_confirmable');
			return $result;
		}

		Log::add([
			'order_srl' => $order_srl,
			'order_code' => $order->order_code,
			'gateway' => $order->gateway,
			'action' => 'confirm',
			'amount' => (int)$order->amount,
			'response_data' => 'purchase confirmed by ' . $order->source_module,
		]);

		$updated = Order::get($order_srl);
		self::fireTrigger('zittme_pay.confirmed', $updated);

		$result->success = true;
		$result->status = $updated ? $updated->status : Order::STATUS_CONFIRMED;
		$result->message = lang('zittme_pay.msg_confirm_success');
		return $result;
	}

	/**
	 * 승인 확정. 콜백·웹훅·무통장 입금확인이 모두 이 문을 통과한다.
	 *
	 * 상태 전이가 원자적이므로, 같은 주문에 승인이 두 번 들어와도 트리거는 한 번만 난다.
	 *
	 * @param object $order
	 * @param array $fields gateway / pg_tid / pay_method / extra_vars
	 * @return bool 이번 호출이 실제로 승인을 확정했는가
	 */
	public static function markPaid(object $order, array $fields = []): bool
	{
		$fields['paid_date'] = date('YmdHis');

		$won = Order::transition((int)$order->order_srl, Order::OPEN_STATUSES, Order::STATUS_PAID, $fields);
		if (!$won)
		{
			// 중복 승인. 이미 처리된 주문이므로 로그만 남기고 조용히 물러난다.
			Log::add([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $fields['gateway'] ?? $order->gateway,
				'action' => 'approve',
				'amount' => (int)$order->amount,
				'response_data' => 'duplicate approval ignored (already settled)',
			]);
			return false;
		}

		$updated = Order::get((int)$order->order_srl);
		self::fireTrigger('zittme_pay.approved', $updated);
		return true;
	}

	/**
	 * 요청자 모듈에게 결제 결과를 알린다.
	 *
	 * 직접 참조 대신 트리거를 쓴다. zittme_pay 는 커머스도 예약도 몰라야 한다.
	 *
	 * @param string $trigger_name
	 * @param ?object $order
	 * @param array $extra
	 * @return void
	 */
	protected static function fireTrigger(string $trigger_name, ?object $order, array $extra = []): void
	{
		if (!$order)
		{
			return;
		}

		$obj = (object)array_merge([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'source_module' => $order->source_module,
			'source_srl' => (int)$order->source_srl,
			'member_srl' => (int)$order->member_srl,
			'amount' => (int)$order->amount,
			'cancelled_amount' => (int)$order->cancelled_amount,
			'gateway' => $order->gateway,
			'pg_tid' => $order->pg_tid,
			'status' => $order->status,
		], $extra);

		// triggerCall 은 참조로 받으므로 변수를 넘겨야 한다.
		\ModuleHandler::triggerCall($trigger_name, 'after', $obj);
	}

	/**
	 * 결제 후 돌아갈 주소를 검증한다.
	 *
	 * 남의 사이트로 보내는 주소가 들어오면 오픈 리다이렉트가 된다. 코어의 URL::isInternalURL 은
	 * 호스트 비교뿐 아니라 스킴 검사(javascript: · data: 차단)와 멀티도메인 사이트까지 함께
	 * 처리하므로, 직접 호스트를 비교하지 않고 이 함수에 맡긴다.
	 *
	 * @param string $url
	 * @return string 통과하지 못하면 빈 문자열
	 */
	public static function sanitizeReturnUrl(string $url): string
	{
		$url = trim($url);
		if ($url === '')
		{
			return '';
		}
		if (!\Zittme\Framework\URL::isInternalURL($url))
		{
			return '';
		}
		return mb_substr($url, 0, 255);
	}

	/**
	 * 로그인한 회원의 member_srl. 비회원이면 0.
	 *
	 * @return int
	 */
	protected static function currentMemberSrl(): int
	{
		$logged_info = \Context::get('logged_info');
		return ($logged_info && !empty($logged_info->member_srl)) ? (int)$logged_info->member_srl : 0;
	}
}
