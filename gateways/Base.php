<?php

namespace Zittme\Modules\Zittme_pay\Gateways;

use Zittme\Modules\Zittme_pay\Models\Config as ConfigModel;

/**
 * 결제 게이트웨이 드라이버 계약.
 *
 * 새 PG 를 붙이는 일은 drivers/ 에 클래스 하나를 추가하고 $supported_gateways 에 이름을
 * 적는 것으로 끝나야 한다. 그 밖의 어떤 파일도 고칠 필요가 없어야 한다는 것이 이 추상화의
 * 존재 이유다. (member/social, member/identity 의 드라이버 구조와 같은 패턴)
 *
 * requiresClientPayment() 가 false 인 드라이버(무통장 등)는 결제창 없이 서버에서 처리된다.
 * 요청자 모듈은 그 차이를 몰라도 된다 — 언제나 결제 URL 로 보내고 트리거를 기다리면 된다.
 */
abstract class Base
{
	/**
	 * 엔진에 들어 있는 드라이버. 새 PG 를 추가하면 여기에도 이름을 적는다.
	 */
	public static array $supported_gateways = [
		'toss',
		'banktransfer',
	];

	/**
	 * @var array<string, Base>
	 */
	protected static array $_instances = [];

	/**
	 * @var object
	 */
	protected $config;

	/**
	 * @return static
	 */
	public static function getInstance()
	{
		$class_name = static::class;
		if (!isset(self::$_instances[$class_name]))
		{
			self::$_instances[$class_name] = new static();
		}
		return self::$_instances[$class_name];
	}

	protected function __construct()
	{
		$this->config = ConfigModel::getConfig();
	}

	/**
	 * 이름으로 드라이버를 얻는다.
	 *
	 * @param ?string $name
	 * @return ?Base 없는 이름이면 null — 호출부는 반드시 null 을 확인할 것
	 */
	public static function getDriver(?string $name): ?Base
	{
		$name = strtolower(trim((string)$name));
		if ($name === '' || !in_array($name, self::$supported_gateways, true))
		{
			return null;
		}

		$class_name = __NAMESPACE__ . '\\Drivers\\' . ucfirst($name);
		if (!class_exists($class_name))
		{
			return null;
		}
		return $class_name::getInstance();
	}

	/**
	 * 결제 화면에 띄울 드라이버 목록.
	 *
	 * 관리자가 켜 두었고(enabled_gateways) 키까지 채워진(isConfigured) 것만 나온다.
	 * 키를 안 넣은 결제수단이 화면에 뜨면 사용자가 결제 도중에 막힌다.
	 *
	 * @return array<string, Base>
	 */
	public static function getEnabledDrivers(): array
	{
		$enabled = ConfigModel::getConfig()->enabled_gateways;
		$enabled = is_array($enabled) ? $enabled : [];

		$result = [];
		foreach (self::$supported_gateways as $name)
		{
			if (!in_array($name, $enabled, true))
			{
				continue;
			}
			$driver = self::getDriver($name);
			if ($driver && $driver->isConfigured())
			{
				$result[$name] = $driver;
			}
		}
		return $result;
	}

	/* ---------------------------------------------------------------------
	 * 드라이버 계약
	 * ------------------------------------------------------------------- */

	/**
	 * 드라이버 이름. 'toss' 처럼 소문자 한 단어.
	 */
	abstract public function getName(): string;

	/**
	 * 이 드라이버를 쓸 수 있을 만큼 설정이 채워졌는가.
	 */
	abstract public function isConfigured(): bool;

	/**
	 * 브라우저 결제창(SDK) 이 필요한가.
	 *
	 * false 면 서버에서 즉시 처리된다 (무통장 등).
	 */
	abstract public function requiresClientPayment(): bool;

	/**
	 * 결제창 SDK 스크립트 주소. 필요 없으면 빈 문자열.
	 */
	public function getClientScript(): string
	{
		return '';
	}

	/**
	 * 승인이 성공했을 때 주문이 도달하는 상태.
	 *
	 * 카드처럼 그 자리에서 돈이 넘어오면 paid 다. 무통장처럼 사람이 입금할 때까지
	 * 기다려야 하는 결제수단은 pending 이고, 실제 입금이 확인된 뒤에야 paid 가 된다.
	 *
	 * 이 구분을 드라이버가 스스로 말해 주기 때문에, 컨트롤러는 결제수단별 분기를 갖지 않는다.
	 *
	 * @return string
	 */
	public function getInitialStatus(): string
	{
		return \Zittme\Modules\Zittme_pay\Models\Order::STATUS_PAID;
	}

