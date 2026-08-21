<?php

$lang->zittme_pay = '짓미 페이';

// 관리자 탭
$lang->zpay_tab_config = '기본';
$lang->zpay_tab_gateway = '결제수단';
$lang->zpay_tab_orders = '결제 내역';
$lang->zpay_tab_logs = '통신 로그';

$lang->about_zpay_config = '결제 엔진의 공통 동작을 설정합니다. 커머스·예약 등 결제가 필요한 모듈이 이 설정을 함께 씁니다.';
$lang->about_zpay_gateway = '사용할 결제수단을 켜고 키를 입력합니다. 키가 채워지지 않은 결제수단은 결제 화면에 나타나지 않습니다.';
$lang->about_zpay_logs = '결제와 관련해 오간 모든 요청과 응답입니다. 분쟁이 생겼을 때의 근거 자료이므로 보관기간을 넉넉히 두세요.';

// 결제수단 이름
$lang->gateway_toss = '토스페이먼츠';
$lang->gateway_banktransfer = '무통장입금';
$lang->gateway_inicis = 'KG이니시스';
$lang->zpay_inicis_mid = '이니시스 상점아이디 (MID)';
$lang->zpay_inicis_sign_key = 'Sign Key';
$lang->zpay_inicis_api_key = 'INIAPI Key (취소용)';
$lang->zpay_inicis_key_help = '이니시스 가맹점관리자에서 발급받습니다. 취소·환불은 INIAPI 라 키가 따로 있습니다. 테스트 모드에서는 테스트 상점(INIpayTest)의 키를 넣으세요.';
$lang->gateway_kcp = 'NHN KCP';
$lang->zpay_kcp_site_cd = 'KCP 사이트코드 (site_cd)';
$lang->zpay_kcp_cert_info = '서비스 인증서 (PEM 본문)';
$lang->zpay_kcp_priv_key = '상점 개인키 (PEM 본문)';
$lang->zpay_kcp_priv_pass = '개인키 비밀번호';
$lang->zpay_kcp_key_help = 'KCP 관리자 인증센터에서 발급받은 PEM 파일 내용을 그대로 붙여 넣으세요. 개인키는 취소·환불 서명에만 쓰입니다. 테스트 모드에서는 테스트 사이트코드(T0000)와 개발자센터의 테스트 인증서를 넣으세요.';
$lang->gateway_nicepay = '나이스페이';
$lang->zpay_nicepay_client_id = '나이스페이 Client ID';
$lang->zpay_nicepay_secret_key = 'Secret Key';
$lang->zpay_nicepay_key_help = '나이스페이 개발자센터(developers.nicepay.co.kr)에서 발급받습니다. 테스트 모드에서는 샌드박스 키를, 운영에서는 계약 후 발급받은 운영 키를 넣으세요.';
$lang->gateway_portone = '포트원';
$lang->zpay_portone_store_id = '포트원 Store ID';
$lang->zpay_portone_channel_key = '채널 키';
$lang->zpay_portone_api_secret = 'V2 API Secret';
$lang->zpay_portone_key_help = '포트원 콘솔(portone.io)에서 발급받습니다. 어느 PG로 결제할지는 콘솔의 채널 설정에서 정합니다. 테스트는 테스트 채널의 키를 넣으세요.';
$lang->gateway_paypal = '페이팔';
$lang->zpay_paypal_client_id = '페이팔 Client ID';
$lang->zpay_paypal_secret = '페이팔 Secret';
$lang->zpay_paypal_key_help = '페이팔 개발자 콘솔(developer.paypal.com)의 앱에서 발급받습니다. 테스트 모드에서는 샌드박스 앱 키를, 운영에서는 라이브 앱 키를 넣으세요.';
$lang->zpay_paypal_currency = '페이팔 결제 통화';
$lang->zpay_paypal_currency_help = '페이팔은 KRW 결제를 지원하지 않습니다. 주문 금액을 이 통화로 환산해 결제합니다.';
$lang->zpay_paypal_exchange_rate = '적용 환율';
$lang->zpay_paypal_exchange_rate_help = '결제 통화 1단위당 원화 금액입니다. 예: USD 기준 1350. 환불은 결제 당시 환율로 처리되며, 환율 변동 차액은 상점이 부담합니다.';
$lang->zpay_exchange_rates = '공용 환율';
$lang->zpay_exchange_rates_help = '1 통화당 원화 금액입니다. 짓미페이 결제 환산과 커머스 다통화 가격이 함께 참조합니다. 주문에는 결제 시점 환율이 저장됩니다.';
$lang->zpay_fx_no_active = '추가 결제 통화가 없습니다. 기본 설정에서 통화를 선택하면 여기에 환율 행이 자동으로 나타납니다.';
$lang->zpay_fx_auto_ph = '자동 갱신됨';
$lang->zpay_fx_code = '통화 코드';
$lang->zpay_fx_rate = '환율 (KRW)';
$lang->zpay_fx_manual = '수동 고정';
$lang->zpay_fx_manual_short = '고정';
$lang->zpay_exchange_auto = '자동 갱신';
$lang->zpay_exchange_auto_label = '하루 1회 자동 갱신';
$lang->zpay_exchange_auto_help = '결제·조회 경로에서 하루 1회 갱신합니다. 수동 고정을 체크한 통화는 자동 갱신이 덮지 않습니다.';
$lang->zpay_fx_source_erapi = 'open.er-api.com (키 불필요)';
$lang->zpay_fx_source_koreaexim = '한국수출입은행 (API 키 필요)';
$lang->zpay_fx_api_key = 'API 키';
$lang->zpay_fx_updated = '마지막 갱신';
$lang->zpay_paypal_rate_shared_help = '위 공용 환율의 해당 통화 값을 사용합니다.';

