<?php

$lang->zittme_pay = 'Zittme 支付';

// 管理分頁
$lang->zpay_tab_config = '基本';
$lang->zpay_tab_gateway = '付款方式';
$lang->zpay_tab_orders = '付款紀錄';
$lang->zpay_tab_logs = '通訊紀錄';

$lang->about_zpay_config = '設定付款引擎的共通行為。商城、預約等需要付款的模組共用這些設定。';
$lang->about_zpay_gateway = '啟用需要的付款方式並填寫金鑰。未填寫金鑰的付款方式不會出現在結帳頁面。';
$lang->about_zpay_logs = '與付款相關的所有請求與回應。這是發生爭議時的憑據，請設定足夠長的保留期限。';

// 付款方式名稱
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = '銀行轉帳';
$lang->gateway_inicis = 'KG INICIS';
$lang->zpay_inicis_mid = 'INICIS 商戶ID (MID)';
$lang->zpay_inicis_sign_key = 'Sign Key';
$lang->zpay_inicis_api_key = 'INIAPI Key (用於退款)';
$lang->zpay_inicis_key_help = '在INICIS商戶管理後台取得。取消/退款使用單獨的INIAPI金鑰。測試模式請使用測試商戶(INIpayTest)的金鑰。';
$lang->gateway_kcp = 'NHN KCP';
$lang->zpay_kcp_site_cd = 'KCP 站點代碼 (site_cd)';
$lang->zpay_kcp_cert_info = '服務憑證 (PEM內容)';
$lang->zpay_kcp_priv_key = '商戶私鑰 (PEM內容)';
$lang->zpay_kcp_priv_pass = '私鑰密碼';
$lang->zpay_kcp_key_help = '請直接貼上KCP管理員認證中心發放的PEM檔案內容。私鑰僅用於退款簽章。測試模式請使用測試站點代碼(T0000)和測試憑證。';
$lang->gateway_nicepay = 'NICE Pay';
$lang->zpay_nicepay_client_id = 'NICE Pay Client ID';
$lang->zpay_nicepay_secret_key = 'Secret Key';
$lang->zpay_nicepay_key_help = '在NICE Pay開發者中心(developers.nicepay.co.kr)取得。測試模式使用沙盒金鑰，正式環境使用簽約後發放的正式金鑰。';
$lang->gateway_portone = 'PortOne';
$lang->zpay_portone_store_id = 'PortOne Store ID';
$lang->zpay_portone_channel_key = '渠道金鑰';
$lang->zpay_portone_api_secret = 'V2 API Secret';
$lang->zpay_portone_key_help = '在PortOne控制台(portone.io)取得。實際PG在控制台渠道設定中選擇。測試時請使用測試渠道的金鑰。';
$lang->gateway_paypal = 'PayPal';
$lang->zpay_paypal_client_id = 'PayPal Client ID';
$lang->zpay_paypal_secret = 'PayPal Secret';
$lang->zpay_paypal_key_help = '在PayPal開發者控制台(developer.paypal.com)的應用程式中取得。測試模式使用沙盒金鑰，正式環境使用正式金鑰。';
$lang->zpay_paypal_currency = 'PayPal結算貨幣';
$lang->zpay_paypal_currency_help = 'PayPal不支援KRW。訂單金額將按此貨幣換算後付款。';
$lang->zpay_paypal_exchange_rate = '適用匯率';
$lang->zpay_paypal_exchange_rate_help = '每1單位結算貨幣對應的韓元金額。例如USD為1350。退款按付款時的匯率處理。';
$lang->zpay_exchange_rates = '共用匯率';
$lang->zpay_exchange_rates_help = '每1單位貨幣對應的韓元金額。付款換算與商城多貨幣價格共同參照。訂單保存付款時的匯率。';
$lang->zpay_fx_no_active = 'No additional currencies. Select currencies in Basic settings and rate rows appear here automatically.';
$lang->zpay_fx_auto_ph = 'Auto updated';
$lang->zpay_fx_code = '貨幣代碼';
$lang->zpay_fx_rate = '匯率 (KRW)';
$lang->zpay_fx_manual = '手動鎖定';
$lang->zpay_fx_manual_short = '鎖定';
$lang->zpay_exchange_auto = '自動更新';
$lang->zpay_exchange_auto_label = '每天自動更新一次';
$lang->zpay_exchange_auto_help = '在付款/瀏覽路徑上每天更新一次。手動鎖定的貨幣不會被覆蓋。';
$lang->zpay_fx_source_erapi = 'open.er-api.com (無需金鑰)';
$lang->zpay_fx_source_koreaexim = '韓國進出口銀行 (需API金鑰)';
$lang->zpay_fx_api_key = 'API金鑰';
$lang->zpay_fx_updated = '最後更新';
$lang->zpay_paypal_rate_shared_help = '使用上方共用匯率中對應貨幣的值。';

