<?php

namespace Zittme\Modules\Zittme_pay\Controllers;

use Zittme\Framework\Exception;
use Zittme\Framework\Exceptions\TargetNotFound;
use Zittme\Modules\Zittme_pay\Gateways\Base as Gateway;
use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;
use Zittme\Modules\Zittme_pay\Models\Log;
use Zittme\Modules\Zittme_pay\Models\Order;
use Zittme\Modules\Zittme_pay\PayService;

/**
 * 관리자 화면.
 *
 * 탭이 여럿이지만 저장되는 곳은 모듈 설정 하나다. 그래서 탭마다 "그 탭이 책임지는 키" 만
 * 저장한다 — 한 탭을 저장했다고 다른 탭 값이 날아가면 안 된다.
 */
class Admin extends Base
{
	/**
	 * 탭별로 저장을 허용할 설정 키.
	 *
	 * 요청에 실려 온 값을 그대로 설정에 붓지 않는다. 여기 적힌 키만 통과한다.
	 */
	public const TAB_FIELDS = [
		'config' => [
			'enabled', 'test_mode', 'currency', 'extra_currencies', 'order_prefix',
			'allow_partial_cancel', 'cancel_reasons', 'auto_cancel_days', 'allow_force_cancel',
			'notify_admin_email', 'notify_on_paid', 'notify_on_cancel',
			'log_retention_days', 'webhook_ip_whitelist', 'biz_notice',
		],
		'gateway' => [
			'enabled_gateways', 'toss_client_key', 'toss_secret_key',
			'inicis_mid', 'inicis_sign_key', 'inicis_api_key',
			'kcp_site_cd', 'kcp_cert_info', 'kcp_priv_key', 'kcp_priv_pass',
			'nicepay_client_id', 'nicepay_secret_key',
			'portone_store_id', 'portone_channel_key', 'portone_api_secret',
			'paypal_client_id', 'paypal_secret', 'paypal_currency', 'paypal_exchange_rate', 'paypal_allow_krw',
			'conekta_private_key', 'conekta_webhook_secret', 'conekta_currency', 'conekta_methods', 'conekta_allow_krw',
			'exchange_rates', 'exchange_rates_manual', 'exchange_auto', 'exchange_source', 'exchange_api_key',
			'bank_accounts', 'bank_due_days',
		],
	];

	/**
	 * Y/N 로만 저장할 키.
	 */
	protected const BOOLEAN_FIELDS = [
		'enabled', 'test_mode', 'allow_partial_cancel', 'notify_on_paid', 'notify_on_cancel',
		'allow_force_cancel', 'exchange_auto', 'paypal_allow_krw', 'conekta_allow_krw',
	];

	/**
	 * 정수로만 저장할 키 → [최솟값, 최댓값].
	 */
	protected const INT_FIELDS = [
		'bank_due_days' => [1, 30],
		'log_retention_days' => [0, 3650],
		'auto_cancel_days' => [0, 365],
	];

	/**
	 * 관리자 화면은 blade 템플릿을 쓴다.
	 */
	public function init()
	{
		parent::init();

		$this->setTemplatePath($this->module_path . 'views/admin/');
	}

	/* ---------------------------------------------------------------------
	 * 화면
	 * ------------------------------------------------------------------- */

	/**
	 * 1. 기본 설정.
	 */
	public function dispZittme_payAdminConfig()
	{
		$this->setCommonContext('config');

		// 스킨 — 커머스·예약 콘솔과 같은 방식. 기본값(/USE_DEFAULT/)이면 사이트 기본 디자인을 따른다.
		$instance = self::getDefaultInstance();
		$module_info = $instance ? \ModuleModel::getModuleInfoByMid($instance->mid) : null;
		\Context::set('zpay_instance', $module_info);
		\Context::set('zpay_skins', \ModuleModel::getSkins(\RX_BASEDIR . 'modules/zittme_pay') ?: []);
		\Context::set('zpay_default_skin', (string)(\ModuleModel::getModuleDefaultSkin('zittme_pay', 'P') ?: 'default'));

		// 레이아웃 — 결제 주소에는 mid 가 없어 코어가 레이아웃을 붙여 주지 않는다.
		// 여기서 고른 값을 화면을 그릴 때 직접 적용한다.
		$layout_model = getModel('layout');
		\Context::set('zpay_layouts', $layout_model->getLayoutList(0, 'P') ?: []);
		\Context::set('zpay_mlayouts', $layout_model->getLayoutList(0, 'M') ?: []);

		$this->setTemplateFile('config');
	}

