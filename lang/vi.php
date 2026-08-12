<?php

$lang->zittme_pay = 'Zittme Pay';

// Thẻ quản trị
$lang->zpay_tab_config = 'Cơ bản';
$lang->zpay_tab_gateway = 'Phương thức thanh toán';
$lang->zpay_tab_orders = 'Lịch sử thanh toán';
$lang->zpay_tab_logs = 'Nhật ký giao tiếp';

$lang->about_zpay_config = 'Thiết lập hành vi chung của công cụ thanh toán. Các module cần thanh toán như thương mại hay đặt chỗ đều dùng chung thiết lập này.';
$lang->about_zpay_gateway = 'Bật phương thức thanh toán bạn cần và nhập khóa. Phương thức chưa có khóa sẽ không hiện trên trang thanh toán.';
$lang->about_zpay_logs = 'Toàn bộ yêu cầu và phản hồi đã trao đổi cho việc thanh toán. Đây là bằng chứng khi có tranh chấp, hãy giữ đủ lâu.';

// Tên phương thức
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Chuyển khoản ngân hàng';
$lang->gateway_inicis = 'KG INICIS';
$lang->zpay_inicis_mid = 'INICIS merchant ID (MID)';
$lang->zpay_inicis_sign_key = 'Sign Key';
$lang->zpay_inicis_api_key = 'INIAPI Key (for refunds)';
$lang->zpay_inicis_key_help = 'Issued from the INICIS merchant admin. Refunds use a separate INIAPI key. In test mode, use the test merchant (INIpayTest) keys.';
$lang->gateway_kcp = 'NHN KCP';
$lang->zpay_kcp_site_cd = 'KCP site code (site_cd)';
$lang->zpay_kcp_cert_info = 'Service certificate (PEM body)';
$lang->zpay_kcp_priv_key = 'Merchant private key (PEM body)';
$lang->zpay_kcp_priv_pass = 'Private key password';
$lang->zpay_kcp_key_help = 'Paste the PEM file contents issued from the KCP admin certificate center. The private key is used only to sign refund requests. In test mode, use the test site code (T0000) and the developer center test certificate.';
$lang->gateway_nicepay = 'NICE Pay';
$lang->zpay_nicepay_client_id = 'NICE Pay Client ID';
$lang->zpay_nicepay_secret_key = 'Secret Key';
$lang->zpay_nicepay_key_help = 'Issued from the NICE Pay developer center (developers.nicepay.co.kr). Use sandbox keys in test mode and production keys issued after the merchant contract in production.';
$lang->gateway_portone = 'PortOne';
$lang->zpay_portone_store_id = 'PortOne Store ID';
$lang->zpay_portone_channel_key = 'Channel key';
$lang->zpay_portone_api_secret = 'V2 API Secret';
$lang->zpay_portone_key_help = 'Issued from the PortOne console (portone.io). The actual PG is chosen in the console channel settings. Use a test channel key for testing.';
$lang->gateway_paypal = 'PayPal';
$lang->zpay_paypal_client_id = 'PayPal Client ID';
$lang->zpay_paypal_secret = 'PayPal Secret';
$lang->zpay_paypal_key_help = 'Issued from your app on the PayPal developer console (developer.paypal.com). Use sandbox app keys in test mode and live app keys in production.';
$lang->zpay_paypal_currency = 'PayPal currency';
$lang->zpay_paypal_currency_help = 'PayPal does not support KRW. Order amounts are converted to this currency.';
$lang->zpay_paypal_exchange_rate = 'Exchange rate';
$lang->zpay_paypal_exchange_rate_help = 'KRW per 1 unit of the payment currency. Example: 1350 for USD. Refunds use the rate stored at payment time; the shop bears any exchange difference.';
$lang->zpay_exchange_rates = 'Shared exchange rates';
$lang->zpay_exchange_rates_help = 'KRW per 1 unit of each currency. Used by both Zittme Pay conversion and commerce multi-currency prices. Orders store the rate at payment time.';
$lang->zpay_fx_code = 'Currency code';
$lang->zpay_fx_rate = 'Rate (KRW)';
$lang->zpay_fx_manual = 'Manual lock';
$lang->zpay_fx_manual_short = 'Lock';
$lang->zpay_exchange_auto = 'Auto update';
$lang->zpay_exchange_auto_label = 'Update once a day';
$lang->zpay_exchange_auto_help = 'Updated once a day on payment/view paths. Currencies with manual lock are not overwritten.';
$lang->zpay_fx_source_erapi = 'open.er-api.com (no key)';
$lang->zpay_fx_source_koreaexim = 'Korea Eximbank (API key required)';
$lang->zpay_fx_api_key = 'API key';
$lang->zpay_fx_updated = 'Last updated';
$lang->zpay_paypal_rate_shared_help = 'Uses the matching currency from the shared exchange rates above.';

