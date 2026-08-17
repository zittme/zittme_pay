<?php

namespace Zittme\Modules\Zittme_pay\Controllers;

use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;

/**
 * 짓미 페이 — 결제 엔진.
 *
 * 결제가 필요한 모듈이 공용으로 쓴다. 요청자(커머스·예약)가 주문을 등록하면 결제창 호출부터
 * 서버 승인, 취소·환불까지 여기서 책임진다. 요청자는 PG 를 전혀 몰라도 된다.
 *
 * 이 모듈은 엔진 기본 제공이 아니라 스토어로 따로 배포하는 부가 모듈이며,
 * 의존 방향은 언제나 한쪽이다: commerce → zittme_pay, reservation → zittme_pay.
 * zittme_pay 는 다른 부가 모듈을 참조하지 않는다.
 */
class Base extends \ModuleObject
{
	/**
	 * 기본 인스턴스의 주소.
	 *
	 * 결제 화면은 mid 없이 열리지만(standalone), 스킨 설정은 모듈 인스턴스에 붙는 값이라
	 * 붙잡아 둘 인스턴스가 하나는 있어야 한다.
	 */
	public const DEFAULT_MID = 'zittme_pay';

	/**
	 * 기본 인스턴스 캐시.
	 *
	 * @var object|false|null
	 */
	protected static $_default_instance = null;

	/**
	 * 공통 초기화.
	 *
	 * 코어의 \ModuleObject 에는 init() 이 없다. 하위 컨트롤러가 parent::init() 을
	 * 부를 수 있도록 빈 구현을 둔다.
	 *
	 * @return void
	 */
	public function init()
	{
	}

	/**
	 * 모듈 설정.
	 *
	 * @return object
	 */
	public static function config(): object
	{
		return ConfigModel::getConfig();
	}

	/**
	 * 이미 만들어진 zittme_pay 인스턴스를 돌려준다.
	 *
	 * 관리자가 주소를 바꿔 두었을 수 있으므로 mid 이름이 아니라 module 종류로 찾는다.
	 *
	 * @return ?object
	 */
	public static function getDefaultInstance(): ?object
	{
		if (self::$_default_instance === null)
		{
			// getMidList 는 결과가 없으면 null, 쿼리가 실패하면 BaseObject 를 돌려준다.
			// 배열일 때만 믿는다.
			$list = \ModuleModel::getMidList((object)['module' => 'zittme_pay']);
			self::$_default_instance = is_array($list) && count($list) ? reset($list) : false;
		}
		return self::$_default_instance ?: null;
	}

	/**
	 * 결제 화면 스킨 이름.
	 *
	 * @return string
	 */
	public static function getSkinName(): string
	{
		$instance = self::getDefaultInstance();
		$skin = (string)($instance->skin ?? '');
		// 기본 스킨 위임이면 사이트 기본 디자인 값을 따른다 (테마 적용이 여길 바꾼다)
		if ($skin === '' || $skin === '/USE_DEFAULT/')
		{
			$skin = (string)(\ModuleModel::getModuleDefaultSkin('zittme_pay', 'P') ?: 'default');
		}
		// 일반 이름과 테마 결합명('테마|@|스킨')만 허용 — 경로 조작 방지
		if (!preg_match('/^[A-Za-z0-9_-]+(\|@\|[A-Za-z0-9_-]+)?$/', $skin))
		{
			$skin = 'default';
		}
		return $skin;
	}

	/**
	 * 결제 화면 스킨 경로 (파일시스템).
	 *
	 * 설정된 스킨 폴더가 없으면 default 로 물러난다. 스킨을 지운 사이트에서
	 * 결제 화면이 통째로 죽는 것보다는 기본 디자인으로라도 뜨는 편이 낫다.
	 *
	 * @return string
	 */
	/**
	 * 결제 화면에 레이아웃을 씌운다.
	 *
	 * 결제 주소에는 mid 가 없어(standalone) 코어가 레이아웃을 붙여 주지 않는다.
	 * 그래서 인스턴스에 설정된 레이아웃 번호를 화면을 그리기 전에 직접 넣어 준다.
	 *
	 * @return void
	 */
	public function applyInstanceLayout(): void
	{
		$instance = self::getDefaultInstance();
		if (!$instance)
		{
			return;
		}

		// -1 은 사이트 기본 레이아웃, -2 는 모바일에서 PC 설정을 따른다는 뜻이다.
		// 코어가 그대로 해석하므로 값을 옮겨 주기만 하면 된다.
		$this->module_info->layout_srl = (int)($instance->layout_srl ?? -1);
		$this->module_info->mlayout_srl = (int)($instance->mlayout_srl ?? -1);
		$this->module_info->site_srl = (int)($instance->site_srl ?? 0);
	}

	public function getSkinPath(): string
	{
		$skin = self::getSkinName();
		$path = \Zittme\Framework\Theme::resolveSkinPath($this->module_path, $skin, 'skins');
		if (!is_dir($path))
		{
			$path = $this->module_path . 'skins/default/';
		}
		return rtrim($path, '/') . '/';
	}

	/**
	 * 지금 시각 (라이믹스 표준 14자리).
	 *
	 * @return string
	 */
	public static function now(): string
	{
		return date('YmdHis');
	}
}
