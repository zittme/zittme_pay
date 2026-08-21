<?php

$lang->zittme_pay = 'Zittme Pay';

// Admin tabs
$lang->zpay_tab_config = 'General';
$lang->zpay_tab_gateway = 'Payment methods';
$lang->zpay_tab_orders = 'Payments';
$lang->zpay_tab_logs = 'Communication log';

$lang->about_zpay_config = 'Common behaviour of the payment engine. Modules that need payment, such as commerce or reservation, all share these settings.';
$lang->about_zpay_gateway = 'Enable the payment methods you want and enter their keys. A method whose keys are missing never appears on the checkout page.';
$lang->about_zpay_logs = 'Every request and response exchanged for a payment. This is your evidence if a payment is disputed, so keep it for a generous period.';

// Payment method names
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Bank transfer';
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
$lang->zpay_fx_no_active = 'No additional currencies. Select currencies in Basic settings and rate rows appear here automatically.';
$lang->zpay_fx_auto_ph = 'Auto updated';
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

// General
$lang->zpay_enabled = 'Enable payments';
$lang->zpay_enabled_help = 'Turn this off to stop accepting new payments.';
$lang->zpay_test_mode = 'Test mode';
$lang->zpay_test_mode_help = 'Indicates that the gateway test keys are in use. No real money moves.';
$lang->zpay_currency = 'Currency';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = 'Order number prefix';
$lang->zpay_order_prefix_help = 'Helps you recognise your orders in the gateway console. Letters and digits, up to 8 characters.';

$lang->zpay_group_cancel = 'Cancellation and refunds';
$lang->zpay_allow_partial_cancel = 'Allow partial cancellation';
$lang->zpay_allow_partial_cancel_help = 'Lets you refund only part of the paid amount.';
$lang->zpay_cancel_reasons = 'Cancellation reasons';
$lang->zpay_cancel_reasons_help = 'One per line. Leave empty to type a reason each time.';

$lang->zpay_group_notify = 'Notifications';
$lang->zpay_notify_admin_email = 'Administrator e-mail';
$lang->zpay_notify_events = 'Notify me when';
$lang->zpay_notify_on_paid = 'A payment completes';
$lang->zpay_notify_on_cancel = 'A payment is cancelled';

$lang->zpay_group_security = 'Security and logging';
$lang->zpay_log_retention_days = 'Log retention (days)';
$lang->zpay_log_retention_days_help = '0 keeps logs forever. Do not set this too low — these logs are your evidence in a dispute.';
$lang->zpay_webhook_ip_whitelist = 'Allowed webhook IPs';
$lang->zpay_webhook_ip_whitelist_help = 'One per line. Leave empty to allow any IP. End with * to allow a range. IP filtering is only a secondary measure; the real defence is re-querying the gateway for every webhook.';

$lang->zpay_group_notice = 'Legal notice';
$lang->zpay_biz_notice = 'Checkout footer notice';
$lang->zpay_biz_notice_help = 'Business registration details and similar text shown at the bottom of the checkout page.';

// Payment methods
$lang->zpay_enabled_gateways = 'Enabled payment methods';
$lang->zpay_enabled_gateways_help = 'Only the methods you check appear on the checkout page.';
$lang->zpay_not_configured = 'keys missing';
$lang->zpay_toss_client_key = 'Client key';
$lang->zpay_toss_secret_key = 'Secret key';
$lang->zpay_toss_key_help = 'Issued in the Toss Payments merchant console. Test keys start with test_ and live keys with live_.';
$lang->zpay_webhook_url = 'Webhook URL';
$lang->zpay_webhook_url_help = 'Register this URL in your gateway console so asynchronous notifications, such as virtual-account deposits, reach us.';
$lang->zpay_bank_accounts = 'Bank accounts';
$lang->zpay_bank_accounts_help = 'Only rows with both a bank and an account number are saved. Clear a row to remove it.';
$lang->zpay_bank_name = 'Bank';
$lang->zpay_bank_account = 'Account number';
$lang->zpay_bank_holder = 'Account holder';
$lang->zpay_bank_extra = 'Extra fields';
$lang->zpay_bank_extra_help = 'One per line as "Label=Value". e.g. Bank code=002, Card number=1234-5678';
$lang->zpay_bank_extra_ph = 'Bank code=002';
$lang->zpay_bank_due_days = 'Payment window (days)';
$lang->zpay_bank_due_days_help = 'The order expires once this period has passed.';