// Cơ bản
$lang->zpay_enabled = 'Bật thanh toán';
$lang->zpay_enabled_help = 'Tắt để ngừng nhận thanh toán mới.';
$lang->zpay_test_mode = 'Chế độ thử nghiệm';
$lang->zpay_test_mode_help = 'Cho biết đang dùng khóa thử nghiệm của cổng thanh toán. Không có giao dịch thật.';
$lang->zpay_currency = 'Đơn vị tiền tệ';
$lang->zpay_order_prefix = 'Tiền tố mã đơn hàng';
$lang->zpay_order_prefix_help = 'Giúp nhận ra đơn hàng của bạn trong bảng điều khiển cổng thanh toán. Chữ và số, tối đa 8 ký tự.';

$lang->zpay_group_cancel = 'Hủy và hoàn tiền';
$lang->zpay_allow_partial_cancel = 'Cho phép hủy một phần';
$lang->zpay_allow_partial_cancel_help = 'Cho phép hoàn lại chỉ một phần số tiền đã thanh toán.';
$lang->zpay_cancel_reasons = 'Danh sách lý do hủy';
$lang->zpay_cancel_reasons_help = 'Mỗi dòng một lý do. Để trống thì nhập tay mỗi lần.';

$lang->zpay_group_notify = 'Thông báo';
$lang->zpay_notify_admin_email = 'E-mail quản trị viên';
$lang->zpay_notify_events = 'Thông báo khi';
$lang->zpay_notify_on_paid = 'Thanh toán hoàn tất';
$lang->zpay_notify_on_cancel = 'Thanh toán bị hủy';

$lang->zpay_group_security = 'Bảo mật và nhật ký';
$lang->zpay_log_retention_days = 'Thời gian lưu nhật ký (ngày)';
$lang->zpay_log_retention_days_help = 'Đặt 0 để không bao giờ xóa. Đây là bằng chứng tranh chấp, đừng đặt quá ngắn.';
$lang->zpay_webhook_ip_whitelist = 'IP được phép gửi webhook';
$lang->zpay_webhook_ip_whitelist_help = 'Mỗi dòng một IP. Để trống thì không giới hạn. Thêm * ở cuối để chỉ định dải. Lọc IP chỉ là biện pháp phụ; phòng tuyến thật sự là truy vấn lại cổng thanh toán mỗi khi nhận webhook.';

$lang->zpay_group_notice = 'Thông tin pháp lý';
$lang->zpay_biz_notice = 'Ghi chú cuối trang thanh toán';
$lang->zpay_biz_notice_help = 'Số đăng ký kinh doanh và nội dung tương tự hiển thị ở cuối trang thanh toán.';

