<?php

namespace Zittme\Modules\Zittme_pay\Gateways\Drivers;

use Zittme\Modules\Zittme_pay\Gateways\Base;
use Zittme\Modules\Zittme_pay\Gateways\Result;
use Zittme\Modules\Zittme_pay\Models\Order;

/**
 * NHN KCP (표준결제, REST 승인).
 *
 * 흐름:
 *   1) 결제 화면이 숨은 폼을 만들어 KCP_Pay_Execute_Web 으로 결제창을 띄운다
 *   2) 인증이 끝나면 KCP 가 Ret_URL 로 브라우저를 POST 로 돌려보낸다
 *      (res_cd, enc_data, enc_info, tran_cd 가 실려 온다)
 *   3) 서버가 승인 API(/gw/enc/v1/payment)를 호출해야 비로소 돈이 넘어온다 ← approve()
 *
 * 인증서: KCP 는 API 인증에 서비스 인증서(PEM 본문)를 쓰고, 취소는 상점 개인키로
 * SHA256withRSA 서명(kcp_sign_data)까지 요구한다. 관리자 설정에 PEM 을 그대로 붙여 넣는다.
 *
 * 테스트: site_cd T0000 + 개발자센터가 제공하는 테스트 인증서·키.
 */
class Kcp extends Base
{
	protected const API_LIVE = 'https://spl.kcp.co.kr';
	protected const API_TEST = 'https://stg-spl.kcp.co.kr';
	protected const SDK_LIVE = 'https://spay.kcp.co.kr/plugin/kcp_spay_hub.js';
	protected const SDK_TEST = 'https://testspay.kcp.co.kr/plugin/kcp_spay_hub.js';

	public function getName(): string
	{
		return 'kcp';
	}

	public function isConfigured(): bool
	{
		// 개인키는 취소 서명(kcp_sign_data)에 쓴다. 없으면 결제만 되고 환불이 막힌다
		return trim((string)$this->config->kcp_site_cd) !== ''
			&& trim((string)$this->config->kcp_cert_info) !== ''
			&& trim((string)$this->config->kcp_priv_key) !== '';
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
	 * 결제창 폼에 넣을 값. pay.js 가 숨은 폼을 만들어 KCP_Pay_Execute_Web 을 부른다.
	 *
	 * @param object $order
	 * @param string $state 티켓 state
	 * @return array
	 */
	public function buildRequest(object $order, string $state = ''): array
	{
		$state = $state !== '' ? $state : (string)\Context::get('pay_state');

		return [
			'sdkUrl' => $this->getClientScript(),
			'fields' => [
				'site_cd' => trim((string)$this->config->kcp_site_cd),
				'site_name' => mb_substr((string)(\Context::getBrowserTitle() ?: 'zittme'), 0, 20),
				'pay_method' => '100000000000',
				'ordr_idxx' => (string)$order->order_code,
				'good_name' => mb_substr((string)$order->title, 0, 40) ?: (string)$order->order_code,
				'good_mny' => (int)$order->amount,
				'currency' => '410',
				'buyr_name' => (string)$order->payer_name,
				'buyr_tel2' => preg_replace('/[^0-9]/', '', (string)$order->payer_phone),
				'buyr_mail' => (string)$order->payer_email,
				'Ret_URL' => Base::buildActionUrl('procZittme_payCallback', [
					'gateway' => 'kcp',
					'state' => $state,
				]),
			],
		];
	}

	/**
	 * 서버 승인.
	 *
	 * @param object $order
	 * @param array $params 콜백 값 (res_cd, enc_data, enc_info, tran_cd)
	 * @return Result
	 */
	public function approve(object $order, array $params): Result
	{
		if (trim((string)($params['res_cd'] ?? '')) !== '0000')
		{
			return Result::fail(lang('zittme_pay.msg_payment_not_completed') . ' (' . (string)($params['res_cd'] ?? '') . ')');
		}

		$enc_data = trim((string)($params['enc_data'] ?? ''));
		$enc_info = trim((string)($params['enc_info'] ?? ''));
		$tran_cd = trim((string)($params['tran_cd'] ?? ''));
		if ($enc_data === '' || $enc_info === '' || $tran_cd === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			$this->apiBase() . '/gw/enc/v1/payment',
			'POST',
			[
				'tran_cd' => $tran_cd,
				'site_cd' => trim((string)$this->config->kcp_site_cd),
				'kcp_cert_info' => $this->certInfo(),
				'enc_data' => $enc_data,
				'enc_info' => $enc_info,
				// 서버 금액·주문번호. 브라우저가 보낸 값이 아니다.
				'ordr_no' => (string)$order->order_code,
				'ordr_mony' => (string)(int)$order->amount,
			],
			['Content-Type' => 'application/json']
		);

		if (!$ok)
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['raw' => $body]);
		}

		$res_cd = (string)($parsed['res_cd'] ?? '');
		$tno = (string)($parsed['tno'] ?? '');
		$total = (int)($parsed['amount'] ?? 0);

