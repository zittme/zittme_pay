<?php

$lang->zittme_pay = 'Zittme Pay';

// Удирдлагын таб
$lang->zpay_tab_config = 'Үндсэн';
$lang->zpay_tab_gateway = 'Төлбөрийн хэрэгсэл';
$lang->zpay_tab_orders = 'Төлбөрийн түүх';
$lang->zpay_tab_logs = 'Холбооны бүртгэл';

$lang->about_zpay_config = 'Төлбөрийн системийн нийтлэг үйлдлийг тохируулна. Худалдаа, захиалга зэрэг төлбөр шаардах модулиуд эдгээр тохиргоог хамтран ашиглана.';
$lang->about_zpay_gateway = 'Хэрэглэх төлбөрийн хэрэгслийг идэвхжүүлж түлхүүрийг оруулна. Түлхүүр нь бөглөгдөөгүй хэрэгсэл төлбөрийн хуудсанд харагдахгүй.';
$lang->about_zpay_logs = 'Төлбөртэй холбоотой илгээсэн, хүлээн авсан бүх хүсэлт ба хариу. Маргаан гарвал энэ нь таны нотолгоо тул хангалттай урт хугацаагаар хадгална уу.';

// Хэрэгслийн нэр
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Банкны шилжүүлэг';
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

// Үндсэн
$lang->zpay_enabled = 'Төлбөрийг идэвхжүүлэх';
$lang->zpay_enabled_help = 'Унтраавал шинэ төлбөр хүлээж авахаа болино.';
$lang->zpay_test_mode = 'Туршилтын горим';
$lang->zpay_test_mode_help = 'Үйлчилгээ үзүүлэгчийн туршилтын түлхүүр ашиглаж байгааг илэрхийлнэ. Бодит мөнгө шилжихгүй.';
$lang->zpay_currency = 'Валют';
$lang->zpay_order_prefix = 'Захиалгын дугаарын угтвар';
$lang->zpay_order_prefix_help = 'Үйлчилгээ үзүүлэгчийн удирдлагад өөрийн захиалгыг таниход тусална. Үсэг, тоо, 8 тэмдэгтээс хэтрэхгүй.';

$lang->zpay_group_cancel = 'Цуцлалт ба буцаалт';
$lang->zpay_allow_partial_cancel = 'Хэсэгчилсэн цуцлалт зөвшөөрөх';
$lang->zpay_allow_partial_cancel_help = 'Төлсөн дүнгийн зөвхөн нэг хэсгийг буцаах боломжтой болгоно.';
$lang->zpay_cancel_reasons = 'Цуцлах шалтгааны жагсаалт';
$lang->zpay_cancel_reasons_help = 'Мөр тутамд нэг. Хоосон орхивол тухай бүрт гараар бичнэ.';

$lang->zpay_group_notify = 'Мэдэгдэл';
$lang->zpay_notify_admin_email = 'Админы и-мэйл';
$lang->zpay_notify_events = 'Хэзээ мэдэгдэх';
$lang->zpay_notify_on_paid = 'Төлбөр амжилттай болоход';
$lang->zpay_notify_on_cancel = 'Төлбөр цуцлагдахад';

$lang->zpay_group_security = 'Аюулгүй байдал ба бүртгэл';
$lang->zpay_log_retention_days = 'Бүртгэл хадгалах хугацаа (хоног)';
$lang->zpay_log_retention_days_help = '0 бол хэзээ ч устгахгүй. Хэт богино тавьж болохгүй — маргааны үед нотолгоо болно.';
$lang->zpay_webhook_ip_whitelist = 'Webhook зөвшөөрөх IP';
$lang->zpay_webhook_ip_whitelist_help = 'Мөр тутамд нэг. Хоосон бол IP-ээр хязгаарлахгүй. Төгсгөлд нь * тавьж мужийг зааж болно. IP шүүлт бол зөвхөн нэмэлт арга; жинхэнэ хамгаалалт нь webhook ирэх бүрт үйлчилгээ үзүүлэгчээс дахин лавлах явдал юм.';

$lang->zpay_group_notice = 'Хууль эрх зүйн мэдэгдэл';
$lang->zpay_biz_notice = 'Төлбөрийн хуудасны доод мэдэгдэл';
$lang->zpay_biz_notice_help = 'Бүртгэлийн дугаар зэрэг төлбөрийн хуудасны доор харуулах бичвэр.';

