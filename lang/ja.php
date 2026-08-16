<?php

$lang->zittme_pay = 'Zittme ペイ';

// 管理者タブ
$lang->zpay_tab_config = '基本';
$lang->zpay_tab_gateway = '決済手段';
$lang->zpay_tab_orders = '決済履歴';
$lang->zpay_tab_logs = '通信ログ';

$lang->about_zpay_config = '決済エンジン共通の動作を設定します。コマース・予約など決済を必要とするモジュールがこの設定を共有します。';
$lang->about_zpay_gateway = '使用する決済手段を有効にし、キーを入力します。キーが未入力の決済手段は決済画面に表示されません。';
$lang->about_zpay_logs = '決済に関してやり取りしたすべてのリクエストとレスポンスです。紛争時の証拠資料になるため、保管期間は長めに設定してください。';

// 決済手段名
$lang->gateway_toss = 'トスペイメンツ';
$lang->gateway_banktransfer = '銀行振込';
$lang->gateway_inicis = 'KG INICIS';
$lang->zpay_inicis_mid = 'INICIS 加盟店ID (MID)';
$lang->zpay_inicis_sign_key = 'Sign Key';
$lang->zpay_inicis_api_key = 'INIAPI Key (キャンセル用)';
$lang->zpay_inicis_key_help = 'INICIS加盟店管理者で発行します。キャンセル・返金は別のINIAPIキーを使用します。テストモードではテスト加盟店(INIpayTest)のキーを入力してください。';
$lang->gateway_kcp = 'NHN KCP';
$lang->zpay_kcp_site_cd = 'KCP サイトコード (site_cd)';
$lang->zpay_kcp_cert_info = 'サービス証明書 (PEM本文)';
$lang->zpay_kcp_priv_key = '加盟店秘密鍵 (PEM本文)';
$lang->zpay_kcp_priv_pass = '秘密鍵パスワード';
$lang->zpay_kcp_key_help = 'KCP管理者認証センターで発行されたPEMファイルの内容をそのまま貼り付けてください。秘密鍵はキャンセル署名にのみ使用されます。テストモードではテストサイトコード(T0000)とテスト証明書を使用してください。';
$lang->gateway_nicepay = 'NICE Pay';
$lang->zpay_nicepay_client_id = 'NICE Pay Client ID';
$lang->zpay_nicepay_secret_key = 'Secret Key';
$lang->zpay_nicepay_key_help = 'NICE Pay開発者センター(developers.nicepay.co.kr)で発行します。テストモードではサンドボックスキーを、運用では契約後に発行された本番キーを入力してください。';
$lang->gateway_portone = 'PortOne';
$lang->zpay_portone_store_id = 'PortOne Store ID';
$lang->zpay_portone_channel_key = 'チャネルキー';
$lang->zpay_portone_api_secret = 'V2 API Secret';
$lang->zpay_portone_key_help = 'PortOneコンソール(portone.io)で発行します。実際のPGはコンソールのチャネル設定で選びます。テストにはテストチャネルのキーを使用してください。';
$lang->gateway_paypal = 'PayPal';
$lang->zpay_paypal_client_id = 'PayPal Client ID';
$lang->zpay_paypal_secret = 'PayPal Secret';
$lang->zpay_paypal_key_help = 'PayPal開発者コンソール(developer.paypal.com)のアプリで発行します。テストモードではサンドボックス、運用ではライブのキーを入力してください。';
$lang->zpay_paypal_currency = 'PayPal決済通貨';
$lang->zpay_paypal_currency_help = 'PayPalはKRWに対応していません。注文金額をこの通貨に換算して決済します。';
$lang->zpay_paypal_exchange_rate = '適用為替レート';
$lang->zpay_paypal_exchange_rate_help = '決済通貨1単位あたりのKRWです。例: USDなら1350。返金は決済時のレートで処理されます。';
$lang->zpay_exchange_rates = '共通為替レート';
$lang->zpay_exchange_rates_help = '各通貨1単位あたりのKRWです。決済換算とコマースの多通貨価格が共に参照します。注文には決済時のレートが保存されます。';
$lang->zpay_fx_no_active = 'No additional currencies. Select currencies in Basic settings and rate rows appear here automatically.';
$lang->zpay_fx_auto_ph = 'Auto updated';
$lang->zpay_fx_code = '通貨コード';
$lang->zpay_fx_rate = 'レート (KRW)';
$lang->zpay_fx_manual = '手動固定';
$lang->zpay_fx_manual_short = '固定';
$lang->zpay_exchange_auto = '自動更新';
$lang->zpay_exchange_auto_label = '1日1回自動更新';
$lang->zpay_exchange_auto_help = '決済・閲覧経路で1日1回更新します。手動固定の通貨は上書きされません。';
$lang->zpay_fx_source_erapi = 'open.er-api.com (キー不要)';
$lang->zpay_fx_source_koreaexim = '韓国輸出入銀行 (APIキー必要)';
$lang->zpay_fx_api_key = 'APIキー';
$lang->zpay_fx_updated = '最終更新';
$lang->zpay_paypal_rate_shared_help = '上の共通為替レートの該当通貨の値を使用します。';

