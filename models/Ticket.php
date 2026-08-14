<?php

namespace Zittme\Modules\Zittme_pay\Models;

/**
 * 결제 콜백용 티켓 저장소 — 세션 무의존.
 *
 * 세션을 쓰지 않는 이유
 *
 * PG 는 결제 결과를 브라우저를 통해 우리 사이트로 크로스사이트 POST 한다. 그때 SameSite
 * 정책 때문에 세션 쿠키가 함께 오지 않는다. 콜백 핸들러가 세션을 건드리면 PHP 가 세션이
 * 없다고 판단해 새 세션을 발급하고, 그 Set-Cookie 가 브라우저의 기존 세션을 갈아치운다.
 * 그러면 결제를 시작했던 원래 창의 CSRF 토큰이 죽은 세션 소속이 되어, 이후 어떤 폼을 내도
 * "보안정책상 허용되지 않습니다(ERR_CSRF_CHECK_FAILED)" 가 뜬다.
 *
 * 그래서 콜백은 세션을 절대 읽거나 쓰지 않는다. 대신
 *   1) 결제 시작 시 랜덤 state 를 발급해 서버 파일에 저장한다 (발급 브라우저 지문 포함)
 *   2) 콜백은 state 로 주문을 찾고, 승인 결과를 그 파일에 적기만 한다
 *   3) 브라우저가 ?pay_ticket=... 로 돌아오면 원래 세션에서 1회용으로 claim 한다
 *
 * modules/member/identity/Base.php 의 티켓 패턴과 같다.
 */
class Ticket
{
	/** 발급→콜백, 콜백→claim 각각의 유효시간(초). 결제창 체류가 길 수 있어 넉넉히 잡는다. */
	public const TTL = 1800;

	/** 한 브라우저가 동시에 들고 있을 수 있는 미사용 티켓 수 (남용 방지). */
	public const MAX_PER_ISSUER = 10;

	/**
	 * 티켓 파일이 사는 곳.
	 *
	 * @return string
	 */
	protected static function dir(): string
	{
		return \RX_BASEDIR . 'files/cache/zittme_pay/';
	}

	/**
	 * state 에 해당하는 파일 경로. state 는 16진수만 남겨 경로 조작을 막는다.
	 *
	 * @param string $state
	 * @return string
	 */
	protected static function path(string $state): string
	{
		return self::dir() . preg_replace('/[^a-f0-9]/', '', $state) . '.json';
	}

	/**
	 * 결제를 시작한 브라우저의 지문.
	 *
	 * claim 은 반드시 같은 브라우저에서 와야 한다. Referer·히스토리·프록시 로그로 티켓이
	 * 새어 나가도 남이 대신 받아갈 수 없게 하는 장치다.
	 *
	 * @return string
	 */
	protected static function fingerprint(): string
	{
		return hash('sha256', implode('|', [
			(string)($_SERVER['REMOTE_ADDR'] ?? ''),
			(string)($_SERVER['HTTP_USER_AGENT'] ?? ''),
		]));
	}

	/**
	 * 세션 id 해시. 세션이 없으면 빈 문자열.
	 *
	 * 주의: 콜백 경로에서는 절대 부르지 말 것 — session_id() 호출이 세션을 깨울 수 있다.
	 * 발급(issue)과 회수(claim) 에서만 쓴다. 양쪽 모두에 값이 있을 때만 비교하므로,
	 * 중간에 로그인 등으로 세션이 재발급돼도 정상 사용자가 막히지 않는다.
	 *
	 * @return string
	 */
	protected static function sessionHash(): string
	{
		$sid = function_exists('session_id') ? (string)@session_id() : '';
		return $sid === '' ? '' : hash('sha256', $sid);
	}

	/**
	 * 만료된 티켓 파일을 지운다. 발급·회수 때 함께 도므로 cron 이 필요 없다.
	 *
	 * @return void
	 */
	protected static function purgeExpired(): void
	{
		$dir = self::dir();
		if (!is_dir($dir))
		{
			return;
		}
		$deadline = time() - (self::TTL * 2);
		foreach ((array)glob($dir . '*.json') as $file)
		{
			if (@filemtime($file) < $deadline)
			{
				@unlink($file);
			}
		}
	}

	/**
	 * 결제 시작 — 새 state 를 발급한다.
	 *
	 * @param int $order_srl 이 티켓이 가리키는 주문
	 * @return string state
	 * @throws \Zittme\Framework\Exception 티켓을 너무 많이 들고 있을 때
	 */
	public static function issue(int $order_srl): string
	{
		self::purgeExpired();

		$fingerprint = self::fingerprint();
		$mine = 0;
		foreach ((array)glob(self::dir() . '*.json') as $file)
		{
			$data = json_decode((string)@file_get_contents($file), true);
			if (is_array($data) && ($data['fp'] ?? '') === $fingerprint)
			{
				$mine++;
			}
		}
		if ($mine >= self::MAX_PER_ISSUER)
		{
			throw new \Zittme\Framework\Exception('zittme_pay.msg_too_many_requests');
		}

		$state = \Zittme\Framework\Security::getRandom(32, 'hex');
		\FileHandler::writeFile(self::path($state), json_encode([
			'issued' => time(),
			'order_srl' => $order_srl,
			'fp' => $fingerprint,
			'sid' => self::sessionHash(),
		], \JSON_UNESCAPED_UNICODE));

		return $state;
	}

