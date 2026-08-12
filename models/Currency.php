<?php

namespace Zittme\Modules\Zittme_pay\Models;

/**
 * 공용 통화·환율 서비스.
 *
 * 환율의 단일 출처다. 짓미페이(결제 환산)와 커머스(다통화 가격·표시)가 함께 참조한다.
 * 값은 zittme_pay 모듈 설정에 저장한다:
 *   exchange_rates      ['USD' => 1350.0, ...]  1 통화당 KRW
 *   exchange_rates_manual ['USD' => 'Y']        수동 고정 통화 (자동 갱신이 덮지 않음)
 *   exchange_auto       'Y'/'N'                 일 1회 자동 갱신
 *   exchange_source     'erapi'/'koreaexim'     자동 갱신 출처
 *   exchange_api_key    한국수출입은행 API 키 (koreaexim 일 때만)
 *   exchange_updated    마지막 성공 갱신 시각 (YmdHis)
 *
 * 주문에는 언제나 체결 시점 환율을 박제한다. 여기 값은 "지금" 의 환율일 뿐이다.
 */
class Currency
{
	/**
	 * 소수 자리를 쓰지 않는 통화 (ISO 4217 기준에서 결제에 흔한 것들).
	 */
	public const ZERO_DECIMAL = ['KRW', 'JPY', 'TWD', 'HUF', 'VND'];

	/**
	 * 통화 기호. 없으면 코드 그대로 보여준다.
	 */
	public const SYMBOLS = [
		'KRW' => '₩', 'USD' => '$', 'EUR' => '€', 'JPY' => '¥', 'GBP' => '£',
		'CNY' => '¥', 'TWD' => 'NT$', 'HKD' => 'HK$', 'AUD' => 'A$', 'CAD' => 'C$',
		'SGD' => 'S$',
	];

	/**
	 * 자동 갱신을 지원하는 출처.
	 */
	public const SOURCES = ['erapi', 'koreaexim'];

	/**
	 * 1 통화당 KRW 환율. 모르는 통화면 0.
	 *
	 * @param string $currency
	 * @return float
	 */
	public static function getRate(string $currency): float
	{
		$currency = strtoupper(trim($currency));
		if ($currency === 'KRW')
		{
			return 1.0;
		}

		$rates = self::getRates();
		return (float)($rates[$currency] ?? 0);
	}

	/**
	 * 전체 환율 맵.
	 *
	 * @return array<string, float>
	 */
	public static function getRates(): array
	{
		$config = Config::getConfig();
		$rates = $config->exchange_rates ?? [];
		if (!is_array($rates))
		{
			$rates = [];
		}

		// 페이팔 단일 환율 시절의 값이 남아 있으면 이어받는다 (설정 이전 호환).
		$legacy_currency = strtoupper(trim((string)($config->paypal_currency ?? '')));
		$legacy_rate = (float)str_replace(',', '', (string)($config->paypal_exchange_rate ?? ''));
		if ($legacy_currency !== '' && $legacy_rate > 0 && empty($rates[$legacy_currency]))
		{
			$rates[$legacy_currency] = $legacy_rate;
		}

		$result = [];
		foreach ($rates as $code => $rate)
		{
			$code = strtoupper(trim((string)$code));
			if ($code !== '' && (float)$rate > 0)
			{
				$result[$code] = (float)$rate;
			}
		}
		return $result;
	}

	/**
	 * KRW 금액을 다른 통화로 환산한다. 통화별 소수 자리에 맞춰 반올림한다.
	 *
	 * @param int $krw_amount
	 * @param string $currency
	 * @return float 환율이 없으면 0
	 */
	public static function fromKRW(int $krw_amount, string $currency): float
	{
		$currency = strtoupper(trim($currency));
		if ($currency === 'KRW')
		{
			return $krw_amount;
		}
		$rate = self::getRate($currency);
		if ($rate <= 0)
		{
			return 0;
		}
		$value = $krw_amount / $rate;
		return self::isZeroDecimal($currency) ? round($value) : round($value, 2);
	}