// 기본 설정
$lang->zpay_enabled = '결제 사용';
$lang->zpay_enabled_help = '끄면 새로운 결제를 받지 않습니다.';
$lang->zpay_test_mode = '테스트 모드';
$lang->zpay_test_mode_help = 'PG 가 발급한 테스트 키를 쓰는 중임을 표시합니다. 실제 결제가 일어나지 않습니다.';
$lang->zpay_currency = '결제 통화';
$lang->zpay_currency_help = '사이트 전체의 기준 통화입니다. 상품 가격 입력, 결제, 적립금, 통계가 전부 이 통화로 움직입니다. 예: KRW, USD, MXN. 이 통화를 지원하는 결제수단만 결제 화면에 나타납니다.';
$lang->zpay_extra_currencies = '추가 결제 통화';
$lang->zpay_extra_currencies_help = '기준 통화 외에 병행 표시·결제를 허용할 통화입니다. 결제수단 탭의 공용 환율(자동 갱신 권장)로 교차 환산하며, 상품에 통화별 가격을 직접 등록하면 그 값이 우선합니다. 쿠폰·적립금은 기준 통화 주문에서만 쓸 수 있습니다.';
$lang->zpay_order_prefix = '주문번호 접두사';
$lang->zpay_order_prefix_help = 'PG 관리자에서 우리 주문을 알아보기 위한 표시입니다. 영문·숫자 8자 이내.';

$lang->zpay_group_cancel = '취소·환불';
$lang->zpay_allow_partial_cancel = '부분취소 허용';
$lang->zpay_allow_partial_cancel_help = '결제 금액의 일부만 환불할 수 있게 합니다.';
$lang->zpay_cancel_reasons = '취소 사유 목록';
$lang->zpay_cancel_reasons_help = '한 줄에 하나씩 적습니다. 비워 두면 사유를 직접 입력합니다.';

$lang->zpay_group_notify = '알림';
$lang->zpay_notify_admin_email = '관리자 알림 메일';
$lang->zpay_notify_events = '알림 받을 시점';
$lang->zpay_notify_on_paid = '결제 완료';
$lang->zpay_notify_on_cancel = '결제 취소';

$lang->zpay_group_security = '보안·로그';
$lang->zpay_log_retention_days = '로그 보관기간(일)';
$lang->zpay_log_retention_days_help = '0 이면 지우지 않습니다. 분쟁 대응 자료이므로 짧게 두지 마세요.';
$lang->zpay_webhook_ip_whitelist = '웹훅 허용 IP';
$lang->zpay_webhook_ip_whitelist_help = '한 줄에 하나씩. 비워 두면 IP 로 막지 않습니다. 끝에 * 를 붙여 대역을 지정할 수 있습니다. IP 는 보조 수단이며, 진짜 방어선은 웹훅을 받을 때마다 PG 에 다시 조회하는 절차입니다.';