// 基本設定
$lang->zpay_enabled = '啟用付款';
$lang->zpay_enabled_help = '關閉後將不再接受新的付款。';
$lang->zpay_test_mode = '測試模式';
$lang->zpay_test_mode_help = '表示目前使用金流的測試金鑰，不會實際扣款。';
$lang->zpay_currency = '結帳貨幣';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = '訂單編號前綴';
$lang->zpay_order_prefix_help = '方便在金流後台辨認本站訂單。英數字，最多 8 個字元。';

$lang->zpay_group_cancel = '取消與退款';
$lang->zpay_allow_partial_cancel = '允許部分取消';
$lang->zpay_allow_partial_cancel_help = '允許只退還部分已付金額。';
$lang->zpay_cancel_reasons = '取消原因清單';
$lang->zpay_cancel_reasons_help = '每行一個。留空則每次手動輸入。';

$lang->zpay_group_notify = '通知';
$lang->zpay_notify_admin_email = '管理者通知信箱';
$lang->zpay_notify_events = '通知時機';
$lang->zpay_notify_on_paid = '付款完成';
$lang->zpay_notify_on_cancel = '付款取消';

$lang->zpay_group_security = '安全與紀錄';
$lang->zpay_log_retention_days = '紀錄保留天數';
$lang->zpay_log_retention_days_help = '設為 0 則永不刪除。這些紀錄是爭議憑據，請勿設得太短。';
$lang->zpay_webhook_ip_whitelist = 'Webhook 允許 IP';
$lang->zpay_webhook_ip_whitelist_help = '每行一個。留空則不限制 IP。結尾加 * 可指定網段。IP 只是輔助手段，真正的防線是每次收到 Webhook 都向金流重新查詢。';

$lang->zpay_group_notice = '政策標示';
$lang->zpay_biz_notice = '結帳頁面公告文字';
$lang->zpay_biz_notice_help = '營業登記編號等需顯示在結帳頁面下方的文字。';

// 付款方式設定
$lang->zpay_enabled_gateways = '啟用的付款方式';
$lang->zpay_enabled_gateways_help = '只有勾選的付款方式會顯示在結帳頁面。';
$lang->zpay_not_configured = '未填金鑰';
$lang->zpay_toss_client_key = '用戶端金鑰';
$lang->zpay_toss_secret_key = '私密金鑰';
$lang->zpay_toss_key_help = '在 Toss Payments 商家後台取得。測試金鑰以 test_ 開頭，正式金鑰以 live_ 開頭。';
$lang->zpay_webhook_url = 'Webhook 位址';
$lang->zpay_webhook_url_help = '請在金流後台的 Webhook 設定中登錄此位址，用於接收虛擬帳戶入帳等非同步通知。';
$lang->zpay_bank_accounts = '收款帳戶';
$lang->zpay_bank_accounts_help = '只有同時填寫銀行與帳號的列才會儲存。清空該列即可刪除。';
$lang->zpay_bank_name = '銀行';
$lang->zpay_bank_account = '帳號';
$lang->zpay_bank_holder = '戶名';
$lang->zpay_bank_extra = '附加項目';
$lang->zpay_bank_extra_help = '每行一項，格式為「名稱=值」。例如：銀行代碼=002、卡號=1234-5678';
$lang->zpay_bank_extra_ph = '銀行代碼=002';
$lang->zpay_bank_due_days = '付款期限（天）';
$lang->zpay_bank_due_days_help = '超過期限後訂單將視為過期。';