// Thiết lập phương thức
$lang->zpay_enabled_gateways = 'Phương thức được bật';
$lang->zpay_enabled_gateways_help = 'Chỉ những phương thức được chọn mới hiện trên trang thanh toán.';
$lang->zpay_not_configured = 'thiếu khóa';
$lang->zpay_toss_client_key = 'Khóa client';
$lang->zpay_toss_secret_key = 'Khóa bí mật';
$lang->zpay_toss_key_help = 'Lấy trong bảng điều khiển của Toss Payments. Khóa thử nghiệm bắt đầu bằng test_, khóa thật bằng live_.';
$lang->zpay_webhook_url = 'Địa chỉ webhook';
$lang->zpay_webhook_url_help = 'Hãy đăng ký địa chỉ này trong phần webhook của cổng thanh toán để nhận thông báo bất đồng bộ như tiền vào tài khoản ảo.';
$lang->zpay_bank_accounts = 'Tài khoản nhận tiền';
$lang->zpay_bank_accounts_help = 'Chỉ lưu những dòng có cả ngân hàng và số tài khoản. Xóa trắng dòng để loại bỏ.';
$lang->zpay_bank_name = 'Ngân hàng';
$lang->zpay_bank_account = 'Số tài khoản';
$lang->zpay_bank_holder = 'Chủ tài khoản';
$lang->zpay_bank_due_days = 'Hạn thanh toán (ngày)';
$lang->zpay_bank_due_days_help = 'Quá hạn thì đơn hàng sẽ được xử lý là hết hạn.';

// Trang thanh toán
$lang->zpay_checkout_title = 'Thanh toán';
$lang->zpay_order_summary = 'Nội dung đơn hàng';
$lang->zpay_order_code = 'Mã đơn hàng';
$lang->zpay_product = 'Sản phẩm';
$lang->zpay_payer = 'Người thanh toán';
$lang->zpay_payer_phone = 'Điện thoại';
$lang->zpay_payer_email = 'E-mail';
$lang->zpay_amount = 'Số tiền';
$lang->zpay_select_method = 'Chọn phương thức thanh toán';
$lang->zpay_depositor_name = 'Tên người chuyển khoản';
$lang->zpay_bank_due_notice = 'Vui lòng chuyển khoản trong vòng %d ngày. Quá hạn đơn hàng sẽ tự động bị hủy.';
$lang->zpay_pay_button = 'Thanh toán %s';

// Kết quả
$lang->zpay_result_paid = 'Thanh toán hoàn tất';
$lang->zpay_result_pending = 'Đang chờ chuyển khoản';
$lang->zpay_result_cancelled = 'Thanh toán đã bị hủy';
$lang->zpay_result_expired = 'Đã quá hạn thanh toán';
$lang->zpay_result_failed = 'Thanh toán thất bại';
$lang->zpay_bank_guide_title = 'Thông tin chuyển khoản';
$lang->zpay_due_date = 'Hạn thanh toán';
$lang->zpay_receipt = 'Xem biên lai';
$lang->zpay_back_to_shop = 'Quay lại';
$lang->zpay_cancelled_amount = 'Số tiền đã hủy';

// Danh sách
$lang->zpay_order_detail = 'Chi tiết thanh toán';
$lang->zpay_source = 'Đối tượng thanh toán';
$lang->zpay_gateway = 'Phương thức';
$lang->zpay_pg_tid = 'Mã giao dịch cổng thanh toán';
$lang->zpay_status = 'Trạng thái';
$lang->zpay_regdate = 'Ngày tạo';
$lang->zpay_paid_date = 'Ngày thanh toán';
$lang->zpay_cancelled_date = 'Ngày hủy';
$lang->zpay_ipaddress = 'Địa chỉ IP';
$lang->zpay_remain_amount = 'còn lại';
$lang->zpay_total_orders = 'Tổng %s giao dịch';
$lang->zpay_no_orders = 'Chưa có giao dịch nào.';
$lang->zpay_filter_all_status = 'Tất cả trạng thái';
$lang->zpay_confirm_deposit = 'Xác nhận đã nhận tiền';
$lang->zpay_confirm_deposit_help = 'Hãy nhấn sau khi bạn thấy tiền đã vào tài khoản. Thanh toán sẽ hoàn tất ngay và module yêu cầu sẽ được thông báo.';
$lang->zpay_cancel_payment = 'Hủy thanh toán';
$lang->zpay_cancel_amount = 'Số tiền hủy';
$lang->zpay_cancel_amount_help = 'Không thể hủy nhiều hơn số tiền còn lại.';
$lang->zpay_cancel_reason = 'Lý do hủy';

