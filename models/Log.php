<?php

namespace Zittme\Modules\Zittme_pay\Models;

/**
 * 통신 로그.
 *
 * 결제와 관련해 오간 요청·응답을 하나도 빠짐없이 남긴다 (보안 3원칙 3).
 * 분쟁이 붙으면 "우리가 PG 에 무엇을 보냈고 무엇을 받았는가" 를 대는 것 말고는 방법이 없다.
 *
 * 이 클래스의 메서드는 절대 예외를 밖으로 내보내지 않는다. 로그를 남기다 실패했다고
 * 결제가 중단되면 본말이 전도된다 — 기록은 결제의 부산물이지 조건이 아니다.
 */
class Log
{
	/**
	 * 로그에 그대로 남기면 안 되는 키. 값이 있으면 마스킹해서 남긴다.
	 *
	 * PG 시크릿 키나 Authorization 헤더가 로그 테이블에 평문으로 쌓이면,
	 * 로그 화면을 볼 수 있는 사람 모두가 결제 키를 갖게 된다.
	 */
	protected const SECRET_KEYS = [
		'secretkey', 'secret_key', 'clientkey', 'client_key', 'authorization',
		'password', 'cppwd', 'apikey', 'api_key', 'token',
	];

	/**
	 * 로그 한 줄을 남긴다.
	 *
	 * @param array $args order_srl / order_code / gateway / action / request_data /
	 *                    response_data / result / pg_tid / amount
	 * @return void
	 */
	public static function add(array $args): void
	{
		try
		{
			$log = new \stdClass;
			$log->log_srl = getNextSequence();
			$log->order_srl = (int)($args['order_srl'] ?? 0);
			$log->order_code = (string)($args['order_code'] ?? '');
			$log->gateway = (string)($args['gateway'] ?? '');
			$log->action = (string)($args['action'] ?? '');
			$log->request_data = self::stringify($args['request_data'] ?? null);
			$log->response_data = self::stringify($args['response_data'] ?? null);
			$log->result = (($args['result'] ?? 'S') === 'F') ? 'F' : 'S';
			$log->pg_tid = (string)($args['pg_tid'] ?? '');
			$log->amount = (int)($args['amount'] ?? 0);
			$log->ipaddress = (string)($_SERVER['REMOTE_ADDR'] ?? '');
			$log->regdate = date('YmdHis');

			executeQuery('zittme_pay.insertLog', $log);
		}
		catch (\Throwable $e)
		{
			// 로그 실패가 결제를 막아서는 안 된다. 조용히 넘긴다.
		}
	}

	/**
	 * 실패 로그. 자주 쓰므로 따로 둔다.
	 *
	 * @param array $args
	 * @return void
	 */
	public static function fail(array $args): void
	{
		$args['result'] = 'F';
		self::add($args);
	}

	/**
	 * 로그에 담을 문자열로 만든다. 배열·객체는 JSON 으로 펴고, 비밀값은 가린다.
	 *
	 * @param mixed $data
	 * @return string
	 */
	public static function stringify($data): string
	{
		if ($data === null || $data === '')
		{
			return '';
		}
		if (is_string($data))
		{
			return mb_substr($data, 0, 60000);
		}

		$masked = self::maskSecrets($data);
		$json = json_encode($masked, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
		return mb_substr((string)$json, 0, 60000);
	}

	/**
	 * 비밀값을 재귀적으로 가린다.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	protected static function maskSecrets($data)
	{
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		if (!is_array($data))
		{
			return $data;
		}

		$result = [];
		foreach ($data as $key => $value)
		{
			if (is_string($key) && in_array(strtolower(str_replace('-', '_', $key)), self::SECRET_KEYS, true))
			{
				$result[$key] = '***';
				continue;
			}
			$result[$key] = (is_array($value) || is_object($value)) ? self::maskSecrets($value) : $value;
		}
		return $result;
	}

	/**
	 * 로그 목록.
	 *
	 * @param object $args
	 * @return object
	 */
	public static function getList(object $args): object
	{
		return executeQueryArray('zittme_pay.getLogList', $args);
	}

	/**
	 * 로그 1건.
	 *
	 * @param int $log_srl
	 * @return ?object
	 */
	public static function get(int $log_srl): ?object
	{
		if ($log_srl <= 0)
		{
			return null;
		}
		$output = executeQuery('zittme_pay.getLog', (object)['log_srl' => $log_srl]);
		return ($output->toBool() && $output->data) ? $output->data : null;
	}

	/**
	 * 보관기간이 지난 로그를 지운다.
	 *
	 * @param int $days 0 이면 아무것도 하지 않는다
	 * @return void
	 */
	public static function purgeOlderThan(int $days): void
	{
		if ($days <= 0)
		{
			return;
		}
		executeQuery('zittme_pay.deleteLogsBefore', (object)[
			'regdate' => date('YmdHis', time() - ($days * 86400)),
		]);
	}
/**
	 * 응답을 사람이 읽는 한 줄로 줄인다.
	 *
	 * 대행사 응답은 그쪽 문구를, 우리가 만든 값(무통장 계좌·입금기한)은 우리 문구를 쓴다.
	 * 원문은 화면이 title 로 함께 실어 보내므로 여기서는 요약만 만든다.
	 *
	 * @param string $raw
	 * @return string
	 */
	public static function summarize(string $raw): string
	{
		$raw = trim($raw);
		if ($raw === '')
		{
			return '';
		}
		if ($raw[0] !== '{' && $raw[0] !== '[')
		{
			return mb_substr($raw, 0, 200);
		}

		$data = json_decode($raw, true);
		if (!is_array($data))
		{
			return mb_substr($raw, 0, 200);
		}

		// 대행사가 준 문구가 있으면 그것이 가장 쓸모 있다
		foreach (['resultMsg', 'message', 'res_msg', 'error_description', 'msg'] as $key)
		{
			if (trim((string)($data[$key] ?? '')) !== '')
			{
				return mb_substr((string)$data[$key], 0, 200);
			}
		}

		$parts = [];
		$account = $data['account'] ?? null;
		if (is_array($account))
		{
			$parts[] = trim(($account['bank'] ?? '') . ' ' . ($account['account'] ?? ''));
			if (trim((string)($account['holder'] ?? '')) !== '')
			{
				$parts[] = (string)$account['holder'];
			}
		}
		elseif (trim((string)$account) !== '')
		{
			$parts[] = (string)$account;
		}
		if (trim((string)($data['depositor'] ?? '')) !== '')
		{
			$parts[] = lang('zittme_pay.zpay_log_depositor') . ' ' . $data['depositor'];
		}
		if (trim((string)($data['due_date'] ?? '')) !== '')
		{
			$parts[] = lang('zittme_pay.zpay_log_due') . ' ' . zdate((string)$data['due_date'], 'Y-m-d H:i');
		}

		return count($parts) ? implode(' · ', $parts) : mb_substr($raw, 0, 200);
	}
}