// 基本設定
$lang->zpay_enabled = '決済を使用';
$lang->zpay_enabled_help = 'オフにすると新しい決済を受け付けません。';
$lang->zpay_test_mode = 'テストモード';
$lang->zpay_test_mode_help = 'PG のテストキーを使用中であることを示します。実際の決済は行われません。';
$lang->zpay_currency = '決済通貨';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = '注文番号の接頭辞';
$lang->zpay_order_prefix_help = 'PG 管理画面で自社の注文を見分けるための表示です。英数字 8 文字以内。';

$lang->zpay_group_cancel = 'キャンセル・返金';
$lang->zpay_allow_partial_cancel = '部分キャンセルを許可';
$lang->zpay_allow_partial_cancel_help = '決済金額の一部だけを返金できるようにします。';
$lang->zpay_cancel_reasons = 'キャンセル理由の一覧';
$lang->zpay_cancel_reasons_help = '1 行に 1 つずつ入力します。空欄にすると毎回入力します。';

$lang->zpay_group_notify = '通知';
$lang->zpay_notify_admin_email = '管理者通知メール';
$lang->zpay_notify_events = '通知を受け取る時点';
$lang->zpay_notify_on_paid = '決済完了';
$lang->zpay_notify_on_cancel = '決済キャンセル';

$lang->zpay_group_security = 'セキュリティ・ログ';
$lang->zpay_log_retention_days = 'ログ保管期間（日）';
$lang->zpay_log_retention_days_help = '0 なら削除しません。紛争対応の資料なので短くしないでください。';
$lang->zpay_webhook_ip_whitelist = 'Webhook 許可 IP';
$lang->zpay_webhook_ip_whitelist_help = '1 行に 1 つ。空欄なら IP で制限しません。末尾に * を付けると範囲指定になります。IP は補助手段であり、本来の防御は Webhook 受信のたびに PG へ再照会する手順です。';

$lang->zpay_group_notice = 'ポリシー表記';
$lang->zpay_biz_notice = '決済画面の告知文';
$lang->zpay_biz_notice_help = '事業者登録番号など、決済画面の下部に表示する文言です。';

// 決済手段の設定
$lang->zpay_enabled_gateways = '使用する決済手段';
$lang->zpay_enabled_gateways_help = 'チェックした決済手段のみ決済画面に表示されます。';
$lang->zpay_not_configured = 'キー未入力';
$lang->zpay_toss_client_key = 'クライアントキー';
$lang->zpay_toss_secret_key = 'シークレットキー';
$lang->zpay_toss_key_help = 'トスペイメンツの加盟店管理画面で発行します。テストキーは test_、本番キーは live_ で始まります。';
$lang->zpay_webhook_url = 'Webhook アドレス';
$lang->zpay_webhook_url_help = 'PG 管理画面の Webhook 設定にこのアドレスを登録してください。仮想口座への入金など非同期の通知を受け取ります。';
$lang->zpay_bank_accounts = '入金口座';
$lang->zpay_bank_accounts_help = '銀行と口座番号が両方入力された行のみ保存されます。行を空にすると削除されます。';
$lang->zpay_bank_name = '銀行';
$lang->zpay_bank_account = '口座番号';
$lang->zpay_bank_holder = '口座名義';
$lang->zpay_bank_due_days = '入金期限（日）';
$lang->zpay_bank_due_days_help = '期限を過ぎると注文は期限切れとして処理されます。';

// 決済画面
$lang->zpay_checkout_title = 'お支払い';
$lang->zpay_order_summary = '注文内容';
$lang->zpay_order_code = '決済番号';
$lang->zpay_product = '商品';
$lang->zpay_payer = 'お支払者';
$lang->zpay_payer_phone = '連絡先';
$lang->zpay_payer_email = 'メール';
$lang->zpay_amount = 'お支払金額';
$lang->zpay_select_method = '決済手段を選択';
$lang->zpay_depositor_name = '振込名義';
$lang->zpay_bank_due_notice = 'ご注文後 %d 日以内にお振込みください。期限を過ぎると注文は自動的にキャンセルされます。';
$lang->zpay_pay_button = '%s を支払う';