// Trạng thái
$lang->zpay_status_ready = 'Chờ thanh toán';
$lang->zpay_status_pending = 'Chờ nhận tiền';
$lang->zpay_status_paid = 'Đã thanh toán';
$lang->zpay_status_cancelled = 'Đã hủy';
$lang->zpay_status_partial_cancelled = 'Hủy một phần';
$lang->zpay_status_failed = 'Thất bại';
$lang->zpay_status_expired = 'Hết hạn';

// Nhật ký
$lang->zpay_communication_log = 'Nhật ký giao tiếp';
$lang->zpay_log_action = 'Hành động';
$lang->zpay_log_result = 'Kết quả';
$lang->zpay_log_response = 'Phản hồi';
$lang->zpay_no_logs = 'Không có bản ghi.';
$lang->zpay_total_logs = 'Tổng %s bản ghi';
$lang->zpay_filter_all_action = 'Tất cả hành động';
$lang->zpay_filter_all_result = 'Tất cả kết quả';
$lang->zpay_result_success = 'Thành công';
$lang->zpay_result_fail = 'Thất bại';
$lang->zpay_purge_logs = 'Xóa nhật ký cũ hơn %d ngày';

// Thông báo
$lang->msg_pay_disabled = 'Chức năng thanh toán đang tắt.';
$lang->msg_invalid_source = 'Đối tượng thanh toán không hợp lệ.';
$lang->msg_invalid_amount = 'Số tiền thanh toán không hợp lệ.';
$lang->msg_order_not_found = 'Không tìm thấy đơn thanh toán.';
$lang->msg_no_gateway_available = 'Không có phương thức thanh toán khả dụng. Vui lòng liên hệ quản trị viên.';
$lang->msg_gateway_not_found = 'Không tìm thấy phương thức thanh toán.';
$lang->msg_invalid_ticket = 'Phiên thanh toán đã hết hạn. Vui lòng bắt đầu lại.';
$lang->msg_already_settled = 'Giao dịch này đã được xử lý xong.';
$lang->msg_too_many_requests = 'Bạn thử thanh toán quá nhiều lần. Vui lòng thử lại sau.';

$lang->msg_approve_success = 'Thanh toán đã được chấp thuận.';
$lang->msg_approve_failed = 'Không thể chấp thuận thanh toán.';
$lang->msg_payment_cancelled = 'Thanh toán đã bị hủy.';
$lang->msg_payment_not_completed = 'Giao dịch này chưa hoàn tất.';
$lang->msg_amount_mismatch = 'Đã dừng thanh toán vì số tiền không khớp với đơn hàng.';
$lang->msg_missing_payment_key = 'Thiếu mã giao dịch của cổng thanh toán.';
$lang->msg_unknown_pg_status = 'Trạng thái thanh toán không xác định.';
$lang->msg_pg_error = 'Đã xảy ra lỗi khi giao tiếp với cổng thanh toán.';
$lang->msg_pg_unreachable = 'Không thể kết nối tới máy chủ cổng thanh toán.';
$lang->msg_query_not_supported = 'Phương thức này không hỗ trợ truy vấn.';

$lang->msg_cancel_success = 'Thanh toán đã được hủy.';
$lang->msg_cancel_failed = 'Không thể hủy thanh toán.';
$lang->msg_not_cancellable = 'Trạng thái hiện tại không cho phép hủy.';
$lang->msg_invalid_cancel_amount = 'Số tiền hủy không hợp lệ.';
$lang->msg_partial_cancel_disabled = 'Không cho phép hủy một phần.';
$lang->msg_cancel_record_failed = 'Cổng thanh toán đã hủy nhưng ghi nhận tại đây thất bại. Vui lòng liên hệ quản trị viên.';
$lang->cancel_default_reason = 'Theo yêu cầu của khách hàng';

