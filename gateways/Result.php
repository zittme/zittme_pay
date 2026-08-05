<?php

namespace Zittme\Modules\Zittme_pay\Gateways;

/**
 * 게이트웨이 표준 결과 객체.
 *
 * 모든 드라이버의 approve / cancel / query 가 이 모양으로만 답한다. 덕분에 호출부는
 * 어느 PG 인지 몰라도 결과를 해석할 수 있다.
 *
 * raw 는 PG 원문이다. 분쟁이 붙었을 때 우리가 무엇을 받았는지 대는 근거가 되므로
 * 가공하지 않고 그대로 담아 로그로 넘긴다.
 */
class Result
{
	public bool $success = false;
	public string $message = '';
	public string $tid = '';
	public int $amount = 0;
	public string $raw = '';

	/**
	 * PG 가 알려주는 결제수단 (card / vbank / transfer …). 표시용이라 없어도 된다.
	 */
	public string $pay_method = '';

	/**
	 * PG 상태를 우리 어휘(Order::STATUS_*)로 옮긴 값.
	 *
	 * 같은 드라이버라도 결제수단에 따라 도착 상태가 다르다. 토스 가상계좌는 승인 API 가
	 * 성공해도 아직 입금 전(WAITING_FOR_DEPOSIT)이라 paid 가 아니다. 그래서 "이 드라이버는
	 * 항상 paid" 같은 고정값으로 판단하지 않고, 응답을 읽은 드라이버가 직접 답하게 한다.
	 *
	 * 비어 있으면 호출부가 드라이버의 getInitialStatus() 를 쓴다.
	 */
	public string $status = '';

	/**
	 * 드라이버가 주문에 함께 저장하고 싶은 부가정보.
	 */
	public array $extra = [];

	/**
	 * 성공 결과.
	 *
	 * @param array $values
	 * @return self
	 */
	public static function ok(array $values = []): self
	{
		$result = new self();
		$result->success = true;
		$result->assign($values);
		return $result;
	}

	/**
	 * 실패 결과.
	 *
	 * @param string $message
	 * @param array $values
	 * @return self
	 */
	public static function fail(string $message, array $values = []): self
	{
		$result = new self();
		$result->success = false;
		$result->message = $message;
		$result->assign($values);
		return $result;
	}

	/**
	 * @param array $values
	 * @return void
	 */
	protected function assign(array $values): void
	{
		foreach ($values as $key => $value)
		{
			if (!property_exists($this, $key))
			{
				continue;
			}
			if ($key === 'amount')
			{
				$this->amount = (int)$value;
			}
			elseif ($key === 'extra')
			{
				$this->extra = is_array($value) ? $value : [];
			}
			elseif ($key === 'success')
			{
				$this->success = (bool)$value;
			}
			else
			{
				$this->{$key} = (string)$value;
			}
		}
	}
}