$lang->zpay_group_notice = '정책 표기';
$lang->zpay_biz_notice = '결제 화면 고지 문구';
$lang->zpay_biz_notice_help = '통신판매업 신고번호 등 결제 화면 하단에 표시할 문구입니다.';

// 결제수단 설정
$lang->zpay_enabled_gateways = '사용할 결제수단';
$lang->zpay_enabled_gateways_help = '체크한 결제수단만 결제 화면에 나타납니다.';
$lang->zpay_not_configured = '키 미입력';
$lang->zpay_toss_client_key = '클라이언트 키';
$lang->zpay_toss_secret_key = '시크릿 키';
$lang->zpay_toss_key_help = '토스페이먼츠 상점관리자에서 발급받습니다. 테스트 키는 test_ 로, 운영 키는 live_ 로 시작합니다.';
$lang->zpay_webhook_url = '웹훅 주소';
$lang->zpay_webhook_url_help = 'PG 상점관리자의 웹훅 설정에 이 주소를 등록하세요. 가상계좌 입금 같은 비동기 통지를 받습니다.';
$lang->zpay_bank_accounts = '입금 계좌';
$lang->zpay_bank_accounts_help = '은행과 계좌번호가 모두 채워진 줄만 저장됩니다. 줄을 비우면 삭제됩니다.';
$lang->zpay_bank_name = '은행';
$lang->zpay_bank_account = '계좌번호';
$lang->zpay_bank_holder = '예금주';
$lang->zpay_bank_extra = '추가 항목';
$lang->zpay_bank_extra_help = '한 줄에 하나씩 "이름=값" 형식으로 적습니다. 예: 은행 코드=002, 카드번호=1234-5678';
$lang->zpay_bank_extra_ph = '은행 코드=002';
$lang->zpay_bank_due_days = '입금 기한(일)';
$lang->zpay_bank_due_days_help = '기한이 지나면 주문이 만료 처리됩니다.';

// 결제 화면
$lang->zpay_checkout_title = '결제하기';
$lang->zpay_order_summary = '주문 내역';
$lang->zpay_order_code = '결제번호';
$lang->zpay_product = '상품';
$lang->zpay_payer = '결제자';
$lang->zpay_payer_phone = '연락처';
$lang->zpay_payer_email = '이메일';
$lang->zpay_amount = '결제 금액';
$lang->zpay_select_method = '결제수단 선택';
$lang->zpay_depositor_name = '입금자명';
$lang->zpay_bank_due_notice = '주문 후 %d일 이내에 입금해 주세요. 기한이 지나면 주문이 자동으로 취소됩니다.';
$lang->zpay_pay_button = '%s 결제하기';

// 결제 결과
$lang->zpay_result_paid = '결제가 완료되었습니다';
$lang->zpay_result_pending = '입금을 기다리고 있습니다';
$lang->zpay_result_cancelled = '결제가 취소되었습니다';
$lang->zpay_result_expired = '결제 기한이 지났습니다';
$lang->zpay_result_failed = '결제에 실패했습니다';
$lang->zpay_bank_guide_title = '입금 안내';
$lang->zpay_due_date = '입금 기한';
$lang->zpay_receipt = '영수증 보기';
$lang->zpay_back_to_shop = '돌아가기';
$lang->zpay_cancelled_amount = '취소 금액';

// 결제 내역
$lang->zpay_order_detail = '결제 상세';
$lang->zpay_source = '결제 대상';
$lang->zpay_gateway = '결제수단';
$lang->zpay_pg_tid = 'PG 거래번호';
$lang->zpay_status = '상태';
$lang->zpay_regdate = '등록일';
$lang->zpay_paid_date = '결제일';
$lang->zpay_cancelled_date = '취소일';
$lang->zpay_ipaddress = 'IP 주소';
$lang->zpay_remain_amount = '남은 금액';
$lang->zpay_total_orders = '전체 %s건';
$lang->zpay_no_orders = '결제 내역이 없습니다.';
$lang->zpay_filter_all_status = '전체 상태';
$lang->zpay_confirm_deposit = '입금 확인';
$lang->zpay_confirm_deposit_ask = '입금을 확인하셨습니까? 확인 처리하면 결제완료로 바뀝니다.';
$lang->zpay_confirm_deposit_help = '통장에서 입금을 확인한 뒤 눌러 주세요. 누르는 즉시 결제가 완료 처리되고 요청한 모듈에 통지됩니다.';
$lang->zpay_cancel_payment = '결제 취소';
$lang->zpay_cancel_amount = '취소 금액';
$lang->zpay_cancel_amount_help = '남은 금액보다 큰 금액은 취소할 수 없습니다.';
$lang->zpay_cancel_reason = '취소 사유';