	/**
	 * 이 결제를 PG 로 자동 취소할 수 있는가.
	 *
	 * false 면 취소는 그대로 진행하되 **PG 를 부르지 않고** 수동 환불 대기로 넘긴다.
	 * 돈은 관리자가 직접 송금해야 한다.
	 *
	 * 자동 취소가 불가능한 경우는 크게 둘이다.
	 *   1) 애초에 되돌릴 PG 가 없는 결제수단 (무통장입금)
	 *   2) PG 는 있지만 시효가 지난 경우 — 정산이 끝나면 카드 취소 대신 계좌 환불로 가야 한다
	 *
	 * 2번은 PG 마다 기준이 달라 관리자 설정(auto_cancel_days)으로 둔다. 0 이면 제한하지 않는다.
	 *
	 * @param object $order
	 * @return bool
	 */
	public function supportsAutoCancel(object $order): bool
	{
		$days = (int)($this->config->auto_cancel_days ?? 0);
		if ($days <= 0)
		{
			return true;
		}

		$paid_date = (string)($order->paid_date ?? '');
		if ($paid_date === '')
		{
			return true;
		}

		// 라이믹스 표준 14자리를 시각으로 되돌린다. 형식이 깨졌으면 막지 않는다.
		$paid = \DateTime::createFromFormat('YmdHis', $paid_date);
		if (!$paid)
		{
			return true;
		}

		return $paid->getTimestamp() >= (time() - ($days * 86400));
	}

	/**
	 * 결제창을 띄우는 데 필요한 값들.
	 *
	 * $state 는 세션 무의존 티켓의 state 다. PG 에 넘기는 콜백 주소에 실어 보내야
	 * 돌아왔을 때 어느 결제였는지 알아볼 수 있다.
	 *
	 * @param object $order
	 * @param string $state
	 * @return array
	 */
	abstract public function buildRequest(object $order, string $state = ''): array;

	/**
	 * 서버 승인.
	 *
	 * 주의: 구현할 때 반드시 지킬 것: 브라우저가 보낸 금액이 아니라 $order->amount 를 기준으로
	 *    승인한다. 금액 대조는 호출부(Pay 컨트롤러)에서도 하지만, 드라이버가 PG 에 넘기는
	 *    금액 역시 언제나 서버 값이어야 한다.
	 *
	 * @param object $order
	 * @param array $params 콜백으로 받은 값
	 * @return Result
	 */
	abstract public function approve(object $order, array $params): Result;

	/**
	 * (부분)취소.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount 0 이면 전액
	 * @return Result
	 */
	abstract public function cancel(object $order, string $reason, int $amount = 0): Result;

	/**
	 * 단건 조회. 웹훅 본문을 믿지 않고 재확인할 때 쓴다 (보안 3원칙 2).
	 *
	 * @param string $tid
	 * @return Result
	 */
	abstract public function query(string $tid): Result;

	/* ---------------------------------------------------------------------
	 * 공통 헬퍼
	 * ------------------------------------------------------------------- */

	/**
	 * 화면에 보일 결제수단 이름.
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		$title = lang('zittme_pay.gateway_' . $this->getName());
		return ($title && $title !== 'gateway_' . $this->getName()) ? $title : $this->getName();
	}

	/**
	 * PG 서버와 통신한다.
	 *
	 * 코어 HTTP 는 통신 자체가 실패하면 상태코드 0 인 응답 객체를 돌려준다(HTTPHelper).
	 * 예외를 던지지 않으므로, 상태코드 0 을 "PG 에 닿지 못함" 으로 해석해야 한다.
	 *
	 * @param string $url
	 * @param string $method
	 * @param mixed $data
	 * @param array $headers
	 * @return array [성공여부, 상태코드, 본문, 파싱된 배열]
	 */
	protected function request(string $url, string $method = 'POST', $data = null, array $headers = []): array
	{
		$settings = ['timeout' => 30];

		try
		{
			$response = \Zittme\Framework\HTTP::request($url, $method, $data, $headers, [], $settings);
		}
		catch (\Throwable $e)
		{
			return [false, 0, $e->getMessage(), []];
		}

		$status = (int)$response->getStatusCode();
		if ($status === 0)
		{
			// 통신 실패. getReasonPhrase() 에 예외 메시지가 들어 있다.
			return [false, 0, (string)$response->getReasonPhrase(), []];
		}

		$body = (string)$response->getBody();
		$parsed = json_decode($body, true);
		$parsed = is_array($parsed) ? $parsed : [];

		return [$status >= 200 && $status < 300, $status, $body, $parsed];
	}

	/**
	 * 우리 서버의 절대 주소로 된 액션 URL. PG 에 콜백·웹훅 주소로 넘긴다.
	 *
	 * PG 는 외부에서 우리를 호출하므로 상대경로로는 안 되고 스킴부터 필요하다.
	 *
	 * @param string $act
	 * @param array $extra
	 * @return string
	 */
	public static function buildActionUrl(string $act, array $extra = []): string
	{
		$url = \Zittme\Framework\URL::getCurrentDomainURL(\RX_BASEURL)
			. 'index.php?module=zittme_pay&act=' . rawurlencode($act);
		foreach ($extra as $key => $value)
		{
			$url .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$value);
		}
		return $url;
	}
}