	/**
	 * 이 주문에 대해 이미 발급해 둔 티켓이 있으면 그것을 쓰고, 없으면 새로 발급한다.
	 *
	 * 결제 화면을 새로고침할 때마다 티켓을 새로 찍으면 남용 방지 한도(MAX_PER_ISSUER)에
	 * 정상 사용자가 먼저 걸린다. 같은 브라우저가 같은 주문을 다시 열었을 뿐이라면
	 * 새 state 를 만들 이유가 없다.
	 *
	 * 이미 승인 결과가 담긴 티켓(result 존재)은 재사용하지 않는다. 결제가 한 번 끝난
	 * state 를 다시 쓰면 결과가 뒤섞인다.
	 *
	 * @param int $order_srl
	 * @return string state
	 */
	public static function issueFor(int $order_srl): string
	{
		$fingerprint = self::fingerprint();
		$now = time();

		foreach ((array)glob(self::dir() . '*.json') as $file)
		{
			$data = json_decode((string)@file_get_contents($file), true);
			if (!is_array($data))
			{
				continue;
			}
			if ((int)($data['order_srl'] ?? 0) !== $order_srl)
			{
				continue;
			}
			if (($data['fp'] ?? '') !== $fingerprint)
			{
				continue;
			}
			if (isset($data['result']))
			{
				continue;
			}
			if (empty($data['issued']) || ($now - $data['issued']) > self::TTL)
			{
				continue;
			}

			$state = basename($file, '.json');
			if (preg_match('/^[a-f0-9]{32}$/', $state))
			{
				return $state;
			}
		}

		return self::issue($order_srl);
	}

	/**
	 * 티켓 내용을 읽는다. 세션을 건드리지 않으므로 콜백에서 써도 안전하다.
	 *
	 * @param string $state
	 * @return ?array 없거나 만료면 null
	 */
	public static function read(string $state): ?array
	{
		if (!preg_match('/^[a-f0-9]{32}$/', $state))
		{
			return null;
		}
		$raw = \FileHandler::readFile(self::path($state));
		if (!$raw)
		{
			return null;
		}
		$data = json_decode($raw, true);
		if (!is_array($data) || empty($data['issued']) || (time() - $data['issued']) > self::TTL)
		{
			return null;
		}
		return $data;
	}

	/**
	 * 콜백이 승인 결과를 티켓에 적는다.
	 *
	 * 발급 때의 지문을 그대로 보존해, 회수 단계에서 같은 브라우저인지 대조할 수 있게 한다.
	 * 여기서는 세션을 절대 건드리지 않는다.
	 *
	 * @param string $state
	 * @param array $result
	 * @return void
	 */
	public static function storeResult(string $state, array $result): void
	{
		$existing = self::read($state);
		if ($existing === null)
		{
			return;
		}

		$existing['result'] = $result;
		$existing['stored_at'] = time();
		\FileHandler::writeFile(self::path($state), json_encode($existing, \JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 원래 세션에서 티켓을 회수한다. 1회용이다.
	 *
	 * @param string $state
	 * @return ?array 승인 결과. 위조·만료·다른 브라우저면 null
	 */
	public static function claim(string $state): ?array
	{
		if (!preg_match('/^[a-f0-9]{32}$/', $state))
		{
			return null;
		}
		self::purgeExpired();

		$path = self::path($state);
		$raw = \FileHandler::readFile($path);

		// 읽는 즉시 지운다. 같은 티켓으로 두 번 성공 화면을 볼 수 없다.
		\FileHandler::removeFile($path);

		if (!$raw)
		{
			return null;
		}
		$data = json_decode($raw, true);
		if (!is_array($data) || empty($data['stored_at']) || (time() - $data['stored_at']) > self::TTL)
		{
			return null;
		}

		// 결제를 시작한 그 브라우저만 회수할 수 있다.
		if (!hash_equals((string)($data['fp'] ?? ''), self::fingerprint()))
		{
			return null;
		}

		// 양쪽 모두 세션이 있을 때만 비교한다 (같은 NAT·같은 UA 공격자 차단).
		$issued_sid = (string)($data['sid'] ?? '');
		$current_sid = self::sessionHash();
		if ($issued_sid !== '' && $current_sid !== '' && !hash_equals($issued_sid, $current_sid))
		{
			return null;
		}

		$result = is_array($data['result'] ?? null) ? $data['result'] : [];
		$result['order_srl'] = (int)($data['order_srl'] ?? 0);
		return $result;
	}

	/**
	 * 티켓을 버린다. (결제 실패로 다시 시작할 때)
	 *
	 * @param string $state
	 * @return void
	 */
	public static function discard(string $state): void
	{
		if (preg_match('/^[a-f0-9]{32}$/', $state))
		{
			\FileHandler::removeFile(self::path($state));
		}
	}
}