		if ($res_cd !== '0000')
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tno, 'raw' => $body]);
		}

		// PG 가 확인해 준 금액이 우리 주문 금액과 다르면 승인으로 인정하지 않는다.
		if ($total > 0 && $total !== (int)$order->amount)
		{
			return Result::fail(lang('zittme_pay.msg_amount_mismatch'), [
				'tid' => $tno,
				'amount' => $total,
				'raw' => $body,
			]);
		}

		$extra = [];
		if (!empty($parsed['bankname']) && !empty($parsed['account']))
		{
			// 가상계좌 발급 — 입금 전이므로 pending 이다
			$extra['vbank'] = [
				'bank' => (string)$parsed['bankname'],
				'account' => (string)$parsed['account'],
				'holder' => (string)($parsed['depositor'] ?? ''),
				'due_date' => (string)($parsed['va_date'] ?? ''),
			];
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_approve_success'),
			'tid' => $tno,
			'amount' => $total > 0 ? $total : (int)$order->amount,
			'pay_method' => (string)($parsed['pay_method'] ?? ''),
			'status' => !empty($extra['vbank']) ? Order::STATUS_PENDING : Order::STATUS_PAID,
			'raw' => $body,
			'extra' => $extra,
		]);
	}

	/**
	 * (부분)취소.
	 *
	 * 취소는 상점 개인키의 SHA256withRSA 서명(kcp_sign_data)이 필요하다.
	 *
	 * @param object $order
	 * @param string $reason
	 * @param int $amount 0 이면 전액
	 * @return Result
	 */
	public function cancel(object $order, string $reason, int $amount = 0): Result
	{
		$tno = trim((string)$order->pg_tid);
		if ($tno === '')
		{
			return Result::fail(lang('zittme_pay.msg_missing_payment_key'));
		}

		$site_cd = trim((string)$this->config->kcp_site_cd);
		$partial = ($amount > 0 && $amount < (int)$order->amount);
		$mod_type = $partial ? 'STPC' : 'STSC';

		$sign = $this->signData($site_cd . '^' . $tno . '^' . $mod_type);
		if ($sign === '')
		{
			return Result::fail(lang('zittme_pay.msg_cancel_failed') . ' (kcp_sign_data)');
		}

		$data = [
			'site_cd' => $site_cd,
			'kcp_cert_info' => $this->certInfo(),
			'kcp_sign_data' => $sign,
			'tno' => $tno,
			'mod_type' => $mod_type,
			'mod_desc' => mb_substr($reason !== '' ? $reason : lang('zittme_pay.cancel_default_reason'), 0, 100),
		];
		if ($partial)
		{
			$data['mod_mny'] = (string)$amount;
			$data['rem_mny'] = (string)((int)$order->amount - (int)$order->cancelled_amount - $amount);
		}

		[$ok, $status_code, $body, $parsed] = $this->request(
			$this->apiBase() . '/gw/mod/v1/cancel',
			'POST',
			$data,
			['Content-Type' => 'application/json']
		);

		if (!$ok || (string)($parsed['res_cd'] ?? '') !== '0000')
		{
			return Result::fail($this->errorMessage($parsed, $status_code), ['tid' => $tno, 'raw' => $body]);
		}

		return Result::ok([
			'message' => lang('zittme_pay.msg_cancel_success'),
			'tid' => $tno,
			'amount' => $amount > 0 ? $amount : (int)$order->amount,
			'status' => $partial ? Order::STATUS_PARTIAL_CANCELLED : Order::STATUS_CANCELLED,
			'raw' => $body,
		]);
	}

	/**
	 * 단건 조회. KCP 표준결제는 범용 조회 API 가 따로 없어 지원하지 않는다.
	 *
	 * @param string $tid
	 * @return Result
	 */
	public function query(string $tid): Result
	{
		return Result::fail(lang('zittme_pay.msg_unknown_pg_status'));
	}

	/**
	 * 서비스 인증서(PEM 본문). 줄바꿈이 섞여 저장돼도 그대로 쓴다.
	 */
	protected function certInfo(): string
	{
		return trim((string)$this->config->kcp_cert_info);
	}

	/**
	 * 상점 개인키로 SHA256withRSA 서명. 실패하면 빈 문자열.
	 */
	protected function signData(string $data): string
	{
		$key_pem = trim((string)$this->config->kcp_priv_key);
		if ($key_pem === '')
		{
			return '';
		}
		$key = openssl_pkey_get_private($key_pem, (string)$this->config->kcp_priv_pass);
		if (!$key)
		{
			return '';
		}
		$signature = '';
		if (!openssl_sign($data, $signature, $key, \OPENSSL_ALGO_SHA256))
		{
			return '';
		}
		return base64_encode($signature);
	}

	protected function apiBase(): string
	{
		return ($this->config->test_mode === 'Y') ? self::API_TEST : self::API_LIVE;
	}

	protected function errorMessage(array $parsed, int $status_code): string
	{
		$message = trim((string)($parsed['res_msg'] ?? ''));
		$code = trim((string)($parsed['res_cd'] ?? ''));

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