	/**
	 * 스킨 저장 — 기본 인스턴스(mid)의 skin 갱신.
	 */
	public function procZittme_payAdminUpdateSkin()
	{
		$instance = self::getDefaultInstance();
		$module_info = $instance ? \ModuleModel::getModuleInfoByMid($instance->mid) : null;
		if (!$module_info || empty($module_info->module_srl))
		{
			return new \BaseObject(-1, 'msg_invalid_request');
		}

		$skin = preg_replace('/[^A-Za-z0-9_\-.\/|@]/', '', (string)\Context::get('skin'));
		if ($skin !== '')
		{
			$module_info->skin = $skin;
			// is_skin_fix 가 N 이면 코어가 저장된 스킨을 무시하고 기본 디자인을 따른다
			$module_info->is_skin_fix = ($skin === '/USE_DEFAULT/') ? 'N' : 'Y';
		}

		// 레이아웃 — -1 은 사이트 기본, -2 는 모바일에서 PC 설정을 따름
		$layout_srl = \Context::get('layout_srl');
		if ($layout_srl !== null && $layout_srl !== '')
		{
			$module_info->layout_srl = (int)$layout_srl;
		}
		$mlayout_srl = \Context::get('mlayout_srl');
		if ($mlayout_srl !== null && $mlayout_srl !== '')
		{
			$module_info->mlayout_srl = (int)$mlayout_srl;
		}

		$module_info->isMenuCreate = false;

		$output = \ModuleController::getInstance()->updateModule($module_info);
		if (!$output->toBool())
		{
			return $output;
		}
		$this->setRedirectUrl(\Context::get('success_return_url') ?: getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminConfig'));
	}

	/**
	 * 2. 결제수단 — 드라이버 활성화와 키 입력.
	 */
	public function dispZittme_payAdminGateway()
	{
		$config = $this->setCommonContext('gateway');

		// 어느 드라이버가 설정을 마쳤는지 화면에 표시해 준다.
		$drivers = [];
		foreach (Gateway::$supported_gateways as $name)
		{
			$driver = Gateway::getDriver($name);
			if (!$driver)
			{
				continue;
			}
			$drivers[$name] = [
				'name' => $name,
				'title' => $driver->getTitle(),
				'configured' => $driver->isConfigured(),
				'enabled' => in_array($name, $config->enabled_gateways, true),
			];
		}

		\Context::set('drivers', $drivers);
		// 결제 통화 목록은 드라이버가 들고 있는 값을 그대로 쓴다 (화면에 따로 적어 두면 어긋난다)
		\Context::set('paypal_currencies', \Zittme\Modules\Zittme_pay\Gateways\Drivers\Paypal::currencyChoices());
		\Context::set('webhook_url', Gateway::buildActionUrl('procZittme_payWebhook'));
		$this->setTemplateFile('gateway');
	}

	/**
	 * 3. 결제 내역.
	 */
	public function dispZittme_payAdminOrders()
	{
		$this->setCommonContext('orders');

		// 기한이 지난 무통장 주문을 이 참에 정리한다.
		Order::expireOverdue();

		$args = new \stdClass;
		$args->page = (int)\Context::get('page') ?: 1;
		$args->list_count = 20;
		$args->page_count = 10;

		$status = (string)\Context::get('status');
		if ($status !== '')
		{
			$args->status = $status;
		}
		$gateway = (string)\Context::get('gateway');
		if ($gateway !== '')
		{
			$args->gateway = $gateway;
		}

		// 송금 대기 목록. 환불이 누락되지 않으려면 이 목록을 비워 두어야 한다.
		$refund_state = (string)\Context::get('refund_state');
		if ($refund_state === Order::REFUND_PENDING)
		{
			$args->refund_state = $refund_state;
		}

		$search_target = (string)\Context::get('search_target');
		$search_keyword = trim((string)\Context::get('search_keyword'));
		if ($search_keyword !== '' && in_array($search_target, ['order_code', 'payer_name', 'payer_phone', 'payer_email', 'pg_tid', 'title'], true))
		{
			$args->{'s_' . $search_target} = $search_keyword;
		}

		$output = Order::getList($args);

		\Context::set('order_list', $output->data ?: []);
		\Context::set('total_count', $output->total_count);
		\Context::set('total_page', $output->total_page);
		\Context::set('page', $output->page);
		// 템플릿이 직접 그린다. PageHandler 에는 HTML 을 만들어 주는 메서드가 없다.
		\Context::set('page_navigation', $output->page_navigation);
		\Context::set('search_target', $search_target);
		\Context::set('search_keyword', $search_keyword);
		\Context::set('filter_status', $status);
		\Context::set('filter_gateway', $gateway);
		\Context::set('filter_refund_state', $refund_state);

		// 송금 대기 건수는 항상 보여 준다. 0 이 아니면 관리자가 할 일이 남아 있다는 뜻이다.
		$pending = new \stdClass;
		$pending->list_count = 1;
		$pending_output = Order::getManualRefundList($pending);
		\Context::set('pending_refund_count', (int)($pending_output->total_count ?? 0));

		$this->setTemplateFile('orders');
	}

	/**
	 * 4. 결제 상세.
	 */
	public function dispZittme_payAdminOrderView()
	{
		$this->setCommonContext('orders');

		$order = Order::get((int)\Context::get('order_srl'));
		if (!$order)
		{
			throw new TargetNotFound;
		}

		$args = new \stdClass;
		$args->order_srl = (int)$order->order_srl;
		$args->list_count = 100;
		$log_output = Log::getList($args);

		\Context::set('order', $order);
		\Context::set('order_logs', $log_output->data ?: []);
		\Context::set('cancel_reasons', ConfigModel::getCancelReasons());
		// 준비(ready) 단계에서 입금부터 하는 고객도 있으므로 미결제(open) 상태면 모두 허용한다
		\Context::set('can_confirm_deposit', in_array($order->status, Order::OPEN_STATUSES, true));
		\Context::set('can_cancel', in_array($order->status, Order::CANCELLABLE_STATUSES, true));

		// 확정된 건은 기본적으로 잠긴다. 관리자가 강제로만 열 수 있고, 그것도 설정으로 막을 수 있다.
		$config = ConfigModel::getConfig();
		\Context::set('can_force_cancel', $order->status === Order::STATUS_CONFIRMED && $config->allow_force_cancel === 'Y');

		// 취소했지만 아직 송금하지 않은 돈이 있는가.
		\Context::set('needs_manual_refund', !empty($order->needs_manual_refund));

		// 이 결제수단을 PG 로 자동 취소할 수 있는가. 화면에서 미리 알려 준다.
		$driver = Gateway::getDriver((string)$order->gateway);
		\Context::set('supports_auto_cancel', $driver ? $driver->supportsAutoCancel($order) : false);

		$this->setTemplateFile('order_view');
	}

	/**
	 * 5. 통신 로그.
	 */
	public function dispZittme_payAdminLogs()
	{
		$this->setCommonContext('logs');

		$args = new \stdClass;
		$args->page = (int)\Context::get('page') ?: 1;
		$args->list_count = 30;
		$args->page_count = 10;

		$action = (string)\Context::get('log_action');
		if ($action !== '')
		{
			$args->action = $action;
		}
		$result = (string)\Context::get('log_result');
		if ($result === 'S' || $result === 'F')
		{
			$args->result = $result;
		}
		$order_code = trim((string)\Context::get('order_code'));
		if ($order_code !== '')
		{
			$args->order_code = $order_code;
		}

		$output = Log::getList($args);

		\Context::set('log_list', $output->data ?: []);
		\Context::set('total_count', $output->total_count);
		\Context::set('total_page', $output->total_page);
		\Context::set('page', $output->page);
		\Context::set('page_navigation', $output->page_navigation);
		\Context::set('filter_action', $action);
		\Context::set('filter_result', $result);
		\Context::set('filter_order_code', $order_code);

		$this->setTemplateFile('logs');
	}

	/* ---------------------------------------------------------------------
	 * 처리
	 * ------------------------------------------------------------------- */

	/**
	 * 설정 저장.
	 */
	public function procZittme_payAdminInsertConfig()
	{
		$tab = (string)\Context::get('tab');
		if (!isset(self::TAB_FIELDS[$tab]))
		{
			throw new Exception('msg_invalid_request');
		}

		$config = ConfigModel::getConfig();
		$vars = \Context::getRequestVars();

		foreach (self::TAB_FIELDS[$tab] as $key)
		{
			$config->{$key} = $this->normalizeField($key, $vars->{$key} ?? null, $config->{$key}, $vars);
		}

		$output = ConfigModel::setConfig($config);
		if (!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success_updated');
		$this->setRedirectUrl(\Context::get('success_return_url') ?: getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminConfig'));
	}

	/**
	 * 취소·환불.
	 *
	 * 실제 처리는 PayService 가 한다. 관리자 화면이든 요청자 모듈이든 취소는 한 문으로만 들어간다.
	 */
	public function procZittme_payCancel()
	{
		$order_srl = (int)\Context::get('order_srl');
		$reason = trim((string)\Context::get('cancel_reason'));
		$amount = (int)\Context::get('cancel_amount');

		// 확정된 결제를 여는 강제 취소. 관리자 화면에서 명시적으로 체크했을 때만 켜진다.
		$force = (\Context::get('force_cancel') === 'Y');

		$result = PayService::cancel($order_srl, $reason, $amount, $force);
		if (!$result->success)
		{
			throw new Exception($result->message ?: lang('zittme_pay.msg_cancel_failed'));
		}

		$this->setMessage($result->message ?: lang('zittme_pay.msg_cancel_success'));
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrderView', 'order_srl', $order_srl));
	}

	/**
	 * 무통장 입금 확인.
	 *
	 * 사람이 통장을 보고 "들어왔다" 고 눌러 주는 순간이 곧 승인이다.
	 * 그 뒤로는 카드 결제와 똑같이 흐른다 — markPaid 가 트리거까지 책임진다.
	 */
	public function procZittme_payAdminConfirmDeposit()
	{
		$order = Order::get((int)\Context::get('order_srl'));
		if (!$order)
		{
			throw new TargetNotFound;
		}
		// ready(결제창 진입 전)·pending(입금대기) 모두 수동 입금확인을 허용한다
		if (!in_array($order->status, Order::OPEN_STATUSES, true))
		{
			throw new Exception('zittme_pay.msg_not_pending');
		}

		$confirmed = PayService::markPaid($order, [
			'gateway' => $order->gateway ?: 'banktransfer',
		]);

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'gateway' => $order->gateway,
			'action' => 'approve',
			'amount' => (int)$order->amount,
			'response_data' => $confirmed
				? 'deposit confirmed by admin'
				: 'deposit confirm ignored (already settled)',
			'result' => $confirmed ? 'S' : 'F',
		]);

		if (!$confirmed)
		{
			throw new Exception('zittme_pay.msg_already_settled');
		}

		$this->setMessage('zittme_pay.msg_deposit_confirmed');
		// 커머스 콘솔 등 다른 화면에서 부르면 그 화면으로 복귀한다
		$this->setRedirectUrl(\Context::get('success_return_url')
			?: getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrderView', 'order_srl', (int)$order->order_srl));
	}

	/**
	 * 수동 환불 완료 처리.
	 *
	 * 관리자가 실제로 계좌 송금을 마친 뒤 누른다. 돈이 나갔다는 사실을 여기서만 기록하므로,
	 * 실제로 보내지 않고 누르면 장부와 통장이 어긋난다.
	 */
	public function procZittme_payAdminCompleteRefund()
	{
		$order = Order::get((int)\Context::get('order_srl'));
		if (!$order)
		{
			throw new TargetNotFound;
		}
		if (empty($order->needs_manual_refund))
		{
			throw new Exception('zittme_pay.msg_no_pending_refund');
		}

		if (!Order::completeManualRefund((int)$order->order_srl))
		{
			// 다른 관리자가 방금 처리했다.
			throw new Exception('zittme_pay.msg_no_pending_refund');
		}

		Log::add([
			'order_srl' => (int)$order->order_srl,
			'order_code' => $order->order_code,
			'gateway' => $order->gateway,
			'action' => 'refund',
			'amount' => (int)$order->refund_amount,
			'response_data' => 'manual refund marked as sent by admin',
		]);

		$this->setMessage('zittme_pay.msg_refund_completed');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrderView', 'order_srl', (int)$order->order_srl));
	}

	/**
	 * 보관기간이 지난 로그를 지금 지운다.
	 */
	public function procZittme_payAdminPurgeLogs()
	{
		$days = (int)ConfigModel::getConfig()->log_retention_days;
		if ($days <= 0)
		{
			throw new Exception('zittme_pay.msg_log_retention_disabled');
		}

		Log::purgeOlderThan($days);

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminLogs'));
	}

	/**
	 * 페이팔 연결 확인. 결제까지 가지 않고 인증만 해 본다.
	 *
	 * 키가 틀렸는지, 테스트 모드와 어긋났는지, 서버에 못 닿는지를
	 * 설정 화면에서 바로 가려내기 위한 것이다.
	 */
	public function procZittme_payAdminTestPaypal()
	{
		$client_id = trim((string)\Context::get('paypal_client_id'));
		$secret = trim((string)\Context::get('paypal_secret'));
		if ($client_id === '' || $secret === '')
		{
			throw new Exception('zittme_pay.msg_paypal_test_empty');
		}

		$driver = \Zittme\Modules\Zittme_pay\Gateways\Drivers\Paypal::getInstance();
		$error = $driver->checkConnection($client_id, $secret);
		if ($error !== '')
		{
			throw new Exception($error);
		}

		$this->setMessage(sprintf(lang('zittme_pay.msg_paypal_test_ok'), $driver->modeLabel()));
	}

	/**
	 * Conekta 연결 확인. 비밀 키로 주문 목록을 한 건 읽어 본다.
	 */
	public function procZittme_payAdminTestConekta()
	{
		$private_key = trim((string)\Context::get('conekta_private_key'));
		if ($private_key === '')
		{
			throw new Exception('zittme_pay.msg_conekta_test_empty');
		}

		$driver = \Zittme\Modules\Zittme_pay\Gateways\Drivers\Conekta::getInstance();
		$error = $driver->checkConnection($private_key);
		if ($error !== '')
		{
			throw new Exception($error);
		}

		$this->setMessage(sprintf(lang('zittme_pay.msg_conekta_test_ok'), $driver->modeLabel()));
	}

	/* ---------------------------------------------------------------------
	 * 내부
	 * ------------------------------------------------------------------- */

	/**
	 * 모든 관리자 화면이 함께 쓰는 값.
	 *
	 * @param string $tab
	 * @return object
	 */
	protected function setCommonContext(string $tab): object
	{
		$config = ConfigModel::getConfig();

		\Context::set('pay_config', $config);
		\Context::set('pay_tab', $tab);
		\Context::set('pay_instance', self::getDefaultInstance());
		\Context::set('status_labels', self::statusLabels());

		return $config;
	}

	/**
	 * 상태 이름표.
	 *
	 * 템플릿에서 $lang->{'...' . $var} 같은 동적 접근을 하지 않도록 미리 만들어 넘긴다.
	 *
	 * @return array
	 */
	public static function statusLabels(): array
	{
		return [
			Order::STATUS_READY => lang('zittme_pay.zpay_status_ready'),
			Order::STATUS_PENDING => lang('zittme_pay.zpay_status_pending'),
			Order::STATUS_PAID => lang('zittme_pay.zpay_status_paid'),
			Order::STATUS_CANCELLED => lang('zittme_pay.zpay_status_cancelled'),
			Order::STATUS_PARTIAL_CANCELLED => lang('zittme_pay.zpay_status_partial_cancelled'),
			Order::STATUS_FAILED => lang('zittme_pay.zpay_status_failed'),
			Order::STATUS_EXPIRED => lang('zittme_pay.zpay_status_expired'),
			Order::STATUS_CONFIRMED => lang('zittme_pay.zpay_status_confirmed'),
		];
	}

	/**
	 * 설정값 하나를 다듬는다.
	 *
	 * 화면에서 보낸 값을 그대로 믿지 않는다. 체크박스는 아예 오지 않을 수도 있다.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $current 판단할 수 없을 때 그대로 둘 현재 값
	 * @param ?object $vars 전체 요청 변수 (여러 필드를 조합해야 하는 경우)
	 * @return mixed
	 */
	protected function normalizeField(string $key, $value, $current, ?object $vars = null)
	{
		// 체크박스는 꺼져 있으면 아예 전송되지 않는다. 값이 없으면 N 이다.
		if (in_array($key, self::BOOLEAN_FIELDS, true))
		{
			return ($value === 'Y' || $value === 'y' || $value === '1') ? 'Y' : 'N';
		}

		if (isset(self::INT_FIELDS[$key]))
		{
			[$min, $max] = self::INT_FIELDS[$key];
			return min($max, max($min, (int)$value));
		}

		if ($key === 'enabled_gateways')
		{
			$value = is_array($value) ? $value : [];
			// 우리가 아는 드라이버 이름만 남긴다.
			return array_values(array_intersect(Gateway::$supported_gateways, array_map('strval', $value)));
		}

		if ($key === 'bank_accounts')
		{
			return self::collectBankAccounts($vars);
		}

		if ($key === 'conekta_methods')
		{
			$picked = is_array($value) ? array_map('strval', $value) : [];
			return array_values(array_intersect(\Zittme\Modules\Zittme_pay\Gateways\Drivers\Conekta::METHODS, $picked));
		}

		if ($key === 'conekta_currency')
		{
			$currency = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)$value));
			return in_array($currency, \Zittme\Modules\Zittme_pay\Gateways\Drivers\Conekta::currencyChoices(), true) ? $currency : 'MXN';
		}