// 結帳頁面
$lang->zpay_checkout_title = '結帳';
$lang->zpay_order_summary = '訂單內容';
$lang->zpay_order_code = '付款編號';
$lang->zpay_product = '商品';
$lang->zpay_payer = '付款人';
$lang->zpay_payer_phone = '聯絡電話';
$lang->zpay_payer_email = '電子郵件';
$lang->zpay_amount = '付款金額';
$lang->zpay_select_method = '選擇付款方式';
$lang->zpay_depositor_name = '匯款人姓名';
$lang->zpay_bank_due_notice = '請於下單後 %d 天內完成轉帳。逾期訂單將自動取消。';
$lang->zpay_pay_button = '付款 %s';

// 付款結果
$lang->zpay_result_paid = '付款已完成';
$lang->zpay_result_pending = '正在等待您的轉帳';
$lang->zpay_result_cancelled = '付款已取消';
$lang->zpay_result_expired = '付款期限已過';
$lang->zpay_result_failed = '付款失敗';
$lang->zpay_bank_guide_title = '轉帳資訊';
$lang->zpay_due_date = '付款期限';
$lang->zpay_receipt = '檢視收據';
$lang->zpay_back_to_shop = '返回';
$lang->zpay_cancelled_amount = '取消金額';

// 付款紀錄
$lang->zpay_order_detail = '付款明細';
$lang->zpay_source = '付款對象';
$lang->zpay_gateway = '付款方式';
$lang->zpay_pg_tid = '金流交易編號';
$lang->zpay_status = '狀態';
$lang->zpay_regdate = '建立時間';
$lang->zpay_paid_date = '付款時間';
$lang->zpay_cancelled_date = '取消時間';
$lang->zpay_ipaddress = 'IP 位址';
$lang->zpay_remain_amount = '剩餘金額';
$lang->zpay_total_orders = '共 %s 筆';
$lang->zpay_no_orders = '尚無付款紀錄。';
$lang->zpay_filter_all_status = '全部狀態';
$lang->zpay_confirm_deposit = '確認入帳';
$lang->zpay_confirm_deposit_ask = '您已確認入帳了嗎？確認後付款狀態將變更為已完成。';
$lang->zpay_confirm_deposit_help = '確認款項入帳後再按下。按下後立即視為付款完成，並通知發起付款的模組。';
$lang->zpay_cancel_payment = '取消付款';
$lang->zpay_cancel_amount = '取消金額';
$lang->zpay_cancel_amount_help = '不能取消超過剩餘金額的部分。';
$lang->zpay_cancel_reason = '取消原因';

// 狀態
$lang->zpay_status_ready = '待付款';
$lang->zpay_status_pending = '待入帳';
$lang->zpay_status_paid = '付款完成';
$lang->zpay_status_cancelled = '已取消';
$lang->zpay_status_partial_cancelled = '部分取消';
$lang->zpay_status_failed = '失敗';
$lang->zpay_status_expired = '已逾期';

// 紀錄
$lang->zpay_communication_log = '通訊紀錄';
$lang->zpay_log_action = '動作';
$lang->zpay_log_result = '結果';
$lang->zpay_log_response = '回應';
$lang->zpay_log_depositor = '匯款人';
$lang->zpay_log_due = '截止';
$lang->zpay_no_logs = '尚無紀錄。';
$lang->zpay_total_logs = '共 %s 筆';
$lang->zpay_filter_all_action = '全部動作';
$lang->zpay_filter_all_result = '全部結果';
$lang->zpay_result_success = '成功';
$lang->zpay_result_fail = '失敗';
$lang->zpay_purge_logs = '刪除 %d 天前的紀錄';