// 상태
$lang->zpay_status_ready = '결제 대기';
$lang->zpay_status_pending = '입금 대기';
$lang->zpay_status_paid = '결제 완료';
$lang->zpay_status_cancelled = '취소';
$lang->zpay_status_partial_cancelled = '부분취소';
$lang->zpay_status_failed = '실패';
$lang->zpay_status_expired = '만료';

// 로그
$lang->zpay_communication_log = '통신 로그';
$lang->zpay_log_action = '동작';
$lang->zpay_log_result = '결과';
$lang->zpay_log_response = '응답';
$lang->zpay_log_depositor = '입금자';
$lang->zpay_log_due = '입금기한';
$lang->zpay_no_logs = '기록이 없습니다.';
$lang->zpay_total_logs = '전체 %s건';
$lang->zpay_filter_all_action = '전체 동작';
$lang->zpay_filter_all_result = '전체 결과';
$lang->zpay_result_success = '성공';
$lang->zpay_result_fail = '실패';
$lang->zpay_purge_logs = '%d일 지난 로그 지우기';

// 안내·오류 메시지
$lang->msg_pay_disabled = '결제 기능이 꺼져 있습니다.';
$lang->msg_invalid_source = '결제 대상이 올바르지 않습니다.';
$lang->msg_invalid_amount = '결제 금액이 올바르지 않습니다.';
$lang->msg_order_not_found = '결제 주문을 찾을 수 없습니다.';
$lang->msg_no_gateway_available = '사용할 수 있는 결제수단이 없습니다. 관리자에게 문의해 주세요.';
$lang->msg_gateway_not_found = '결제수단을 찾을 수 없습니다.';
$lang->msg_invalid_ticket = '결제 정보가 만료되었습니다. 처음부터 다시 시도해 주세요.';
$lang->msg_already_settled = '이미 처리가 끝난 결제입니다.';
$lang->msg_too_many_requests = '결제 시도가 너무 많습니다. 잠시 후 다시 시도해 주세요.';

$lang->msg_approve_success = '결제가 승인되었습니다.';
$lang->msg_approve_failed = '결제 승인에 실패했습니다.';
$lang->msg_payment_cancelled = '결제가 취소되었습니다.';
$lang->msg_payment_not_completed = '완료되지 않은 결제입니다.';
$lang->msg_amount_mismatch = '결제 금액이 주문 금액과 일치하지 않아 결제를 중단했습니다.';
$lang->msg_missing_payment_key = 'PG 거래번호가 없습니다.';
$lang->msg_unknown_pg_status = '알 수 없는 결제 상태입니다.';
$lang->msg_pg_error = 'PG 통신 중 오류가 발생했습니다.';
$lang->msg_pg_unreachable = 'PG 서버에 연결할 수 없습니다.';
$lang->msg_paypal_auth_failed = '페이팔 인증에 실패했습니다. 지금 %s 로 연결하고 있습니다. 키가 이 모드의 것인지 확인해 주세요.';
$lang->paypal_mode_sandbox = '샌드박스(테스트)';
$lang->paypal_mode_live = '실거래';
$lang->zpay_paypal_mode = '연결 대상';
$lang->zpay_paypal_mode_help = '테스트 모드를 켜면 샌드박스로, 끄면 실거래로 연결합니다. 키도 같은 쪽 것이어야 합니다. 테스트 모드는 기본 설정에서 바꿉니다.';
$lang->zpay_paypal_test = '연결 확인';
$lang->zpay_paypal_testing = '확인 중...';
$lang->msg_paypal_test_ok = '페이팔에 정상적으로 연결됩니다.';
$lang->msg_paypal_test_empty = '클라이언트 ID와 시크릿을 먼저 입력해 주세요.';
$lang->msg_query_not_supported = '이 결제수단은 조회를 지원하지 않습니다.';

