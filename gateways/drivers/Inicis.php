<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * KG이니시스 (INIStdPay 표준결제).
 *
 * 흐름:
 *   1) 결제 화면이 숨은 폼을 만들어 INIStdPay.pay 로 결제창을 띄운다 (buildRequest 값으로)
 *   2) 인증이 끝나면 이니시스가 returnUrl 로 브라우저를 POST 로 돌려보낸다
 *      (resultCode, authToken, authUrl, netCancelUrl 이 실려 온다)
 *   3) 서버가 authUrl 로 승인 요청을 보내야 비로소 돈이 넘어온다 ← approve()
 *
 * 서명: 요청은 oid/price/timestamp 를 NVP 로 이어 SHA256, 승인은 authToken/timestamp.
 * verification 은 같은 문자열에 signKey 를 끼워 넣은 위변조 검증값이다.
 *
 * 주의: authUrl 은 PG 가 알려주는 값이지만 브라우저를 거쳐 오므로, *.inicis.com 도메인일
 * 때만 호출한다 (조작된 콜백으로 우리 서버가 임의 주소를 때리는 것을 막는다).
 *
 * 취소는 별도 체계(INIAPI)라 API 키를 따로 받는다.
 * 테스트: mid INIpayTest / signKey SU5JTElURV9UUklQTEVERVNfS0VZU1RS / INIAPI key ItEQKi3rY7uvDS8l
 */
class Inicis extends Base
{
	protected const SDK_LIVE = 'https://stdpay.inicis.com/stdjs/INIStdPay.js';
	protected const SDK_TEST = 'https://stgstdpay.inicis.com/stdjs/INIStdPay.js';
	protected const REFUND_URL = 'https://iniapi.inicis.com/api/v1/refund';

	public function getName(): string
	{
		return 'inicis';
	}

	public function isConfigured(): bool
	{
		// API 키는 취소·환불에 쓴다. 없으면 결제만 되고 환불이 막히므로 여기서 함께 본다
		return trim((string)$this->config->inicis_mid) !== ''
			&& trim((string)$this->config->inicis_sign_key) !== ''
			&& trim((string)$this->config->inicis_api_key) !== '';
	}

	public function requiresClientPayment(): bool
	{
		return true;
	}

	public function getClientScript(): string
	{
		return ($this->config->test_mode === 'Y') ? self::SDK_TEST : self::SDK_LIVE;
	}