// Checkout
$lang->zpay_checkout_title = 'Checkout';
$lang->zpay_order_summary = 'Order summary';
$lang->zpay_order_code = 'Payment number';
$lang->zpay_product = 'Item';
$lang->zpay_payer = 'Payer';
$lang->zpay_payer_phone = 'Phone';
$lang->zpay_payer_email = 'E-mail';
$lang->zpay_amount = 'Amount';
$lang->zpay_select_method = 'Choose a payment method';
$lang->zpay_depositor_name = 'Depositor name';
$lang->zpay_bank_due_notice = 'Please transfer the amount within %d days. The order is cancelled automatically once that period passes.';
$lang->zpay_pay_button = 'Pay %s';

// Result
$lang->zpay_result_paid = 'Payment complete';
$lang->zpay_result_pending = 'Waiting for your transfer';
$lang->zpay_result_cancelled = 'Payment cancelled';
$lang->zpay_result_expired = 'The payment window has passed';
$lang->zpay_result_failed = 'Payment failed';
$lang->zpay_bank_guide_title = 'Transfer details';
$lang->zpay_due_date = 'Pay by';
$lang->zpay_receipt = 'View receipt';
$lang->zpay_back_to_shop = 'Go back';
$lang->zpay_cancelled_amount = 'Cancelled amount';

// Payments list
$lang->zpay_order_detail = 'Payment detail';
$lang->zpay_source = 'Paid for';
$lang->zpay_gateway = 'Method';
$lang->zpay_pg_tid = 'Gateway transaction ID';
$lang->zpay_status = 'Status';
$lang->zpay_regdate = 'Created';
$lang->zpay_paid_date = 'Paid';
$lang->zpay_cancelled_date = 'Cancelled';
$lang->zpay_ipaddress = 'IP address';
$lang->zpay_remain_amount = 'remaining';
$lang->zpay_total_orders = '%s payments';
$lang->zpay_no_orders = 'No payments yet.';
$lang->zpay_filter_all_status = 'All statuses';
$lang->zpay_confirm_deposit = 'Confirm deposit';
$lang->zpay_confirm_deposit_ask = 'Have you verified the deposit? Confirming will mark the payment as complete.';
$lang->zpay_confirm_deposit_help = 'Press this once you have seen the money arrive. The payment is completed immediately and the requesting module is notified.';
$lang->zpay_cancel_payment = 'Cancel payment';
$lang->zpay_cancel_amount = 'Amount to cancel';
$lang->zpay_cancel_amount_help = 'You cannot cancel more than the remaining amount.';
$lang->zpay_cancel_reason = 'Reason';

// Statuses
$lang->zpay_status_ready = 'Awaiting payment';
$lang->zpay_status_pending = 'Awaiting deposit';
$lang->zpay_status_paid = 'Paid';
$lang->zpay_status_cancelled = 'Cancelled';
$lang->zpay_status_partial_cancelled = 'Partially cancelled';
$lang->zpay_status_failed = 'Failed';
$lang->zpay_status_expired = 'Expired';

// Log
$lang->zpay_communication_log = 'Communication log';
$lang->zpay_log_action = 'Action';
$lang->zpay_log_result = 'Result';
$lang->zpay_log_response = 'Response';
$lang->zpay_log_depositor = 'Depositor';
$lang->zpay_log_due = 'Due';
$lang->zpay_no_logs = 'No records.';
$lang->zpay_total_logs = '%s records';
$lang->zpay_filter_all_action = 'All actions';
$lang->zpay_filter_all_result = 'All results';
$lang->zpay_result_success = 'Success';
$lang->zpay_result_fail = 'Failure';
$lang->zpay_purge_logs = 'Delete logs older than %d days';

// Messages
$lang->msg_pay_disabled = 'Payments are turned off.';
$lang->msg_invalid_source = 'The payment target is not valid.';
$lang->msg_invalid_amount = 'The payment amount is not valid.';
$lang->msg_order_not_found = 'Payment order not found.';
$lang->msg_no_gateway_available = 'No payment method is available. Please contact the site administrator.';
$lang->msg_gateway_not_found = 'Payment method not found.';
$lang->msg_invalid_ticket = 'This payment session has expired. Please start again.';
$lang->msg_already_settled = 'This payment has already been settled.';
$lang->msg_too_many_requests = 'Too many payment attempts. Please try again shortly.';