// Хэрэгслийн тохиргоо
$lang->zpay_enabled_gateways = 'Идэвхтэй төлбөрийн хэрэгсэл';
$lang->zpay_enabled_gateways_help = 'Зөвхөн сонгосон хэрэгсэл төлбөрийн хуудсанд харагдана.';
$lang->zpay_not_configured = 'түлхүүр дутуу';
$lang->zpay_toss_client_key = 'Клиент түлхүүр';
$lang->zpay_toss_secret_key = 'Нууц түлхүүр';
$lang->zpay_toss_key_help = 'Toss Payments-ийн худалдаачийн удирдлагаас авна. Туршилтын түлхүүр test_, бодит түлхүүр live_ гэж эхэлнэ.';
$lang->zpay_webhook_url = 'Webhook хаяг';
$lang->zpay_webhook_url_help = 'Виртуал дансны орлого зэрэг асинхрон мэдэгдэл хүлээн авахын тулд энэ хаягийг үйлчилгээ үзүүлэгчийн удирдлагад бүртгүүлнэ үү.';
$lang->zpay_bank_accounts = 'Хүлээн авах данс';
$lang->zpay_bank_accounts_help = 'Банк ба дансны дугаар хоёулаа бөглөгдсөн мөрийг л хадгална. Мөрийг хоослоход устана.';
$lang->zpay_bank_name = 'Банк';
$lang->zpay_bank_account = 'Дансны дугаар';
$lang->zpay_bank_holder = 'Данс эзэмшигч';
$lang->zpay_bank_due_days = 'Төлөх хугацаа (хоног)';
$lang->zpay_bank_due_days_help = 'Хугацаа өнгөрвөл захиалга дуусгавар болно.';

// Төлбөрийн хуудас
$lang->zpay_checkout_title = 'Төлбөр хийх';
$lang->zpay_order_summary = 'Захиалгын хураангуй';
$lang->zpay_order_code = 'Захиалгын дугаар';
$lang->zpay_product = 'Бараа';
$lang->zpay_payer = 'Төлөгч';
$lang->zpay_payer_phone = 'Утас';
$lang->zpay_payer_email = 'И-мэйл';
$lang->zpay_amount = 'Дүн';
$lang->zpay_select_method = 'Төлбөрийн хэрэгсэл сонгох';
$lang->zpay_depositor_name = 'Шилжүүлэгчийн нэр';
$lang->zpay_bank_due_notice = 'Захиалснаас хойш %d хоногийн дотор шилжүүлнэ үү. Хугацаа өнгөрвөл захиалга автоматаар цуцлагдана.';
$lang->zpay_pay_button = '%s төлөх';

// Үр дүн
$lang->zpay_result_paid = 'Төлбөр амжилттай';
$lang->zpay_result_pending = 'Таны шилжүүлгийг хүлээж байна';
$lang->zpay_result_cancelled = 'Төлбөр цуцлагдсан';
$lang->zpay_result_expired = 'Төлөх хугацаа өнгөрсөн';
$lang->zpay_result_failed = 'Төлбөр амжилтгүй';
$lang->zpay_bank_guide_title = 'Шилжүүлгийн мэдээлэл';
$lang->zpay_due_date = 'Төлөх эцсийн хугацаа';
$lang->zpay_receipt = 'Баримт харах';
$lang->zpay_back_to_shop = 'Буцах';
$lang->zpay_cancelled_amount = 'Цуцалсан дүн';

// Жагсаалт
$lang->zpay_order_detail = 'Төлбөрийн дэлгэрэнгүй';
$lang->zpay_source = 'Төлбөрийн зүйл';
$lang->zpay_gateway = 'Хэрэгсэл';
$lang->zpay_pg_tid = 'Гүйлгээний дугаар';
$lang->zpay_status = 'Төлөв';
$lang->zpay_regdate = 'Үүсгэсэн';
$lang->zpay_paid_date = 'Төлсөн';
$lang->zpay_cancelled_date = 'Цуцалсан';
$lang->zpay_ipaddress = 'IP хаяг';
$lang->zpay_remain_amount = 'үлдэгдэл';
$lang->zpay_total_orders = 'Нийт %s';
$lang->zpay_no_orders = 'Одоогоор төлбөр байхгүй.';
$lang->zpay_filter_all_status = 'Бүх төлөв';
$lang->zpay_confirm_deposit = 'Орлогыг баталгаажуулах';
$lang->zpay_confirm_deposit_help = 'Мөнгө орсныг шалгасны дараа дарна уу. Дарсан даруйд төлбөр дууссанд тооцогдож, хүсэлт гаргасан модульд мэдэгдэнэ.';
$lang->zpay_cancel_payment = 'Төлбөр цуцлах';
$lang->zpay_cancel_amount = 'Цуцлах дүн';
$lang->zpay_cancel_amount_help = 'Үлдэгдлээс их дүнг цуцлах боломжгүй.';
$lang->zpay_cancel_reason = 'Шалтгаан';