	/**
	 * 통화 금액을 KRW 로 환산한다 (통계용).
	 *
	 * @param float $amount
	 * @param string $currency
	 * @param float $rate 0 이면 현재 환율 사용
	 * @return int
	 */
	public static function toKRW(float $amount, string $currency, float $rate = 0): int
	{
		$currency = strtoupper(trim($currency));
		if ($currency === 'KRW')
		{
			return (int)round($amount);
		}
		$rate = $rate > 0 ? $rate : self::getRate($currency);
		return (int)round($amount * $rate);
	}

	/**
	 * 통화가 소수 자리를 쓰지 않는가.
	 */
	public static function isZeroDecimal(string $currency): bool
	{
		return in_array(strtoupper(trim($currency)), self::ZERO_DECIMAL, true);
	}

	/**
	 * 결제·화면 공용 금액 포맷. 예: $9.20 / ¥1,200 / ₩12,000
	 *
	 * @param float $amount
	 * @param string $currency
	 * @return string
	 */
	public static function format(float $amount, string $currency): string
	{
		$currency = strtoupper(trim($currency));
		$symbol = self::SYMBOLS[$currency] ?? ($currency . ' ');
		$decimals = self::isZeroDecimal($currency) ? 0 : 2;
		return $symbol . number_format($amount, $decimals, '.', ',');
	}

	/**
	 * 최소단위 정수 금액을 통화 기호까지 붙여 표기한다. 화면 공용.
	 * 예: (1234, 'USD') → $12.34 / (12000, 'KRW') → ₩12,000
	 *
	 * @param int $minor
	 * @param string $currency
	 * @return string
	 */
	public static function money(int $minor, string $currency): string
	{
		$currency = strtoupper(trim($currency)) ?: 'KRW';
		if ($currency === 'KRW')
		{
			return number_format($minor) . '원';
		}
		return self::format(self::fromMinor($minor, $currency), $currency);
	}

	/**
	 * 결제 최소단위 정수 → 표시 금액. (USD 센트 1234 → 12.34)
	 *
	 * @param int $minor
	 * @param string $currency
	 * @return float
	 */
	public static function fromMinor(int $minor, string $currency): float
	{
		return self::isZeroDecimal($currency) ? $minor : ($minor / 100);
	}

	/**
	 * 표시 금액 → 결제 최소단위 정수.
	 *
	 * @param float $amount
	 * @param string $currency
	 * @return int
	 */
	public static function toMinor(float $amount, string $currency): int
	{
		return (int)round(self::isZeroDecimal($currency) ? $amount : ($amount * 100));
	}

	/**
	 * 마지막 갱신이 하루를 넘었으면 갱신한다.
	 *
	 * cron 없이 결제·상품 조회 경로에서 게으르게 돈다 (완전 설치형 원칙, expireOverdue 와 같은 방식).
	 * 외부 API 호출이 응답을 막지 않도록, 시도 시각을 성공 여부와 무관하게 먼저 기록해
	 * 실패해도 하루에 한 번만 시도한다.
	 *
	 * @return void
	 */
	public static function refreshIfStale(): void
	{
		$config = Config::getConfig();
		if (($config->exchange_auto ?? 'N') !== 'Y')
		{
			return;
		}
		$attempted = (string)($config->exchange_attempted ?? '');
		if ($attempted !== '' && (time() - (int)strtotime(preg_replace(
			'/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', '$1-$2-$3 $4:$5:$6', $attempted))) < 86400)
		{
			return;
		}
		$config->exchange_attempted = date('YmdHis');
		Config::setConfig($config);
		self::refresh();
	}