		if ($key === 'exchange_rates')
		{
			return self::collectExchangeRates($vars)[0];
		}

		if ($key === 'exchange_rates_manual')
		{
			return self::collectExchangeRates($vars)[1];
		}

		if ($key === 'exchange_source')
		{
			return in_array($value, \Zittme\Modules\Zittme_pay\Models\Currency::SOURCES, true) ? $value : 'erapi';
		}

		if ($key === 'currency')
		{
			$currency = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)$value));
			return isset(\Zittme\Modules\Zittme_pay\Models\Currency::MAJOR_CURRENCIES[$currency]) ? $currency : 'KRW';
		}

		if ($key === 'extra_currencies')
		{
			// 대표 통화 안에서만 고른다. 기준 통화는 항상 포함이므로 뺀다.
			$base = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)($vars->currency ?? '')));
			$codes = [];
			foreach ((array)$value as $code)
			{
				$code = strtoupper(trim((string)$code));
				if (isset(\Zittme\Modules\Zittme_pay\Models\Currency::MAJOR_CURRENCIES[$code])
					&& $code !== $base && !in_array($code, $codes, true))
				{
					$codes[] = $code;
				}
			}
			return $codes;
		}

		if ($key === 'order_prefix')
		{
			$prefix = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$value);
			return $prefix !== '' ? substr($prefix, 0, 8) : 'ZP';
		}

		if ($value === null)
		{
			return $current;
		}

		return is_string($value) ? trim($value) : $value;
	}

	/**
	 * 환율 입력을 모은다. 통화·환율·수동고정이 각각 배열로 온다.
	 *
	 * @param ?object $vars
	 * @return array [환율 맵, 수동고정 맵]
	 */
	protected static function collectExchangeRates(?object $vars): array
	{
		if (!$vars)
		{
			return [[], []];
		}

		$codes = is_array($vars->fx_code ?? null) ? $vars->fx_code : [];
		$rates = is_array($vars->fx_rate ?? null) ? $vars->fx_rate : [];
		$manuals = is_array($vars->fx_manual ?? null) ? $vars->fx_manual : [];

		$rate_map = [];
		$manual_map = [];
		foreach ($codes as $index => $code)
		{
			$code = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)$code));
			$rate = (float)str_replace(',', '', (string)($rates[$index] ?? ''));
			if ($code === '' || strlen($code) !== 3 || $rate <= 0)
			{
				continue;
			}
			$rate_map[$code] = round($rate, 4);
			if (($manuals[$index] ?? '') === 'Y')
			{
				$manual_map[$code] = 'Y';
			}
		}
		return [$rate_map, $manual_map];
	}

	/**
	 * 입금 계좌 입력을 모은다.
	 *
	 * 화면에서는 은행·계좌·예금주가 각각 배열로 온다. 셋을 짝지어 한 줄로 만든다.
	 * 은행이나 계좌가 비어 있는 줄은 버린다 — 빈 계좌가 결제 화면에 뜨면 안 된다.
	 *
	 * @param ?object $vars
	 * @return array
	 */
	protected static function collectBankAccounts(?object $vars): array
	{
		if (!$vars)
		{
			return [];
		}

		$banks = is_array($vars->bank_name ?? null) ? $vars->bank_name : [];
		$numbers = is_array($vars->bank_account ?? null) ? $vars->bank_account : [];
		$holders = is_array($vars->bank_holder ?? null) ? $vars->bank_holder : [];
		$extras = is_array($vars->bank_extra ?? null) ? $vars->bank_extra : [];

		$accounts = [];
		foreach ($banks as $index => $bank)
		{
			$bank = trim((string)$bank);
			$number = trim((string)($numbers[$index] ?? ''));
			if ($bank === '' || $number === '')
			{
				continue;
			}
			// 추가 항목은 한 줄에 '이름=값'. 은행 코드, 카드번호 등 계좌번호 외 입금 정보
			$extra = [];
			foreach (preg_split('/\R/', (string)($extras[$index] ?? '')) as $line)
			{
				$pos = strpos($line, '=');
				if ($pos === false)
				{
					continue;
				}
				$label = trim(substr($line, 0, $pos));
				$value = trim(substr($line, $pos + 1));
				if ($label === '' || $value === '')
				{
					continue;
				}
				$extra[] = ['label' => mb_substr($label, 0, 40), 'value' => mb_substr($value, 0, 80)];
			}
			$accounts[] = [
				'bank' => mb_substr($bank, 0, 40),
				'account' => mb_substr($number, 0, 60),
				'holder' => mb_substr(trim((string)($holders[$index] ?? '')), 0, 40),
				'extra' => $extra,
			];
		}
		return $accounts;
	}
}