// Төлөв
$lang->zpay_status_ready = 'Төлбөр хүлээж байна';
$lang->zpay_status_pending = 'Орлого хүлээж байна';
$lang->zpay_status_paid = 'Төлөгдсөн';
$lang->zpay_status_cancelled = 'Цуцлагдсан';
$lang->zpay_status_partial_cancelled = 'Хэсэгчлэн цуцлагдсан';
$lang->zpay_status_failed = 'Амжилтгүй';
$lang->zpay_status_expired = 'Хугацаа дууссан';

// Бүртгэл
$lang->zpay_communication_log = 'Холбооны бүртгэл';
$lang->zpay_log_action = 'Үйлдэл';
$lang->zpay_log_result = 'Үр дүн';
$lang->zpay_log_response = 'Хариу';
$lang->zpay_no_logs = 'Бүртгэл алга.';
$lang->zpay_total_logs = 'Нийт %s бүртгэл';
$lang->zpay_filter_all_action = 'Бүх үйлдэл';
$lang->zpay_filter_all_result = 'Бүх үр дүн';
$lang->zpay_result_success = 'Амжилттай';
$lang->zpay_result_fail = 'Амжилтгүй';
$lang->zpay_purge_logs = '%d хоногоос хуучин бүртгэлийг устгах';

// Мэдэгдэл
$lang->msg_pay_disabled = 'Төлбөрийн үйлдэл унтраалттай байна.';
$lang->msg_invalid_source = 'Төлбөрийн зүйл буруу байна.';
$lang->msg_invalid_amount = 'Төлбөрийн дүн буруу байна.';
$lang->msg_order_not_found = 'Төлбөрийн захиалга олдсонгүй.';
$lang->msg_no_gateway_available = 'Ашиглах боломжтой төлбөрийн хэрэгсэл алга. Сайтын админд хандана уу.';
$lang->msg_gateway_not_found = 'Төлбөрийн хэрэгсэл олдсонгүй.';
$lang->msg_invalid_ticket = 'Төлбөрийн мэдээллийн хугацаа дууссан. Эхнээс нь дахин оролдоно уу.';
$lang->msg_already_settled = 'Энэ төлбөр аль хэдийн боловсруулагдсан байна.';
$lang->msg_too_many_requests = 'Хэт олон удаа төлбөр оролдлоо. Түр хүлээгээд дахин оролдоно уу.';

$lang->msg_approve_success = 'Төлбөр баталгаажлаа.';
$lang->msg_approve_failed = 'Төлбөрийг баталгаажуулж чадсангүй.';
$lang->msg_payment_cancelled = 'Төлбөр цуцлагдлаа.';
$lang->msg_payment_not_completed = 'Энэ төлбөр дуусаагүй байна.';
$lang->msg_amount_mismatch = 'Дүн захиалгынхтай таарахгүй тул төлбөрийг зогсоолоо.';
$lang->msg_missing_payment_key = 'Гүйлгээний дугаар алга.';
$lang->msg_unknown_pg_status = 'Тодорхойгүй төлбөрийн төлөв.';
$lang->msg_pg_error = 'Төлбөрийн үйлчилгээнээс алдаа буцаалаа.';
$lang->msg_pg_unreachable = 'Төлбөрийн серверт холбогдож чадсангүй.';
$lang->msg_query_not_supported = 'Энэ төлбөрийн хэрэгсэл лавлагааг дэмждэггүй.';

$lang->msg_cancel_success = 'Төлбөр цуцлагдлаа.';
$lang->msg_cancel_failed = 'Төлбөрийг цуцалж чадсангүй.';
$lang->msg_not_cancellable = 'Одоогийн төлөвт цуцлах боломжгүй.';
$lang->msg_invalid_cancel_amount = 'Цуцлах дүн буруу байна.';
$lang->msg_partial_cancel_disabled = 'Хэсэгчилсэн цуцлалт зөвшөөрөгдөөгүй.';
$lang->msg_cancel_record_failed = 'Үйлчилгээ үзүүлэгч цуцалсан боловч манай бүртгэлд тусгаж чадсангүй. Сайтын админд хандана уу.';
$lang->cancel_default_reason = 'Үйлчлүүлэгчийн хүсэлт';