	/**
	 * 환율 자동 갱신. 수동 고정 통화는 덮지 않는다.
	 *
	 * 실패해도 예외를 던지지 않고 false 를 돌려준다. 마지막 성공값이 그대로 살아 있어야
	 * 갱신 실패가 판매 중단으로 번지지 않는다.
	 *
	 * @return bool
	 */
	public static function refresh(): bool
	{
		$config = Config::getConfig();
		if (($config->exchange_auto ?? 'N') !== 'Y')
		{
			return false;
		}

		$source = (string)($config->exchange_source ?? 'erapi');
		$fetched = ($source === 'koreaexim')
			? self::fetchKoreaexim((string)($config->exchange_api_key ?? ''))
			: self::fetchErApi();

		if (!count($fetched))
		{
			return false;
		}

		$rates = is_array($config->exchange_rates) ? $config->exchange_rates : [];
		$manual = is_array($config->exchange_rates_manual ?? null) ? $config->exchange_rates_manual : [];
		foreach ($fetched as $code => $rate)
		{
			if (($manual[$code] ?? 'N') === 'Y')
			{
				continue;
			}
			$rates[$code] = $rate;
		}

		$config->exchange_rates = $rates;
		$config->exchange_updated = date('YmdHis');
		Config::setConfig($config);
		return true;
	}

	/**
	 * open.er-api.com — 키 없이 쓰는 무료 환율. USD 기준이라 KRW 로 재환산한다.
	 *
	 * @return array<string, float> 1 통화당 KRW
	 */
	protected static function fetchErApi(): array
	{
		$parsed = self::fetchJson('https://open.er-api.com/v6/latest/USD');
		$per_usd = (array)($parsed['rates'] ?? []);
		$krw_per_usd = (float)($per_usd['KRW'] ?? 0);
		if ($krw_per_usd <= 0)
		{
			return [];
		}

		$result = [];
		foreach ($per_usd as $code => $per)
		{
			$code = strtoupper((string)$code);
			$per = (float)$per;
			if ($code === 'KRW' || $per <= 0)
			{
				continue;
			}
			// 1 [code] = (KRW/USD) / ([code]/USD) KRW
			$result[$code] = round($krw_per_usd / $per, 4);
		}
		return $result;
	}

	/**
	 * 한국수출입은행 공개 환율 API. 매매기준율(deal_bas_r)을 쓴다.
	 *
	 * @param string $api_key
	 * @return array<string, float>
	 */
	protected static function fetchKoreaexim(string $api_key): array
	{
		$api_key = trim($api_key);
		if ($api_key === '')
		{
			return [];
		}

		$parsed = self::fetchJson(
			'https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey='
			. rawurlencode($api_key) . '&data=AP01'
		);

		$result = [];
		foreach ((array)$parsed as $row)
		{
			if (!is_array($row) || (int)($row['result'] ?? 0) !== 1)
			{
				continue;
			}
			$unit = strtoupper(trim((string)($row['cur_unit'] ?? '')));
			$rate = (float)str_replace(',', '', (string)($row['deal_bas_r'] ?? ''));
			if ($rate <= 0)
			{
				continue;
			}
			// JPY(100), IDR(100) 처럼 100 단위 고시 통화는 1 단위로 되돌린다.
			if (str_contains($unit, '(100)'))
			{
				$unit = str_replace('(100)', '', $unit);
				$rate = $rate / 100;
			}
			if (preg_match('/^[A-Z]{3}$/', $unit))
			{
				$result[$unit] = round($rate, 4);
			}
		}
		return $result;
	}

	/**
	 * @return array
	 */
	protected static function fetchJson(string $url): array
	{
		try
		{
			$response = \Zittme\Framework\HTTP::get($url, [], [], ['timeout' => 15]);
		}
		catch (\Throwable $e)
		{
			return [];
		}
		if ((int)$response->getStatusCode() !== 200)
		{
			return [];
		}
		$parsed = json_decode((string)$response->getBody(), true);
		return is_array($parsed) ? $parsed : [];
	}
}
