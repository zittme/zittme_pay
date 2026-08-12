# Zittme Pay (짓미 페이)

[Zittme](https://github.com/zittme/zittme) 엔진용 결제 모듈입니다. 주문·결제 흐름을 추상화하고 PG(결제대행사)를 드라이버로 붙입니다. [commerce](https://github.com/zittme/commerce)·[reservation](https://github.com/zittme/reservation) 모듈이 이 모듈을 공용 결제 계층으로 씁니다.

## 요구 사항

- Zittme 0.0.01 이상
- 이 모듈은 다른 부가 모듈에 의존하지 않습니다 (가장 아래층).

## 설치

Zittme 설치 경로의 `modules/zittme_pay` 에 이 저장소의 내용을 놓습니다. **폴더 이름은 `zittme_pay`(밑줄)** 입니다.

```bash
cd 설치경로/modules
git clone https://github.com/zittme/zittme_pay.git
```

압축 파일로 받았다면 `modules/zittme_pay/` 에 풀면 됩니다. 이후 관리자 화면에 접속하면 테이블 생성과 기본 설정이 자동으로 진행됩니다.

## 주요 기능

- 주문 원장과 상태 전이(멱등), 만료 주문 정리
- 결제 게이트웨이 드라이버 구조 (7종 내장)
  - 토스페이먼츠
  - KG이니시스 (INIStdPay, 망취소, INIAPI 취소)
  - NHN KCP (REST 승인, 개인키 서명 취소)
  - 나이스페이 (v1 API, 샌드박스 지원)
  - 포트원 V2 (채널 하나로 여러 PG 연결)
  - PayPal (REST Orders v2, 해외 결제)
  - 무통장입금
- 공용 환율 관리
  - 통화별 환율 수동 입력, 하루 1회 자동 갱신 (open.er-api.com 또는 한국수출입은행 API)
  - 커머스 모듈의 다통화 가격이 같은 환율을 참조합니다
- 외화 결제
  - 요청 모듈이 외화 주문을 넘기면 그 통화로 결제합니다
  - 주문 통화를 지원하지 않는 결제수단은 결제 화면에 나타나지 않습니다
  - PayPal 은 KRW 주문을 설정한 통화로 환산해 결제하고, 환불도 결제 당시 환율로 처리합니다
- 부분 취소·수동 환불 처리, 결제 로그
- 체크아웃·결과 화면 스킨 (기본 스킨 포함)

## 보안 원칙

모든 드라이버가 같은 원칙으로 동작합니다.

1. 승인 금액은 항상 서버가 보관한 주문 금액을 기준으로 합니다. 브라우저가 보낸 금액은 대조용으로만 씁니다.
2. 웹훅 본문을 믿지 않습니다. PG 조회 API 로 재확인한 결과만 반영합니다.
3. PG 가 확인해 준 금액이 주문 금액과 다르면 승인하지 않습니다.

## 드라이버 추가

`gateways/drivers/` 에 드라이버 클래스를 추가하고 `gateways/Base.php` 의 `$supported_gateways` 에 이름을 적는 것으로 끝나야 한다는 것이 이 추상화의 목표입니다. 결제 형태에 따라 세 가지 중 하나를 고릅니다.

- 브라우저 SDK 결제창: `requiresClientPayment()` 참조 (Toss, Nicepay, Kcp, Inicis, Portone)
- PG 페이지 리다이렉트: `requiresRedirect()` + `buildRedirect()` (Paypal)
- 서버 즉시 처리: 둘 다 아님 (Banktransfer)

외화를 받는 드라이버는 `supportsCurrency()` 를 재정의합니다.

## 라이선스

[GPL v2](LICENSE)

## 문의

- 홈페이지: https://zitt.me
- 매뉴얼: https://zitt.me/manual