$lang->msg_cancel_success = '결제가 취소되었습니다.';
$lang->msg_cancel_failed = '결제 취소에 실패했습니다.';
$lang->msg_not_cancellable = '취소할 수 있는 상태가 아닙니다.';
$lang->msg_invalid_cancel_amount = '취소 금액이 올바르지 않습니다.';
$lang->msg_partial_cancel_disabled = '부분취소가 허용되지 않습니다.';
$lang->msg_cancel_record_failed = 'PG 취소는 처리되었으나 내역 반영에 실패했습니다. 관리자에게 문의해 주세요.';
$lang->cancel_default_reason = '고객 요청';

$lang->msg_no_bank_account = '등록된 입금 계좌가 없습니다.';
$lang->msg_bank_registered = '입금 계좌가 안내되었습니다. 기한 내에 입금해 주세요.';
$lang->msg_bank_manual_refund = '환불은 관리자가 직접 송금해야 합니다.';
$lang->msg_not_pending = '입금 대기 상태의 주문이 아닙니다.';
$lang->msg_deposit_confirmed = '입금이 확인되어 결제가 완료 처리되었습니다.';
$lang->msg_log_retention_disabled = '로그 보관기간이 0 으로 설정되어 있어 지우지 않습니다.';

// 구매확정 · 수동 환불
$lang->zpay_status_confirmed = '구매확정';
$lang->zpay_confirm_date = '구매확정일';
$lang->zpay_auto_cancel_days = 'PG 자동취소 기한(일)';
$lang->zpay_auto_cancel_days_help = '결제 후 이 기간이 지나면 PG 취소를 시도하지 않고 수동 환불로 넘깁니다. 정산이 끝나면 카드 취소가 막히기 때문입니다. 0 이면 제한하지 않습니다.';
$lang->zpay_allow_force_cancel = '확정 건 강제취소 허용';
$lang->zpay_allow_force_cancel_help = '구매확정된 결제도 관리자가 취소할 수 있게 합니다. 끄면 확정 후에는 어떤 경로로도 취소되지 않습니다.';
$lang->zpay_force_cancel = '강제취소';
$lang->zpay_force_cancel_confirm = '확정된 결제임을 알고 있으며 취소합니다';
$lang->zpay_force_cancel_help = '이미 구매확정된 결제입니다. 취소하려면 아래에서 강제취소를 명시적으로 선택해야 합니다.';
$lang->zpay_no_auto_cancel_help = '이 결제수단은 자동 취소가 불가능합니다. 취소하면 장부만 정리되고, 실제 송금은 관리자가 직접 해야 합니다.';
$lang->zpay_manual_refund_title = '수동 환불';
$lang->zpay_manual_refund_help = '취소는 처리되었지만 아직 돈이 나가지 않았습니다. 아래 금액을 계좌로 보낸 뒤 완료를 눌러 주세요.';
$lang->zpay_manual_refund_done = '송금 완료 처리';
$lang->zpay_manual_refund_sent = '송금 완료';
$lang->zpay_pending_refund_notice = '송금하지 않은 환불이 %s건 있습니다. 확인해 주세요.';

$lang->msg_confirm_success = '구매확정 처리되었습니다.';
$lang->msg_already_confirmed = '이미 구매확정된 결제입니다.';
$lang->msg_not_confirmable = '구매확정할 수 있는 상태가 아닙니다.';
$lang->msg_confirmed_not_cancellable = '구매확정된 결제는 취소할 수 없습니다. 관리자 강제취소가 필요합니다.';
$lang->msg_force_cancel_disabled = '확정 건 강제취소가 허용되어 있지 않습니다.';
$lang->msg_cancel_manual_refund_queued = '취소되었습니다. 이 결제수단은 자동 환불이 불가능하므로 관리자가 직접 송금해야 합니다.';
$lang->msg_no_pending_refund = '송금 대기 중인 환불이 없습니다.';
$lang->msg_refund_completed = '송금 완료로 기록했습니다.';
$lang->zpay_paypal_allow_krw = '원화 주문 환산 결제';
$lang->zpay_paypal_allow_krw_label = '원화 주문도 페이팔로 결제할 수 있게 한다';
$lang->zpay_paypal_allow_krw_help = '페이팔은 원화를 정산하지 않습니다. 이 항목을 켜면 원화 주문을 위에서 고른 결제 통화로 환산해 보냅니다. 구매자에게는 외화 결제창이 뜨고, 환불 시점의 환율에 따라 실제 반환액이 주문 금액과 달라질 수 있습니다. 해외 판매를 하신다면 상품 자체를 외화로 파는 쪽을 권합니다. 환율이 설정되어 있어야 동작합니다.';