	/**
	 * 결제창 폼에 넣을 값. pay.js 가 숨은 폼을 만들어 INIStdPay.pay 를 부른다.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$state = $state !== '' ? $state : (string)\Context::get('pay_state');

		$mid = trim((string)$this->config->inicis_mid);
		$sign_key = trim((string)$this->config->inicis_sign_key);
		$oid = (string)$order->order_code;
		$price = (int)$order->amount;
		$timestamp = (string)round(microtime(true) * 1000);

		return [
			'sdkUrl' => $this->getClientScript(),
			'fields' => [
				'version' => '1.0',
				'gopaymethod' => 'Card',
				'mid' => $mid,
				'oid' => $oid,
				'price' => $price,
				'timestamp' => $timestamp,
				'use_chkfake' => 'Y',
				// NVP 알파벳 순 정렬, 마지막 & 없음, 공백 없음 — 이니시스 서명 규약
				'signature' => hash('sha256', 'oid=' . $oid . '&price=' . $price . '&timestamp=' . $timestamp),
				'verification' => hash('sha256', 'oid=' . $oid . '&price=' . $price . '&signKey=' . $sign_key . '&timestamp=' . $timestamp),
				'mKey' => hash('sha256', $sign_key),
				'currency' => 'WON',
				'goodname' => mb_substr((string)$order->title, 0, 40) ?: $oid,
				'buyername' => (string)$order->payer_name,
				'buyertel' => preg_replace('/[^0-9]/', '', (string)$order->payer_phone),
				'buyeremail' => (string)$order->payer_email,
				'returnUrl' => Base::buildActionUrl('procZittme_payCallback', [
					'gateway' => 'inicis',
					'state' => $state,
				]),
				'closeUrl' => Base::buildActionUrl('procZittme_payCallback', [
					'gateway' => 'inicis',
					'state' => $state,
					'failed' => '1',
				]),
				'acceptmethod' => 'below1000',
			],
		];
	}

	/**
	 * 서버 승인 (authUrl POST).
	 *
	 * @param object $order
	 * @param array $params 콜백 값 (resultCode, authToken, authUrl)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		if (trim((string)($params['resultCode'] ?? '')) !== '0000')
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ' (' . (string)($params['resultCode'] ?? '') . ')');
		}

		$auth_token = trim((string)($params['authToken'] ?? ''));
		$auth_url = trim((string)($params['authUrl'] ?? ''));
		if ($auth_token === '' || !$this->isInicisUrl($auth_url))
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$mid = trim((string)$this->config->inicis_mid);
		$sign_key = trim((string)$this->config->inicis_sign_key);
		$timestamp = (string)round(microtime(true) * 1000);

		[$ok, $status_code, $body, $parsed] = $this->request($auth_url, 'POST', [
			'mid' => $mid,
			'authToken' => $auth_token,
			'timestamp' => $timestamp,
			'signature' => hash('sha256', 'authToken=' . $auth_token . '&timestamp=' . $timestamp),
			'verification' => hash('sha256', 'authToken=' . $auth_token . '&signKey=' . $sign_key . '&timestamp=' . $timestamp),
			'charset' => 'UTF-8',
			'format' => 'JSON',
		], ['Content-Type' => 'application/x-www-form-urlencoded']);

		if (!$ok)
		{
			// 승인 요청 자체가 실패하면 결제가 붕 뜰 수 있다. 망취소로 되돌린다.
			$this->netCancel($params, $order);
			return Result::fail($this->errorMessage($parsed, $status_code), ['raw' => $body]);
		}

		$result_code = (string)($parsed['resultCode'] ?? '');
		$tid = (string)($parsed['tid'] ?? '');
		$total = (int)($parsed['TotPrice'] ?? 0);

		if ($result_code !== '0000')
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		// PG 가 확인해 준 금액이 우리 주문 금액과 다르면 승인으로 인정하지 않고 망취소한다.
		if ($total !== (int)$order->amount)
		{
			$this->netCancel($params, $order);
			return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
				'tid' => $tid,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		$extra = [];
		if (!empty($parsed['VACT_Num']))
		{
			// 가상계좌 발급 — 입금 전이므로 pending 이다
			$extra['vbank'] = [
				'bank' => (string)($parsed['VACT_BankCode'] ?? ''),
				'account' => (string)($parsed['VACT_Num'] ?? ''),
				'holder' => (string)($parsed['VACT_Name'] ?? ''),
				'due_date' => (string)($parsed['VACT_Date'] ?? ''),
			];
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $tid,
			'amount' => $total,
			'pay_method' => (string)($parsed['payMethod'] ?? ''),
			'status' => !empty($extra['vbank']) ? Order::STATUS_PENDING : Order::STATUS_PAID,
			'raw' => $body,
			'extra' => $extra,
		]);
	}

	/**
	 * (부분)취소 — INIAPI refund.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount 0 이면 전액
	 * @return Result
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$tid = trim((string)$order->pg_tid);
		$api_key = trim((string)$this->config->inicis_api_key);
		if ($tid === '' || $api_key === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$mid = trim((string)$this->config->inicis_mid);
		$timestamp = date('YmdHis');
		$client_ip = (string)($_SERVER['SERVER_ADDR'] ?? '127.0.0.1');
		$reason = mb_substr($reason !== '' ? $reason : lang('zittme_pay.cancel_default_reason'), 0, 100);
		// 승인 때 받아 둔 결제수단. 해시 대상이라 비우면 인증이 어긋난다
		$paymethod = self::payMethodCode((string)($order->pay_method ?? ''));

		$partial = ($amount > 0 && $amount < (int)$order->amount);
		$type = $partial ? 'PartialRefund' : 'Refund';

		$params = [
			'type' => $type,
			'paymethod' => $paymethod,
			'timestamp' => $timestamp,
			'clientIp' => $client_ip,
			'mid' => $mid,
			'tid' => $tid,
			'msg' => $reason,
		];

		// hashData = SHA512(INIAPI키 + type + paymethod + timestamp + clientIp + mid + tid)
		// 부분취소는 뒤에 price + confirmPrice 가 더 붙는다
		$plain = $api_key . $type . $paymethod . $timestamp . $client_ip . $mid . $tid;
		if ($partial)
		{
			$price = (string)$amount;
			$confirm = (string)((int)$order->amount - (int)$order->cancelled_amount - $amount);
			$params['price'] = $price;
			$params['confirmPrice'] = $confirm;
			$plain .= $price . $confirm;
		}
		$params['hashData'] = hash('sha512', $plain);

		// NVP(form) 전송이다. JSON 은 받지 않는다
		[$ok, $status_code, $body, $parsed] = $this->request(self::REFUND_URL, 'POST', $params, [
			'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
		]);

		if (!$ok || (string)($parsed['resultCode'] ?? '') !== '00')
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tid, 'raw' => $body]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => $tid,
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => $partial ? Order::STATUS_PARTIAL_CANCELLED : Order::STATUS_CANCELLED,
			'raw' => $body,
		]);
	}

	/**
	 * 승인 응답의 payMethod 를 INIAPI 가 쓰는 지불수단 코드로 맞춘다.
	 * 모르는 값이면 카드로 본다 — 표준결제에서 가장 흔하다.
	 */
	protected static function payMethodCode(string $stored): string
	{
		$map = [
			'card' => 'Card',
			'vbank' => 'Vbank',
			'directbank' => 'Bank',
			'bank' => 'Bank',
			'hpp' => 'HPP',
			'phone' => 'HPP',
		];
		return $map[strtolower(trim($stored))] ?? 'Card';
	}

