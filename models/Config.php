<?php

namespace Zittme\Modules\Zittme_pay\Models;

/**
 * 모듈 설정.
 *
 * 결제 설정은 게시판처럼 인스턴스마다 달라질 물건이 아니다. PG 계약은 사이트 단위로 맺으므로
 * 인스턴스별 extra_vars 가 아니라 모듈 전역 설정 하나에 담고, 관리자 탭들이 그것을 나눠 쓴다.
 */
class Config
{
	/**
	 * 설정 기본값.
	 *
	 * 여기 없는 키를 관리자 화면이나 결제 화면에서 읽으면 최초 설치 직후 undefined 가 된다.
	 * 새 설정을 추가할 때는 반드시 이 표에 기본값을 같이 넣을 것.
	 */
	public const DEFAULTS = [
		// 1. 기본
		'enabled' => 'Y',
		// Y 면 PG 의 테스트 키·테스트 엔드포인트를 쓴다. 실결제가 일어나지 않는다.
		'test_mode' => 'Y',
		'currency' => 'KRW',
		// 주문번호 접두사. PG 관리자에서 우리 주문을 알아보는 용도.
		'order_prefix' => 'ZP',

		// 2. 결제수단
		// 활성화된 드라이버 이름 목록. 여기 없는 드라이버는 결제 화면에 뜨지 않는다.
		'enabled_gateways' => ['banktransfer'],
		'toss_client_key' => '',
		'toss_secret_key' => '',
		// 무통장 입금 계좌 목록. [['bank'=>'국민','account'=>'...','holder'=>'...'], ...]
		'bank_accounts' => [],
		// 입금 기한(일). 지나면 주문을 만료 처리한다.
		'bank_due_days' => 3,

		// 3. 취소·환불
		'allow_partial_cancel' => 'Y',
		// 줄바꿈으로 구분한 취소 사유 목록.
		'cancel_reasons' => '',
		// 결제 후 이 일수가 지나면 PG 자동취소를 시도하지 않고 수동 환불로 넘긴다.
		// 정산이 끝나면 카드 취소가 막히기 때문이다. 0 이면 제한하지 않는다.
		'auto_cancel_days' => 0,
		// 구매확정된 결제를 관리자가 강제로 취소할 수 있게 할지.
		// N 이면 확정 후에는 어떤 경로로도 취소되지 않는다.
		'allow_force_cancel' => 'Y',

		// 4. 알림
		'notify_admin_email' => '',
		'notify_on_paid' => 'N',
		'notify_on_cancel' => 'N',

		// 5. 보안·로그
		// 통신 로그 보관기간(일). 0 이면 지우지 않는다. 분쟁 대응 자료라 넉넉히 잡는다.
		'log_retention_days' => 1095,
		// 웹훅 허용 IP. 비어 있으면 IP 로 막지 않는다 (재조회 검증이 본 방어선이다).
		'webhook_ip_whitelist' => '',

		// 6. 정책 표기 — 결제 화면 하단 고지 문구
		'biz_notice' => '',
	];

	/**
	 * @var ?object
	 */
	protected static $_cache = null;

	/**
	 * 설정을 읽는다. 빠진 키는 기본값으로 채워 돌려준다.
	 *
	 * @return object
	 */
	public static function getConfig(): object
	{
		if (self::$_cache === null)
		{
			$config = \ModuleModel::getModuleConfig('zittme_pay');
			if (!is_object($config))
			{
				$config = new \stdClass;
			}
			foreach (self::DEFAULTS as $key => $value)
			{
				if (!isset($config->{$key}))
				{
					$config->{$key} = $value;
				}
			}

			// 배열로 쓰는 값이 문자열로 저장돼 있으면 이후 foreach 가 죽는다. 여기서 바로잡는다.
			foreach (['enabled_gateways', 'bank_accounts'] as $key)
			{
				if (!is_array($config->{$key}))
				{
					$config->{$key} = [];
				}
			}

			self::$_cache = $config;
		}
		return self::$_cache;
	}

	/**
	 * 설정을 저장한다.
	 *
	 * @param object $config
	 * @return object
	 */
	public static function setConfig(object $config): object
	{
		$output = \ModuleController::getInstance()->insertModuleConfig('zittme_pay', $config);
		if ($output->toBool())
		{
			self::$_cache = null;
		}
		return $output;
	}

	/**
	 * 캐시를 버린다. 설정을 저장한 직후 다시 읽어야 할 때 쓴다.
	 *
	 * @return void
	 */
	public static function clearCache(): void
	{
		self::$_cache = null;
	}

	/**
	 * 취소 사유 목록.
	 *
	 * @return array
	 */
	public static function getCancelReasons(): array
	{
		$raw = (string)self::getConfig()->cancel_reasons;
		return array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $raw)), 'strlen'));
	}

	/**
	 * 웹훅을 보낸 IP 가 허용 대상인가.
	 *
	 * 화이트리스트가 비어 있으면 IP 로 막지 않는다. IP 는 보조 수단일 뿐이고, 진짜 방어선은
	 * "웹훅 본문을 믿지 않고 PG 에 재조회한다" 는 쪽이다. 끝에 * 를 붙여 대역을 쓸 수 있다.
	 *
	 * @param string $ipaddress
	 * @return bool
	 */
	public static function isAllowedWebhookIP(string $ipaddress): bool
	{
		$raw = (string)self::getConfig()->webhook_ip_whitelist;
		$list = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $raw)), 'strlen');
		if (!count($list))
		{
			return true;
		}
		foreach ($list as $pattern)
		{
			if ($pattern === $ipaddress)
			{
				return true;
			}
			if (str_ends_with($pattern, '*') && str_starts_with($ipaddress, substr($pattern, 0, -1)))
			{
				return true;
			}
		}
		return false;
	}
}