$lang->msg_no_bank_account = 'Бүртгэсэн данс алга.';
$lang->msg_bank_registered = 'Дансны мэдээллийг доор харууллаа. Хугацаанд нь шилжүүлнэ үү.';
$lang->msg_bank_manual_refund = 'Банкны шилжүүлгийн буцаалтыг админ гараар хийх шаардлагатай.';
$lang->msg_not_pending = 'Энэ захиалга орлого хүлээх төлөвт байхгүй.';
$lang->msg_deposit_confirmed = 'Орлого баталгаажиж, төлбөр дууслаа.';
$lang->msg_log_retention_disabled = 'Бүртгэл хадгалах хугацаа 0 тул юу ч устгахгүй.';

// Худалдан авалт баталгаажуулалт ба гар аргаар буцаалт
$lang->zpay_status_confirmed = 'Баталгаажсан';
$lang->zpay_confirm_date = 'Баталгаажсан огноо';
$lang->zpay_auto_cancel_days = 'Системээр цуцлах хугацаа (хоног)';
$lang->zpay_auto_cancel_days_help = 'Төлбөрөөс хойш энэ хугацаа өнгөрвөл төлбөрийн системээр цуцлахыг оролдохгүй, гар аргаар буцаана. Тооцоо хийгдсэний дараа картын цуцлалт хаагддаг. 0 бол хязгаарлахгүй.';
$lang->zpay_allow_force_cancel = 'Албадан цуцлахыг зөвшөөрөх';
$lang->zpay_allow_force_cancel_help = 'Баталгаажсан төлбөрийг ч админ цуцлах боломжтой болгоно. Унтраавал баталгаажуулалт эцсийн төлөв болно.';
$lang->zpay_force_cancel = 'Албадан цуцлах';
$lang->zpay_force_cancel_confirm = 'Энэ төлбөр баталгаажсаныг мэдэж байгаа бөгөөд цуцална';
$lang->zpay_force_cancel_help = 'Энэ төлбөр аль хэдийн баталгаажсан. Цуцлахын тулд доор албадан цуцлахыг тодорхой сонгоно уу.';
$lang->zpay_no_auto_cancel_help = 'Энэ төлбөрийн хэрэгслийг автоматаар цуцлах боломжгүй. Цуцлахад зөвхөн бүртгэл цэгцлэгдэх бөгөөд мөнгийг админ өөрөө шилжүүлнэ.';
$lang->zpay_manual_refund_title = 'Гар аргаар буцаалт';
$lang->zpay_manual_refund_help = 'Цуцлалт бүртгэгдсэн ч мөнгө хараахан шилжээгүй. Доорх дүнг шилжүүлээд дуусгах товчийг дарна уу.';
$lang->zpay_manual_refund_done = 'Шилжүүлсэн гэж тэмдэглэх';
$lang->zpay_manual_refund_sent = 'Шилжүүлсэн';
$lang->zpay_pending_refund_notice = 'Шилжүүлээгүй %s буцаалт байна. Шалгана уу.';

$lang->msg_confirm_success = 'Худалдан авалт баталгаажлаа.';
$lang->msg_already_confirmed = 'Энэ төлбөр аль хэдийн баталгаажсан байна.';
$lang->msg_not_confirmable = 'Одоогийн төлөвт баталгаажуулах боломжгүй.';
$lang->msg_confirmed_not_cancellable = 'Баталгаажсан төлбөрийг цуцлах боломжгүй. Админы албадан цуцлалт шаардлагатай.';
$lang->msg_force_cancel_disabled = 'Баталгаажсан төлбөрийг албадан цуцлахыг зөвшөөрөөгүй.';
$lang->msg_cancel_manual_refund_queued = 'Цуцлагдлаа. Энэ хэрэгсэл автоматаар буцаах боломжгүй тул админ өөрөө шилжүүлэх шаардлагатай.';
$lang->msg_no_pending_refund = 'Шилжүүлэхийг хүлээж буй буцаалт алга.';
$lang->msg_refund_completed = 'Шилжүүлсэн гэж бүртгэлээ.';