// 訊息
$lang->msg_pay_disabled = '付款功能已關閉。';
$lang->msg_invalid_source = '付款對象無效。';
$lang->msg_invalid_amount = '付款金額無效。';
$lang->msg_order_not_found = '找不到付款訂單。';
$lang->msg_no_gateway_available = '沒有可用的付款方式，請聯絡管理者。';
$lang->msg_gateway_not_found = '找不到該付款方式。';
$lang->msg_invalid_ticket = '付款資訊已過期，請重新開始。';
$lang->msg_already_settled = '該筆付款已處理完成。';
$lang->msg_too_many_requests = '付款嘗試過於頻繁，請稍後再試。';

$lang->msg_approve_success = '付款已核准。';
$lang->msg_approve_failed = '付款核准失敗。';
$lang->msg_payment_cancelled = '付款已取消。';
$lang->msg_payment_not_completed = '該筆付款尚未完成。';
$lang->msg_amount_mismatch = '付款金額與訂單金額不一致，已中止付款。';
$lang->msg_missing_payment_key = '缺少金流交易編號。';
$lang->msg_unknown_pg_status = '未知的付款狀態。';
$lang->msg_pg_error = '與金流通訊時發生錯誤。';
$lang->msg_pg_unreachable = '無法連線至金流伺服器。';
$lang->msg_paypal_auth_failed = 'PayPal 認證失敗。目前連線到 %s。請確認金鑰與該模式一致。';
$lang->paypal_mode_sandbox = '沙盒(測試)';
$lang->paypal_mode_live = '正式';
$lang->zpay_paypal_mode = '連線對象';
$lang->zpay_paypal_mode_help = '開啟測試模式連線沙盒，關閉則連線正式環境。金鑰須與之相符。';
$lang->zpay_paypal_test = '連線測試';
$lang->zpay_paypal_testing = '檢查中...';
$lang->msg_paypal_test_ok = '已成功連線 PayPal。';
$lang->msg_paypal_test_empty = '請先輸入用戶端 ID 和密鑰。';
$lang->msg_query_not_supported = '此付款方式不支援查詢。';

$lang->msg_cancel_success = '付款已取消。';
$lang->msg_cancel_failed = '取消付款失敗。';
$lang->msg_not_cancellable = '目前狀態無法取消。';
$lang->msg_invalid_cancel_amount = '取消金額無效。';
$lang->msg_partial_cancel_disabled = '不允許部分取消。';
$lang->msg_cancel_record_failed = '金流已完成取消，但本站紀錄更新失敗，請聯絡管理者。';
$lang->cancel_default_reason = '客戶要求';

$lang->msg_no_bank_account = '尚未登錄收款帳戶。';
$lang->msg_bank_registered = '已顯示收款帳戶資訊，請於期限內完成轉帳。';
$lang->msg_bank_manual_refund = '銀行轉帳的退款需由管理者手動匯出。';
$lang->msg_not_pending = '該訂單不處於待入帳狀態。';
$lang->msg_deposit_confirmed = '已確認入帳，付款處理完成。';
$lang->msg_log_retention_disabled = '紀錄保留天數設為 0，因此不會刪除。';

// 確認收貨與手動退款
$lang->zpay_status_confirmed = '已確認收貨';
$lang->zpay_confirm_date = '確認收貨時間';
$lang->zpay_auto_cancel_days = '金流可取消期限（天）';
$lang->zpay_auto_cancel_days_help = '付款後超過此期限將不再嘗試金流取消，改為手動退款。因為結算完成後信用卡取消會被拒絕。設為 0 表示不限制。';
$lang->zpay_allow_force_cancel = '允許強制取消已確認訂單';
$lang->zpay_allow_force_cancel_help = '允許管理者取消已確認收貨的付款。關閉後，確認收貨即為最終狀態。';
$lang->zpay_force_cancel = '強制取消';
$lang->zpay_force_cancel_confirm = '我知道該付款已確認收貨，仍要取消';
$lang->zpay_force_cancel_help = '該付款已確認收貨。如需取消，必須在下方明確勾選強制取消。';
$lang->zpay_no_auto_cancel_help = '此付款方式無法自動取消。取消只會整理紀錄，實際退款需由管理者手動匯出。';
$lang->zpay_manual_refund_title = '手動退款';
$lang->zpay_manual_refund_help = '取消已紀錄，但款項尚未匯出。請匯出下列金額後點擊完成。';
$lang->zpay_manual_refund_done = '標記為已匯出';
$lang->zpay_manual_refund_sent = '已匯出';
$lang->zpay_pending_refund_notice = '有 %s 筆退款尚未匯出，請及時處理。';