	/**
	 * 단건 조회. 이니시스 표준결제는 범용 조회 API 가 따로 없어 지원하지 않는다.
	 * 웹훅도 쓰지 않으므로(가상계좌 입금 통지는 별도 계약) 호출될 일이 없다.
	 *
	 * @param string $tid
	 * @return Result
	 */
	public function query(string $tid): Result
	{
		return Result::fail(lang('zittme_pay.msg_unknown_pg_status'));
	}

	/**
	 * 망취소 — 승인 통신이 깨졌을 때 인증 건을 되돌린다. 실패해도 조용히 넘어간다
	 * (이미 실패 처리 중이므로 여기서 또 죽으면 안 된다).
	 */
	protected function netCancel(array $params, object $order): void
	{
		$cancel_url = trim((string)($params['netCancelUrl'] ?? ''));
		$auth_token = trim((string)($params['authToken'] ?? ''));
		if (!$this->isInicisUrl($cancel_url) || $auth_token === '')
		{
			return;
		}

		$sign_key = trim((string)$this->config->inicis_sign_key);
		$timestamp = (string)round(microtime(true) * 1000);
		$this->request($cancel_url, 'POST', [
			'mid' => trim((string)$this->config->inicis_mid),
			'authToken' => $auth_token,
			'timestamp' => $timestamp,
			'signature' => hash('sha256', 'authToken=' . $auth_token . '&timestamp=' . $timestamp),
			'verification' => hash('sha256', 'authToken=' . $auth_token . '&signKey=' . $sign_key . '&timestamp=' . $timestamp),
			'charset' => 'UTF-8',
			'format' => 'JSON',
		], ['Content-Type' => 'application/x-www-form-urlencoded']);
	}

	/**
	 * 이니시스 도메인인지. PG 가 준 주소라도 브라우저를 거쳐 오므로 반드시 확인한다.
	 */
	protected function isInicisUrl(string $url): bool
	{
		$host = (string)parse_url($url, \PHP_URL_HOST);
		$scheme = (string)parse_url($url, \PHP_URL_SCHEME);
		return $scheme === 'https' && ($host === 'inicis.com' || str_ends_with($host, '.inicis.com'));
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$message = trim((string)($parsed['resultMsg'] ?? ''));
		$code = trim((string)($parsed['resultCode'] ?? ''));

		if ($message !== '')
		{
			return $code !== '' ? ($message . ' (' . $code . ')') : $message;
		}
		if ($status_code === 0)
		{
			return lang('zittme_pay.msg_pg_unreachable');
		}
		return lang('zittme_pay.msg_pg_error') . ' (HTTP ' . $status_code . ')';
	}
}
