<?php

namespace Zittme\Modules\Zittme_pay\Controllers;

use Zittme\Framework\Exceptions\TargetNotFound;
use Zittme\Modules\Zittme_pay\Gateways\Base as Gateway;
use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;
use Zittme\Modules\Zittme_pay\Models\Currency;
use Zittme\Modules\Zittme_pay\Models\Log;
use Zittme\Modules\Zittme_pay\Models\Order;
use Zittme\Modules\Zittme_pay\Models\Ticket;
use Zittme\Modules\Zittme_pay\PayService;

/**
 * 결제 진행 — 결제 화면, 결제 시작, PG 콜백, 웹훅, 결과 화면.
 *
 * 이 파일에서 가장 중요한 규칙
 *
 * procZittme_payCallback 과 procZittme_payWebhook 은 "세션 금지 구역" 이다.
 * PG 는 이 두 액션을 크로스사이트로 호출하므로 SameSite 정책상 세션 쿠키가 함께 오지 않는다.
 * 여기서 세션을 읽거나 쓰면 PHP 가 새 세션을 발급하고, 그 Set-Cookie 가 브라우저의 기존
 * 세션을 갈아치워 결제를 시작했던 창의 CSRF 토큰이 전부 무효화된다.
 * ("보안정책상 허용되지 않습니다 / ERR_CSRF_CHECK_FAILED")
 *
 * 그래서 두 액션은 $_SESSION 을 건드리지 않고, 상관관계와 결과 전달을 파일 티켓으로만 한다.
 * Context::get() 으로 요청 변수를 읽는 것은 안전하지만, 로그인 정보를 조회하거나
 * 세션에 무언가를 저장하는 코드를 이 아래에 추가하지 말 것.
 *
 * 주의: 같은 이유로, zittme_pay.approved 트리거를 받는 요청자 모듈의 핸들러도 세션을 건드리면
 *    안 된다. 그 핸들러는 콜백 요청 안에서 함께 실행된다.
 */
class Pay extends Base
{
	/**
	 * 결제 화면.
	 *
	 * 요청자 모듈이 발급한 결제 URL 로 바로 열린다. mid 가 없으므로 이 액션은 standalone 이다.
	 */
	public function dispZittme_payCheckout()
	{
		$config = self::config();
		if ($config->enabled !== 'Y')
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_pay_disabled');
		}

		// 기한이 지난 무통장 주문을 이 참에 정리한다 (cron 없이 조회 경로에서 처리).
		Order::expireOverdue();

		// 환율 자동 갱신도 같은 방식으로 하루 1회 여기서 돈다.
		\Zittme\Modules\Zittme_pay\Models\Currency::refreshIfStale();

		$order = Order::getByCode((string)\Context::get('order_code'));
		if (!$order)
		{
			throw new TargetNotFound;
		}