// 決済結果
$lang->zpay_result_paid = 'お支払いが完了しました';
$lang->zpay_result_pending = 'ご入金をお待ちしています';
$lang->zpay_result_cancelled = '決済がキャンセルされました';
$lang->zpay_result_expired = 'お支払い期限が過ぎました';
$lang->zpay_result_failed = '決済に失敗しました';
$lang->zpay_bank_guide_title = 'お振込のご案内';
$lang->zpay_due_date = '入金期限';
$lang->zpay_receipt = '領収書を見る';
$lang->zpay_back_to_shop = '戻る';
$lang->zpay_cancelled_amount = 'キャンセル金額';

// 決済履歴
$lang->zpay_order_detail = '決済の詳細';
$lang->zpay_source = '決済対象';
$lang->zpay_gateway = '決済手段';
$lang->zpay_pg_tid = 'PG 取引番号';
$lang->zpay_status = '状態';
$lang->zpay_regdate = '登録日';
$lang->zpay_paid_date = '決済日';
$lang->zpay_cancelled_date = 'キャンセル日';
$lang->zpay_ipaddress = 'IP アドレス';
$lang->zpay_remain_amount = '残額';
$lang->zpay_total_orders = '全 %s 件';
$lang->zpay_no_orders = '決済履歴がありません。';
$lang->zpay_filter_all_status = 'すべての状態';
$lang->zpay_confirm_deposit = '入金を確認';
$lang->zpay_confirm_deposit_ask = '入金を確認しましたか。確認処理を行うと決済完了に変わります。';
$lang->zpay_confirm_deposit_help = '通帳で入金を確認してから押してください。押した時点で決済完了として処理され、要求元のモジュールに通知されます。';
$lang->zpay_cancel_payment = '決済をキャンセル';
$lang->zpay_cancel_amount = 'キャンセル金額';
$lang->zpay_cancel_amount_help = '残額を超える金額はキャンセルできません。';
$lang->zpay_cancel_reason = 'キャンセル理由';

// 状態
$lang->zpay_status_ready = '決済待ち';
$lang->zpay_status_pending = '入金待ち';
$lang->zpay_status_paid = '決済完了';
$lang->zpay_status_cancelled = 'キャンセル';
$lang->zpay_status_partial_cancelled = '部分キャンセル';
$lang->zpay_status_failed = '失敗';
$lang->zpay_status_expired = '期限切れ';

// ログ
$lang->zpay_communication_log = '通信ログ';
$lang->zpay_log_action = '動作';
$lang->zpay_log_result = '結果';
$lang->zpay_log_response = '応答';
$lang->zpay_log_depositor = '入金者';
$lang->zpay_log_due = '入金期限';
$lang->zpay_no_logs = '記録がありません。';
$lang->zpay_total_logs = '全 %s 件';
$lang->zpay_filter_all_action = 'すべての動作';
$lang->zpay_filter_all_result = 'すべての結果';
$lang->zpay_result_success = '成功';
$lang->zpay_result_fail = '失敗';
$lang->zpay_purge_logs = '%d 日を過ぎたログを削除';

// メッセージ
$lang->msg_pay_disabled = '決済機能がオフになっています。';
$lang->msg_invalid_source = '決済対象が正しくありません。';
$lang->msg_invalid_amount = '決済金額が正しくありません。';
$lang->msg_order_not_found = '決済注文が見つかりません。';
$lang->msg_no_gateway_available = '利用できる決済手段がありません。管理者にお問い合わせください。';
$lang->msg_gateway_not_found = '決済手段が見つかりません。';
$lang->msg_invalid_ticket = '決済情報の有効期限が切れました。最初からやり直してください。';
$lang->msg_already_settled = 'すでに処理が終わった決済です。';
$lang->msg_too_many_requests = '決済の試行が多すぎます。しばらくしてからもう一度お試しください。';

$lang->msg_approve_success = '決済が承認されました。';
$lang->msg_approve_failed = '決済の承認に失敗しました。';
$lang->msg_payment_cancelled = '決済がキャンセルされました。';
$lang->msg_payment_not_completed = '完了していない決済です。';
$lang->msg_amount_mismatch = '決済金額が注文金額と一致しないため、決済を中止しました。';
$lang->msg_missing_payment_key = 'PG 取引番号がありません。';
$lang->msg_unknown_pg_status = '不明な決済状態です。';
$lang->msg_pg_error = 'PG との通信中にエラーが発生しました。';
$lang->msg_pg_unreachable = 'PG サーバーに接続できません。';
$lang->msg_paypal_auth_failed = 'PayPal の認証に失敗しました。現在 %s に接続しています。キーがこのモードのものか確認してください。';
$lang->paypal_mode_sandbox = 'サンドボックス(テスト)';
$lang->paypal_mode_live = '本番';
$lang->zpay_paypal_mode = '接続先';
$lang->zpay_paypal_mode_help = 'テストモードをオンにするとサンドボックス、オフにすると本番に接続します。キーも同じ側のものが必要です。';
$lang->zpay_paypal_test = '接続確認';
$lang->zpay_paypal_testing = '確認中...';
$lang->msg_paypal_test_ok = 'PayPal に正常に接続できます。';
$lang->msg_paypal_test_empty = 'クライアント ID とシークレットを先に入力してください。';
$lang->msg_query_not_supported = 'この決済手段は照会に対応していません。';

