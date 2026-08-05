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
- 결제 게이트웨이 드라이버 구조
  - 토스페이먼츠 드라이버
  - 무통장입금 드라이버
- 부분 취소·수동 환불 처리
- 결제 로그
- 체크아웃·결과 화면 스킨 (기본 스킨 포함)

## 드라이버 추가

`gateways/drivers/` 에 드라이버 클래스를 추가하는 방식으로 다른 PG를 붙일 수 있습니다. 기존 드라이버(Toss, Banktransfer)를 참고하세요.

## 라이선스

[GPL v2](LICENSE)

## 문의

- 홈페이지: https://zitt.me
- 매뉴얼: https://zitt.me/manual