		// 입금 대기(무통장) 주문을 다시 열면 결제수단 화면이 아니라 입금 안내(결과 화면)로 보낸다.
		if ($order->status === Order::STATUS_PENDING)
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'zittme_pay', 'act', 'dispZittme_payResult', 'order_code', $order->order_code));
			return;
		}

		// 이미 끝난 결제를 다시 열면 결과 화면으로 보낸다.
		if (!$order->is_open)
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'zittme_pay', 'act', 'dispZittme_payResult', 'order_code', $order->order_code));
			return;
		}

		$drivers = Gateway::getEnabledDrivers();
		if (!count($drivers))
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_no_gateway_available');
		}

		// 같은 브라우저가 같은 주문을 다시 열었을 뿐이면 기존 티켓을 그대로 쓴다.
		$state = Ticket::issueFor((int)$order->order_srl);

		$gateway_info = [];
		$client_scripts = [];
		foreach ($drivers as $name => $driver)
		{
			// 주문 통화를 처리 못 하는 결제수단은 화면에 올리지 않는다 (외화 주문)
			if (!$driver->supportsCurrency((string)($order->currency ?: 'KRW')))
			{
				continue;
			}
			$gateway_info[$name] = [
				'name' => $name,
				'title' => $driver->getTitle(),
				'requires_client' => $driver->requiresClientPayment(),
				'request' => $driver->buildRequest($order, $state),
			];
			$script = $driver->getClientScript();
			if ($script !== '')
			{
				$client_scripts[$name] = $script;
			}
		}

		\Context::set('order', $order);
		// 금액 문구는 여기서 완성해 넘긴다. 스킨이 모델을 직접 부르면 컴파일 단계에서
		// 네임스페이스 구분자가 유실돼 클래스를 못 찾는 일이 생긴다
		\Context::set('amount_text', Currency::money($order->amount, $order->currency ?: 'KRW'));
		\Context::set('source_order_code', self::sourceOrderCode($order));
		\Context::set('pay_state', $state);
		\Context::set('pay_config', $config);
		\Context::set('gateways', $gateway_info);
		\Context::set('gateway_scripts', $client_scripts);
		\Context::set('pay_boot_json', json_encode([
			'state' => $state,
			'order_code' => $order->order_code,
			'amount' => (int)$order->amount,
			'gateways' => $gateway_info,
		], \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES));

		foreach ($client_scripts as $script)
		{
			\Context::addJsFile($script);
		}
		// 테마 결합명('테마|@|스킨')도 실제 경로로 해석해서 로드한다 (절대경로는 웹 상대경로로 변환)
		$asset_base = './' . ltrim(str_replace(\RX_BASEDIR, '', $this->getSkinPath()), './');
		\Context::addCSSFile($asset_base . 'css/pay.css');
		\Context::addJsFile($asset_base . 'js/pay.js');

		$this->applyInstanceLayout();
		$this->setTemplatePath($this->getSkinPath());
		$this->setTemplateFile('checkout');
	}

	/**
	 * 결제 시작 — 사용자가 결제수단을 골랐다.
	 *
	 * 결제창이 필요한 드라이버는 결제창에 넘길 값을 JSON 으로 돌려주고,
	 * 필요 없는 드라이버(무통장)는 서버에서 그 자리에서 처리한다.
	 * 요청자 모듈은 물론이고 이 컨트롤러조차 결제수단별 분기를 갖지 않는다 —
	 * 차이는 드라이버가 requiresClientPayment() 로 스스로 말한다.
	 */
	public function procZittme_payReady()
	{
		$state = (string)\Context::get('state');
		$ticket = Ticket::read($state);
		if (!$ticket)
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_invalid_ticket');
		}

		$order = Order::get((int)($ticket['order_srl'] ?? 0));
		if (!$order)
		{
			throw new TargetNotFound;
		}
		if (!$order->is_open)
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_already_settled');
		}

		$gateway_name = (string)\Context::get('gateway');
		$drivers = Gateway::getEnabledDrivers();
		if (!isset($drivers[$gateway_name]))
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_gateway_not_found');
		}
		$driver = $drivers[$gateway_name];

		// 고른 결제수단을 주문에 기록해 둔다. 콜백이 어느 드라이버로 승인할지 여기서 정해진다.
		Order::update((int)$order->order_srl, ['gateway' => $gateway_name]);

		if ($driver->requiresClientPayment())
		{
			$this->add('gateway', $gateway_name);
			$this->add('requires_client', true);
			$this->add('request', $driver->buildRequest($order, $state));
			return;
		}

		// PG 페이지로 보내는 결제수단 (페이팔 등). PG 주문을 만들고 승인 페이지로 보낸다.
		if ($driver->requiresRedirect())
		{
			$redirect = $driver->buildRedirect($order, $state);

			Log::add([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $gateway_name,
				'action' => 'ready',
				'amount' => (int)$order->amount,
				'pg_tid' => (string)($redirect['pg_order_id'] ?? ''),
				'response_data' => $redirect['raw'] ?? ($redirect['error'] ?? ($redirect['redirect_url'] ?? '')),
				'result' => empty($redirect['error']) ? 'S' : 'F',
			]);

			if (!empty($redirect['error']) || empty($redirect['redirect_url']))
			{
				throw new \Zittme\Framework\Exception($redirect['error'] ?: lang('zittme_pay.msg_pg_error'));
			}

			$this->add('requires_client', false);
			$this->setRedirectUrl($redirect['redirect_url']);
			return;
		}

		// 서버에서 처리하는 결제수단 (무통장 등).
		$params = [
			'bank_index' => (int)\Context::get('bank_index'),
			'depositor_name' => (string)\Context::get('depositor_name'),
		];

		$result = $driver->approve($order, $params);

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'gateway' => $gateway_name,
			'action' => 'approve',
			'amount' => (int)$order->amount,
			'pg_tid' => $result->tid,
			'request_data' => $params,
			'response_data' => $result->raw ?: $result->message,
			'result' => $result->success ? 'S' : 'F',
		]);

		if (!$result->success)
		{
			throw new \Zittme\Framework\Exception($result->message ?: lang('zittme_pay.msg_approve_failed'));
		}

		$this->settle($order, $driver, $result);

		Ticket::storeResult($state, [
			'success' => true,
			'gateway' => $gateway_name,
			'message' => $result->message,
		]);

		$this->add('requires_client', false);
		// 티켓과 주문번호를 함께 싣는다. 결제 도중 로그인 등으로 세션이 바뀌면
		// 티켓 회수가 실패하는데, 주문번호가 없으면 되돌아갈 길이 없다
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'zittme_pay', 'act', 'dispZittme_payResult',
			'pay_ticket', $state, 'order_code', $order->order_code));
	}

	/**
	 * PG 리턴(콜백).  세션 금지 구역 — 파일 맨 위 주석을 반드시 읽을 것.
	 *
	 * 외부에서 호출되므로 module.xml 에서 check-csrf="false" 로 두었다. 대신 state 티켓과
	 * 금액 대조로 자체 검증한다.
	 */
	public function procZittme_payCallback()
	{
		$state = (string)\Context::get('state');
		$gateway_name = (string)\Context::get('gateway');

		// 세션을 건드리지 않고 티켓만 읽는다.
		$ticket = Ticket::read($state);
		if (!$ticket)
		{
			$this->finishCallback('', '', lang('zittme_pay.msg_invalid_ticket'));
			return;
		}

		$order = Order::get((int)($ticket['order_srl'] ?? 0));
		if (!$order)
		{
			$this->finishCallback($state, '', lang('zittme_pay.msg_order_not_found'));
			return;
		}

		// 사용자가 결제창에서 취소했거나 PG 가 실패로 돌려보낸 경우.
		if (\Context::get('failed') === '1' || \Context::get('code'))
		{
			$message = (string)(\Context::get('message') ?: lang('zittme_pay.msg_payment_cancelled'));
			Log::fail([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $gateway_name,
				'action' => 'approve',
				'amount' => (int)$order->amount,
				'response_data' => ['code' => \Context::get('code'), 'message' => $message],
			]);
			$this->finishCallback($state, $order->return_url, $message);
			return;
		}

		$driver = Gateway::getDriver($gateway_name ?: (string)$order->gateway);
		if (!$driver)
		{
			$this->finishCallback($state, $order->return_url, lang('zittme_pay.msg_gateway_not_found'));
			return;
		}

		$params = self::callbackParams();

		// 보안 3원칙 1 — 금액은 서버 것만 믿는다.
		// 브라우저를 거쳐 온 금액이 주문 금액과 다르면 그 자리에서 끊는다. 승인 요청조차 보내지 않는다.
		$client_amount = (int)($params['amount'] ?? 0);
		if ($client_amount > 0 && $client_amount !== (int)$order->amount)
		{
			Log::fail([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $driver->getName(),
				'action' => 'approve',
				'amount' => (int)$order->amount,
				'request_data' => $params,
				'response_data' => sprintf('amount mismatch: client=%d server=%d', $client_amount, (int)$order->amount),
			]);
			$this->finishCallback($state, $order->return_url, lang('zittme_pay.msg_amount_mismatch'));
			return;
		}

		// 이미 처리된 주문에 콜백이 또 왔다. 중복 승인을 막기 위해 승인 요청을 보내지 않는다.
		if (!$order->is_open)
		{
			Log::add([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $driver->getName(),
				'action' => 'approve',
				'amount' => (int)$order->amount,
				'response_data' => 'duplicate callback ignored (already settled: ' . $order->status . ')',
			]);
			$this->finishCallback($state, $order->return_url, '', true);
			return;
		}

		$result = $driver->approve($order, $params);

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'gateway' => $driver->getName(),
			'action' => 'approve',
			'amount' => (int)$order->amount,
			'pg_tid' => $result->tid,
			'request_data' => $params,
			'response_data' => $result->raw ?: $result->message,
			'result' => $result->success ? 'S' : 'F',
		]);

		if (!$result->success)
		{
			// 승인 실패는 주문을 실패로 내린다. 단, 이미 다른 경로로 확정됐다면 건드리지 않는다.
			Order::transition((int)$order->order_srl, [Order::STATUS_READY, Order::STATUS_PENDING], Order::STATUS_FAILED);
			$this->finishCallback($state, $order->return_url, $result->message ?: lang('zittme_pay.msg_approve_failed'));
			return;
		}

		// 여기서 DB 상태를 확정한다.
		//
		// 문서 초안은 이 일을 "복귀 후 티켓 claim 시점" 으로 미뤄 두었지만, 그러면 사용자가
		// 승인 직후 창을 닫았을 때 돈은 빠져나갔는데 주문은 영영 미결로 남는다.
		// 상태 전이가 원자적이라(updateOrderStatusIf) 여기서 확정해도 중복 승인이 나지 않으므로,
		// "돈을 받은 순간 장부에 적는다" 는 원칙을 지키는 편이 옳다.
		// 세션을 건드리지 않으므로 이 구역의 규칙도 깨지 않는다.
		$this->settle($order, $driver, $result);

		Ticket::storeResult($state, [
			'success' => true,
			'gateway' => $driver->getName(),
			'message' => $result->message,
			'tid' => $result->tid,
		]);

		$this->finishCallback($state, $order->return_url, '', true);
	}

	/**
	 * PG 웹훅.  세션 금지 구역.
	 *
	 * 가상계좌 입금처럼 사용자의 브라우저와 무관하게 나중에 도착하는 통지를 받는다.
	 *
	 * 보안 3원칙 2 — 본문을 믿지 않는다.
	 * 웹훅 본문은 누구나 흉내 낼 수 있다. 그래서 본문의 상태·금액을 그대로 쓰지 않고
	 * 반드시 PG 조회 API 로 다시 물어본 결과만 반영한다.
	 */
	public function procZittme_payWebhook()
	{
		\Context::setResponseMethod('JSON');

		$ipaddress = (string)($_SERVER['REMOTE_ADDR'] ?? '');
		if (!ConfigModel::isAllowedWebhookIP($ipaddress))
		{
			Log::fail([
				'action' => 'webhook',
				'response_data' => 'rejected by IP whitelist: ' . $ipaddress,
			]);
			$this->add('status', 'REJECTED');
			return;
		}

		$raw = (string)file_get_contents('php://input');
		$body = json_decode($raw, true);
		$body = is_array($body) ? $body : [];

		// 토스는 {eventType, data:{...}} 로 감싸서 보낸다. 다른 PG 는 평평하게 보내기도 한다.
		$data = is_array($body['data'] ?? null) ? $body['data'] : $body;
		$order_code = (string)($data['orderId'] ?? $data['order_code'] ?? '');
		$tid = (string)($data['paymentKey'] ?? $data['tid'] ?? '');

		$order = Order::getByCode($order_code);
		if (!$order)
		{
			Log::fail([
				'order_code' => $order_code,
				'action' => 'webhook',
				'request_data' => $raw,
				'response_data' => 'unknown order',
			]);
			// 모르는 주문이라도 200 으로 답해 PG 의 재시도를 멈춘다.
			$this->add('status', 'IGNORED');
			return;
		}

		$driver = Gateway::getDriver((string)$order->gateway);
		if (!$driver)
		{
			Log::fail([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'action' => 'webhook',
				'request_data' => $raw,
				'response_data' => 'driver missing: ' . $order->gateway,
			]);
			$this->add('status', 'IGNORED');
			return;
		}

		// 재조회. 여기서 나온 값만 믿는다.
		$verified = $driver->query($tid ?: (string)$order->pg_tid);

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'gateway' => $driver->getName(),
			'action' => 'webhook',
			'amount' => (int)$order->amount,
			'pg_tid' => $tid,
			'request_data' => $raw,
			'response_data' => $verified->raw ?: $verified->message,
			'result' => $verified->success ? 'S' : 'F',
		]);

		if (!$verified->success)
		{
			$this->add('status', 'IGNORED');
			return;
		}

		// 재조회 금액이 주문 금액과 다르면 위조이거나 사고다. 절대 반영하지 않는다.
		if ((int)$verified->amount !== (int)$order->amount)
		{
			Log::fail([
				'order_srl' => (int)$order->order_srl,
				'order_code' => $order->order_code,
				'gateway' => $driver->getName(),
				'action' => 'webhook',
				'amount' => (int)$order->amount,
				'response_data' => sprintf('amount mismatch: pg=%d server=%d', (int)$verified->amount, (int)$order->amount),
			]);
			$this->add('status', 'REJECTED');
			return;
		}

		$status = $verified->status ?: $driver->getInitialStatus();

		if ($status === Order::STATUS_PAID)
		{
			// 이미 paid 면 markPaid 가 스스로 중복을 걸러 낸다 (로그만 남고 트리거는 나지 않는다).
			$this->settle($order, $driver, $verified);
		}
		elseif (in_array($status, [Order::STATUS_CANCELLED, Order::STATUS_PARTIAL_CANCELLED], true))
		{
			// PG 관리자에서 직접 취소한 경우. 우리 장부를 맞춰 준다.
			Order::transition((int)$order->order_srl, Order::CANCELLABLE_STATUSES, $status, [
				'cancelled_date' => self::now(),
			]);
		}
		elseif (in_array($status, [Order::STATUS_EXPIRED, Order::STATUS_FAILED], true))
		{
			Order::transition((int)$order->order_srl, Order::OPEN_STATUSES, $status);
		}

		$this->add('status', 'OK');
	}

	/**
	 * 결제 결과 화면.
	 *
	 * 여기서 티켓을 회수한다(1회용·지문 대조). 상태 확정은 이미 콜백에서 끝났으므로
	 * 이 화면은 "무엇이 일어났는지 보여 주는" 역할만 한다.
	 */
	public function dispZittme_payResult()
	{
		$state = (string)\Context::get('pay_ticket');
		$claimed = $state !== '' ? Ticket::claim($state) : null;

		$order = null;
		if ($claimed && !empty($claimed['order_srl']))
		{
			$order = Order::get((int)$claimed['order_srl']);
		}
		if (!$order)
		{
			// 티켓 없이 들어온 경우(새로고침, 결제 도중 로그인 등)에는 주문번호로 찾는다.
			$order = Order::getByCode((string)\Context::get('order_code'));

			// 티켓을 거치지 않은 경로다. 회원 주문이면 본인만 볼 수 있게 한다.
			// 비회원 주문은 확인할 것이 주문번호뿐이라 그대로 보여준다.
			if ($order && (int)($order->member_srl ?? 0) > 0)
			{
				$viewer = \Context::get('logged_info');
				$viewer_srl = (int)($viewer->member_srl ?? 0);
				$is_admin = ($viewer->is_admin ?? '') === 'Y';
				if (!$is_admin && $viewer_srl !== (int)$order->member_srl)
				{
					$order = null;
				}
			}
		}
		if (!$order)
		{
			throw new TargetNotFound;
		}

		\Context::set('order', $order);
		// 스킨이 모델을 직접 부르지 않도록 금액 문구를 여기서 완성한다
		$result_currency = $order->currency ?: 'KRW';
		\Context::set('amount_text', Currency::money($order->amount, $result_currency));
		\Context::set('cancelled_amount_text', Currency::money($order->cancelled_amount ?? 0, $result_currency));
		\Context::set('source_order_code', self::sourceOrderCode($order));
		\Context::set('pay_config', self::config());
		\Context::set('claim_result', $claimed);
		\Context::set('claim_message', (string)($claimed['message'] ?? ''));

		// 테마 결합명('테마|@|스킨')도 실제 경로로 해석해서 로드한다 (절대경로는 웹 상대경로로 변환)
		$asset_base = './' . ltrim(str_replace(\RX_BASEDIR, '', $this->getSkinPath()), './');
		\Context::addCSSFile($asset_base . 'css/pay.css');

		$this->applyInstanceLayout();
		$this->setTemplatePath($this->getSkinPath());
		$this->setTemplateFile('result');
	}

	/* ---------------------------------------------------------------------
	 * 내부 헬퍼
	 * ------------------------------------------------------------------- */

	/**
	 * 출처 모듈의 주문번호 — 사용자에게는 이 번호를 대표로 보여준다 (결제번호는 보조).
	 *
	 * @param object $order
	 * @return string
	 */
	protected static function sourceOrderCode(object $order): string
	{
		// 신규 주문은 생성 시 넘겨받은 source_code 를 그대로 쓴다 (모듈 독립적)
		if (!empty($order->source_code))
		{
			return (string)$order->source_code;
		}
		// 과거 주문 호환: 출처 모듈 테이블에서 직접 조회
		$source_srl = (int)($order->source_srl ?? 0);
		$module = (string)($order->source_module ?? '');
		if ($source_srl <= 0 || $module === '')
		{
			return '';
		}
		try
		{
			$prefix = (string)(\Zittme\Framework\Config::get('db.master.prefix') ?? '');
			$handle = \Zittme\Framework\DB::getInstance()->getHandle();
			if ($module === 'commerce')
			{
				$stmt = $handle->prepare('SELECT order_code FROM `' . $prefix . 'commerce_order` WHERE order_srl = ?');
			}
			elseif ($module === 'reservation')
			{
				$stmt = $handle->prepare('SELECT booking_code AS order_code FROM `' . $prefix . 'reservation_booking` WHERE booking_srl = ?');
			}
			else
			{
				return '';
			}
			if ($stmt && $stmt->execute([$source_srl]))
			{
				// 코어는 버퍼링 없는 쿼리를 쓴다. 반환 전에 커서를 닫는다
				$code = (string)($stmt->fetchColumn() ?: '');
				$stmt->closeCursor();
				return $code;
			}
		}
		catch (\Throwable $e)
		{
		}
		return '';
	}

	/**
	 * 승인 결과를 주문에 반영한다.
	 *
	 * 드라이버가 알려 준 상태(카드=paid, 가상계좌=pending)에 따라 갈린다.
	 * paid 로 가는 길은 반드시 PayService::markPaid 를 지나므로, 트리거 발생과
	 * 중복 승인 차단이 한곳에서만 일어난다.
	 *
	 * @param object $order
	 * @param \Zittme\Modules\Zittme_pay\Gateways\Base $driver
	 * @param \Zittme\Modules\Zittme_pay\Gateways\Result $result
	 * @return void
	 */
	protected function settle(object $order, $driver, $result): void
	{
		$status = $result->status ?: $driver->getInitialStatus();

		$fields = [
			'gateway' => $driver->getName(),
			'pg_tid' => $result->tid,
			'pay_method' => $result->pay_method,
		];

		$extra = Order::mergeExtra($order, $result->extra);
		if (count($extra))
		{
			$fields['extra_vars'] = json_encode($extra, \JSON_UNESCAPED_UNICODE);
		}

		// 무통장·가상계좌는 입금 기한이 있다. 만료 정리가 이 값을 본다.
		if (!empty($result->extra['due_date']))
		{
			$fields['due_date'] = (string)$result->extra['due_date'];
		}
		elseif (!empty($result->extra['vbank']['due_date']))
		{
			$fields['due_date'] = date('YmdHis', strtotime((string)$result->extra['vbank']['due_date']));
		}

		if ($status === Order::STATUS_PAID)
		{
			PayService::markPaid($order, $fields);
			return;
		}

		Order::transition((int)$order->order_srl, [Order::STATUS_READY], Order::STATUS_PENDING, $fields);
	}

	/**
	 * 콜백에서 쓸 요청 변수만 추린다.
	 *
	 * $_REQUEST 를 통째로 넘기면 무엇이 승인에 쓰이는지 알 수 없게 된다.
	 *
	 * @return array
	 */
	protected static function callbackParams(): array
	{
		$keys = ['paymentKey', 'orderId', 'amount', 'tid', 'code', 'message', 'pg_token', 'imp_uid', 'merchant_uid', 'token', 'PayerID', 'paymentId', 'authResultCode', 'resultCode', 'authToken', 'authUrl', 'netCancelUrl', 'res_cd', 'res_msg', 'enc_data', 'enc_info', 'tran_cd'];

		$params = [];
		foreach ($keys as $key)
		{
			$value = \Context::get($key);
			if ($value !== null && $value !== '')
			{
				$params[$key] = is_scalar($value) ? (string)$value : '';
			}
		}
		return $params;
	}

	/**
	 * 콜백을 마치고 브라우저를 돌려보낸다.
	 *
	 * 요청자 모듈이 return_url 을 지정했으면 그리로, 아니면 우리 결과 화면으로 보낸다.
	 * 어느 쪽이든 pay_ticket 을 달고 가서 원래 세션에서 회수하게 한다.
	 *
	 * @param string $state
	 * @param string $return_url
	 * @param string $error
	 * @param bool $success
	 * @return void
	 */
	protected function finishCallback(string $state, string $return_url, string $error = '', bool $success = false): void
	{
		if (!$success && $state !== '')
		{
			Ticket::storeResult($state, ['success' => false, 'message' => $error]);
		}

		$return_url = PayService::sanitizeReturnUrl($return_url);
		if ($return_url !== '')
		{
			$separator = (strpos($return_url, '?') === false) ? '?' : '&';
			$this->setRedirectUrl($return_url . $separator . 'pay_ticket=' . rawurlencode($state));
			return;
		}

		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'zittme_pay', 'act', 'dispZittme_payResult', 'pay_ticket', $state));
	}
}
