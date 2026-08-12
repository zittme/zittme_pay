<?php

namespace Zittme\Modules\Zittme_pay\Controllers;

use Zittme\Framework\Storage;
use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;

/**
 * 설치와 업데이트.
 *
 * 테이블은 schemas/*.xml 을 보고 코어가 만들어 준다. 여기서는 코어가 대신해 주지 않는 것만
 * 한다 — 티켓 디렉터리, 기본 인스턴스, 설정 기본값, 그리고 나중에 추가된 칼럼 붙이기.
 */
class Install extends Base
{
	/**
	 * 최초 스키마 이후에 추가된 칼럼들. [테이블, 칼럼, 타입, 길이]
	 *
	 * 코어는 이미 만들어진 테이블에 스키마 XML 의 새 칼럼을 자동으로 붙여 주지 않는다.
	 * 스키마에 칼럼을 추가할 때는 반드시 이 표에도 같이 적을 것.
	 */
	public const ADDED_COLUMNS = [
		// 0.1.1 — 구매확정과 수동 환불 (docs/PAY-MODULE.md 8항)
		['zittme_pay_order', 'confirm_date', 'char', 14],
		['zittme_pay_order', 'refund_state', 'varchar', 20],
		['zittme_pay_order', 'refund_amount', 'bigint', null],
		['zittme_pay_order', 'refund_date', 'char', 14],
		// 0.1.2 — 출처 모듈의 주문번호 (사용자 대표 번호, 결제번호는 내부용)
		['zittme_pay_order', 'source_code', 'varchar', 80],
	];

	/**
	 * 티켓 저장 위치 (RX_BASEDIR 기준 상대경로).
	 */
	public const TICKET_DIR = 'files/cache/zittme_pay/';

	/**
	 * 최초 설치.
	 */
	public function moduleInstall()
	{
		$this->prepareStorage();
		$this->prepareConfig();
		self::createDefaultInstance();

		return new \BaseObject();
	}

	/**
	 * 업데이트가 필요한가.
	 */
	public function checkUpdate()
	{
		if (!Storage::isDirectory(\RX_BASEDIR . self::TICKET_DIR))
		{
			return true;
		}
		if (!self::getDefaultInstance())
		{
			return true;
		}

		$config = \ModuleModel::getModuleConfig('zittme_pay');
		if (!is_object($config) || !isset($config->enabled))
		{
			return true;
		}

		$oDB = \DB::getInstance();
		foreach (self::ADDED_COLUMNS as [$table, $column])
		{
			if (!$oDB->isColumnExists($table, $column))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * 업데이트 실행.
	 */
	public function moduleUpdate()
	{
		$this->prepareStorage();
		$this->prepareConfig();
		self::createDefaultInstance();

		$oDB = \DB::getInstance();
		foreach (self::ADDED_COLUMNS as [$table, $column, $type, $size])
		{
			if (!$oDB->isColumnExists($table, $column))
			{
				$oDB->addColumn($table, $column, $type, $size);
			}
		}

		return new \BaseObject();
	}

	/**
	 * 캐시 재생성.
	 */
	public function recompileCache()
	{
	}

	/**
	 * 티켓 디렉터리를 만든다.
	 *
	 * 결제 진행 상태가 담기는 곳이라 웹에서 직접 읽히면 안 된다.
	 *
	 * @return void
	 */
	protected function prepareStorage(): void
	{
		$dir = \RX_BASEDIR . self::TICKET_DIR;
		Storage::createDirectory($dir);
		Storage::protectDirectory($dir);
	}

	/**
	 * 설정 기본값을 실제로 저장해 둔다.
	 *
	 * Config 모델이 읽을 때도 기본값을 채우지만, 관리자 화면에서 한 탭만 저장해도
	 * 나머지 값이 사라지지 않도록 최초 1회 통째로 적어 둔다.
	 *
	 * @return void
	 */
	protected function prepareConfig(): void
	{
		$config = \ModuleModel::getModuleConfig('zittme_pay');
		if (!is_object($config))
		{
			$config = new \stdClass;
		}

		$changed = false;
		foreach (ConfigModel::DEFAULTS as $key => $value)
		{
			if (!isset($config->{$key}))
			{
				$config->{$key} = $value;
				$changed = true;
			}
		}

		if ($changed)
		{
			ConfigModel::setConfig($config);
		}
	}

	/**
	 * 기본 인스턴스를 만든다. 이미 있으면 아무것도 하지 않는다.
	 *
	 * 결제 화면은 mid 없이 열리지만, 스킨 설정을 붙일 인스턴스가 하나는 있어야 한다.
	 *
	 * @return void
	 */
	protected static function createDefaultInstance(): void
	{
		if (self::getDefaultInstance())
		{
			return;
		}

		// 다른 모듈이 이미 그 주소를 쓰고 있으면 비켜 간다.
		$mid = self::DEFAULT_MID;
		if (\ModuleModel::isIDExists($mid))
		{
			$mid = \ModuleModel::getNextAvailableMid($mid) ?: ($mid . '_' . time());
		}

		\ModuleController::getInstance()->insertModule((object)[
			'mid' => $mid,
			'module' => 'zittme_pay',
			'browser_title' => lang('zittme_pay.zittme_pay') ?: 'Zittme Pay',
			'description' => '',
			'layout_srl' => -1,
			'mlayout_srl' => -1,
			'skin' => '/USE_DEFAULT/',
			'mskin' => '/USE_DEFAULT/',
			// 결제 화면은 메뉴에 걸릴 물건이 아니다.
			'isMenuCreate' => false,
		]);

		// 방금 만든 인스턴스를 다음 조회에서 잡을 수 있도록 캐시를 비운다.
		self::$_default_instance = null;
	}
}