$lang->msg_no_bank_account = 'Chưa đăng ký tài khoản nhận tiền.';
$lang->msg_bank_registered = 'Thông tin tài khoản đã được hiển thị. Vui lòng chuyển khoản đúng hạn.';
$lang->msg_bank_manual_refund = 'Hoàn tiền cho chuyển khoản ngân hàng phải do quản trị viên chuyển thủ công.';
$lang->msg_not_pending = 'Đơn hàng này không ở trạng thái chờ nhận tiền.';
$lang->msg_deposit_confirmed = 'Đã xác nhận nhận tiền, thanh toán hoàn tất.';
$lang->msg_log_retention_disabled = 'Thời gian lưu nhật ký đặt là 0 nên không xóa gì cả.';

// Xác nhận mua hàng và hoàn tiền thủ công
$lang->zpay_status_confirmed = 'Đã xác nhận';
$lang->zpay_confirm_date = 'Ngày xác nhận';
$lang->zpay_auto_cancel_days = 'Thời hạn hủy qua cổng thanh toán (ngày)';
$lang->zpay_auto_cancel_days_help = 'Sau số ngày này, hệ thống không thử hủy qua cổng thanh toán nữa mà chuyển sang hoàn tiền thủ công, vì thẻ không thể hủy sau khi đã quyết toán. Đặt 0 để không giới hạn.';
$lang->zpay_allow_force_cancel = 'Cho phép hủy cưỡng chế';
$lang->zpay_allow_force_cancel_help = 'Cho phép quản trị viên hủy cả giao dịch đã xác nhận. Tắt đi thì xác nhận là trạng thái cuối cùng.';
$lang->zpay_force_cancel = 'Hủy cưỡng chế';
$lang->zpay_force_cancel_confirm = 'Tôi hiểu giao dịch này đã được xác nhận và vẫn muốn hủy';
$lang->zpay_force_cancel_help = 'Giao dịch này đã được xác nhận mua hàng. Muốn hủy, bạn phải chọn rõ hủy cưỡng chế bên dưới.';
$lang->zpay_no_auto_cancel_help = 'Phương thức này không thể hủy tự động. Việc hủy chỉ cập nhật sổ sách, tiền phải do quản trị viên chuyển thủ công.';
$lang->zpay_manual_refund_title = 'Hoàn tiền thủ công';
$lang->zpay_manual_refund_help = 'Đã ghi nhận hủy nhưng tiền chưa được chuyển. Hãy chuyển số tiền dưới đây rồi bấm hoàn tất.';
$lang->zpay_manual_refund_done = 'Đánh dấu đã chuyển';
$lang->zpay_manual_refund_sent = 'Đã chuyển tiền';
$lang->zpay_pending_refund_notice = 'Có %s khoản hoàn tiền chưa được chuyển. Vui lòng kiểm tra.';

$lang->msg_confirm_success = 'Đã xác nhận mua hàng.';
$lang->msg_already_confirmed = 'Giao dịch này đã được xác nhận trước đó.';
$lang->msg_not_confirmable = 'Trạng thái hiện tại không thể xác nhận mua hàng.';
$lang->msg_confirmed_not_cancellable = 'Giao dịch đã xác nhận không thể hủy. Cần quản trị viên hủy cưỡng chế.';
$lang->msg_force_cancel_disabled = 'Việc hủy cưỡng chế giao dịch đã xác nhận không được phép.';
$lang->msg_cancel_manual_refund_queued = 'Đã hủy. Phương thức này không hoàn tiền tự động được nên quản trị viên phải chuyển tiền thủ công.';
$lang->msg_no_pending_refund = 'Không có khoản hoàn tiền nào đang chờ chuyển.';
$lang->msg_refund_completed = 'Đã ghi nhận là đã chuyển.';