$lang->msg_approve_success = 'The payment was approved.';
$lang->msg_approve_failed = 'The payment could not be approved.';
$lang->msg_payment_cancelled = 'The payment was cancelled.';
$lang->msg_payment_not_completed = 'This payment was not completed.';
$lang->msg_amount_mismatch = 'The payment was stopped because the amount did not match the order.';
$lang->msg_missing_payment_key = 'The gateway transaction ID is missing.';
$lang->msg_unknown_pg_status = 'Unknown payment status.';
$lang->msg_pg_error = 'The payment gateway returned an error.';
$lang->msg_pg_unreachable = 'Could not reach the payment gateway.';
$lang->msg_paypal_auth_failed = 'PayPal authentication failed. You are connecting to %s. Check that the keys match this mode.';
$lang->paypal_mode_sandbox = 'Sandbox (test)';
$lang->paypal_mode_live = 'Live';
$lang->zpay_paypal_mode = 'Connecting to';
$lang->zpay_paypal_mode_help = 'Test mode on connects to the sandbox, off connects to live. The keys must match. Test mode is set under Basic settings.';
$lang->zpay_paypal_test = 'Test connection';
$lang->zpay_paypal_testing = 'Checking...';
$lang->msg_paypal_test_ok = 'Connected to PayPal successfully.';
$lang->msg_paypal_test_empty = 'Enter the client ID and secret first.';
$lang->msg_query_not_supported = 'This payment method does not support lookups.';

$lang->msg_cancel_success = 'The payment was cancelled.';
$lang->msg_cancel_failed = 'The payment could not be cancelled.';
$lang->msg_not_cancellable = 'This payment cannot be cancelled in its current state.';
$lang->msg_invalid_cancel_amount = 'The cancellation amount is not valid.';
$lang->msg_partial_cancel_disabled = 'Partial cancellation is not allowed.';
$lang->msg_cancel_record_failed = 'The gateway cancelled the payment, but recording it here failed. Please contact the site administrator.';
$lang->cancel_default_reason = 'Customer request';

$lang->msg_no_bank_account = 'No bank account has been registered.';
$lang->msg_bank_registered = 'The bank account details are shown below. Please transfer within the deadline.';
$lang->msg_bank_manual_refund = 'Refunds for bank transfers must be sent manually by an administrator.';
$lang->msg_not_pending = 'This order is not awaiting a deposit.';
$lang->msg_deposit_confirmed = 'The deposit was confirmed and the payment is now complete.';
$lang->msg_log_retention_disabled = 'Log retention is set to 0, so nothing is deleted.';

// Purchase confirmation and manual refunds
$lang->zpay_status_confirmed = 'Confirmed';
$lang->zpay_confirm_date = 'Confirmed on';
$lang->zpay_auto_cancel_days = 'Gateway cancellation window (days)';
$lang->zpay_auto_cancel_days_help = 'After this many days we stop attempting a gateway cancellation and queue a manual refund instead, because card cancellation is blocked once the payment has been settled. 0 means no limit.';
$lang->zpay_allow_force_cancel = 'Allow forced cancellation';
$lang->zpay_allow_force_cancel_help = 'Lets an administrator cancel a payment that has already been confirmed. Turn this off to make confirmation final.';
$lang->zpay_force_cancel = 'Force cancellation';
$lang->zpay_force_cancel_confirm = 'I understand this payment is confirmed and want to cancel it';
$lang->zpay_force_cancel_help = 'This payment has already been confirmed. To cancel it you must explicitly choose forced cancellation below.';
$lang->zpay_no_auto_cancel_help = 'This payment method cannot be cancelled automatically. Cancelling updates the records only — an administrator must send the money manually.';
$lang->zpay_manual_refund_title = 'Manual refund';
$lang->zpay_manual_refund_help = 'The cancellation is recorded but the money has not been sent yet. Transfer the amount below, then mark it as sent.';
$lang->zpay_manual_refund_done = 'Mark as sent';
$lang->zpay_manual_refund_sent = 'Refund sent';
$lang->zpay_pending_refund_notice = '%s refund(s) have not been sent yet. Please review them.';

$lang->msg_confirm_success = 'The purchase has been confirmed.';
$lang->msg_already_confirmed = 'This payment was already confirmed.';
$lang->msg_not_confirmable = 'This payment cannot be confirmed in its current state.';
$lang->msg_confirmed_not_cancellable = 'A confirmed payment cannot be cancelled. It requires a forced cancellation by an administrator.';
$lang->msg_force_cancel_disabled = 'Forced cancellation of confirmed payments is not allowed.';
$lang->msg_cancel_manual_refund_queued = 'Cancelled. This payment method cannot be refunded automatically, so an administrator must send the money manually.';
$lang->msg_no_pending_refund = 'There is no refund awaiting transfer.';
$lang->msg_refund_completed = 'Recorded as sent.';
$lang->zpay_paypal_allow_krw = 'Converted payment for KRW orders';
$lang->zpay_paypal_allow_krw_label = 'Allow PayPal for orders priced in KRW';
$lang->zpay_paypal_allow_krw_help = 'PayPal does not settle in Korean won. With this on, a KRW order is converted into the payment currency chosen above. The buyer sees a foreign currency checkout, and the amount actually refunded can differ from the order total because the rate moves. If you sell abroad, pricing the product in that currency is the safer route. An exchange rate must be set for this to work.';