$lang->msg_cancel_success = '決済がキャンセルされました。';
$lang->msg_cancel_failed = '決済のキャンセルに失敗しました。';
$lang->msg_not_cancellable = 'キャンセルできる状態ではありません。';
$lang->msg_invalid_cancel_amount = 'キャンセル金額が正しくありません。';
$lang->msg_partial_cancel_disabled = '部分キャンセルは許可されていません。';
$lang->msg_cancel_record_failed = 'PG 側のキャンセルは完了しましたが、履歴への反映に失敗しました。管理者にお問い合わせください。';
$lang->cancel_default_reason = 'お客様のご要望';

$lang->msg_no_bank_account = '登録された入金口座がありません。';
$lang->msg_bank_registered = '入金口座をご案内しました。期限内にお振込みください。';
$lang->msg_bank_manual_refund = '返金は管理者が直接送金する必要があります。';
$lang->msg_not_pending = '入金待ち状態の注文ではありません。';
$lang->msg_deposit_confirmed = '入金が確認され、決済が完了しました。';
$lang->msg_log_retention_disabled = 'ログ保管期間が 0 に設定されているため削除しません。';

// 購入確定・手動返金
$lang->zpay_status_confirmed = '購入確定';
$lang->zpay_confirm_date = '購入確定日';
$lang->zpay_auto_cancel_days = 'PG 自動キャンセル期限（日）';
$lang->zpay_auto_cancel_days_help = '決済後この期間を過ぎると PG キャンセルを試みず手動返金に回します。精算が終わるとカードキャンセルができなくなるためです。0 なら制限しません。';
$lang->zpay_allow_force_cancel = '確定分の強制キャンセルを許可';
$lang->zpay_allow_force_cancel_help = '購入確定済みの決済も管理者がキャンセルできるようにします。オフにすると確定後は一切キャンセルできません。';
$lang->zpay_force_cancel = '強制キャンセル';
$lang->zpay_force_cancel_confirm = '確定済みの決済であることを理解してキャンセルします';
$lang->zpay_force_cancel_help = 'すでに購入確定された決済です。キャンセルするには下で強制キャンセルを明示的に選択してください。';
$lang->zpay_no_auto_cancel_help = 'この決済手段は自動キャンセルができません。キャンセルしても記録が整理されるだけで、実際の送金は管理者が行う必要があります。';
$lang->zpay_manual_refund_title = '手動返金';
$lang->zpay_manual_refund_help = 'キャンセルは処理されましたが、まだ送金されていません。下記の金額を口座へ送ってから完了を押してください。';
$lang->zpay_manual_refund_done = '送金完了にする';
$lang->zpay_manual_refund_sent = '送金完了';
$lang->zpay_pending_refund_notice = '未送金の返金が %s 件あります。ご確認ください。';

$lang->msg_confirm_success = '購入確定として処理されました。';
$lang->msg_already_confirmed = 'すでに購入確定された決済です。';
$lang->msg_not_confirmable = '購入確定できる状態ではありません。';
$lang->msg_confirmed_not_cancellable = '購入確定された決済はキャンセルできません。管理者による強制キャンセルが必要です。';
$lang->msg_force_cancel_disabled = '確定分の強制キャンセルは許可されていません。';
$lang->msg_cancel_manual_refund_queued = 'キャンセルされました。この決済手段は自動返金ができないため、管理者が直接送金する必要があります。';
$lang->msg_no_pending_refund = '送金待ちの返金はありません。';
$lang->msg_refund_completed = '送金完了として記録しました。';
$lang->zpay_paypal_allow_krw = 'ウォン建て注文の換算決済';
$lang->zpay_paypal_allow_krw_label = 'ウォン建て注文もペイパルで決済できるようにする';
$lang->zpay_paypal_allow_krw_help = 'ペイパルはウォンでの精算に対応していません。この項目をオンにすると、ウォン建て注文を上で選んだ決済通貨に換算して送ります。購入者には外貨の決済画面が表示され、返金時のレートによって実際の返金額が注文金額と異なる場合があります。海外販売をされるなら、商品自体を外貨で販売する方をおすすめします。為替レートの設定が必要です。';