$lang->msg_confirm_success = '已確認收貨。';
$lang->msg_already_confirmed = '該付款已確認收貨。';
$lang->msg_not_confirmable = '目前狀態無法確認收貨。';
$lang->msg_confirmed_not_cancellable = '已確認收貨的付款無法取消，需要管理者強制取消。';
$lang->msg_force_cancel_disabled = '未允許強制取消已確認的付款。';
$lang->msg_cancel_manual_refund_queued = '已取消。此付款方式無法自動退款，需由管理者手動匯出。';
$lang->msg_no_pending_refund = '沒有待匯出的退款。';
$lang->msg_refund_completed = '已紀錄為匯出完成。';
$lang->zpay_paypal_allow_krw = '韓元訂單換算支付';
$lang->zpay_paypal_allow_krw_label = '允許韓元訂單使用 PayPal 支付';
$lang->zpay_paypal_allow_krw_help = 'PayPal 不結算韓元。開啟後，韓元訂單會依上方選擇的支付貨幣換算後送出。買家將看到外幣結帳頁面，且退款時匯率變動可能導致實際退款金額與訂單金額不一致。若面向海外銷售，建議直接以該貨幣定價。需要先設定匯率。';

$lang->gateway_conekta = 'Conekta (墨西哥 / 拉美)';
$lang->zpay_conekta_private_key = 'Conekta 私鑰';
$lang->zpay_conekta_key_help = '於控制台 > 開發者 > API 金鑰取得。key_test 開頭為測試，key_live 開頭為正式環境。';
$lang->zpay_conekta_webhook = 'Webhook 網址';
$lang->zpay_conekta_webhook_help = '於控制台 > Webhook 註冊此網址並啟用 order.paid、order.expired。OXXO 與 SPEI 付款透過此 Webhook 確認。';
$lang->zpay_conekta_methods = '付款方式';
$lang->zpay_conekta_method_card = '信用卡';
$lang->zpay_conekta_method_cash = '現金 (OXXO)';
$lang->zpay_conekta_method_bank_transfer = '銀行轉帳 (SPEI)';
$lang->zpay_conekta_methods_help = '託管付款頁提供的方式。現金與轉帳需等待入帳，由 Webhook 完成。';
$lang->zpay_conekta_currency = 'Conekta 結算貨幣';
$lang->zpay_conekta_currency_help = 'MXN、USD 訂單依原幣種收款，KRW 訂單依上方共用匯率換算為此貨幣。';
$lang->zpay_paypal_allow_krw_label_generic = '允許以 KRW 計價的訂單使用此付款方式';
$lang->msg_conekta_auth_failed = 'Conekta 驗證失敗，請檢查私鑰。';
$lang->msg_conekta_test_empty = '請先輸入 Conekta 私鑰。';
$lang->msg_conekta_test_ok = '已成功連線 Conekta (%s)。';
$lang->zpay_conekta_pending_guide = '等待入帳。依以下參考號付款後訂單將自動完成。';
$lang->zpay_conekta_reference = '參考號 / CLABE';
$lang->zpay_bank_due_date = '付款期限';
$lang->msg_conekta_manual_refund = '現金與轉帳付款無法透過 Conekta 自動退款，請手動退款。';
$lang->msg_conekta_pending = '已登錄 Conekta 付款說明。';
