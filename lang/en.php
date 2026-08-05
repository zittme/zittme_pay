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

// General
$lang->zpay_enabled = 'Enable payments';
$lang->zpay_enabled_help = 'Turn this off to stop accepting new payments.';
$lang->zpay_test_mode = 'Test mode';
$lang->zpay_test_mode_help = 'Indicates that the gateway test keys are in use. No real money moves.';
$lang->zpay_currency = 'Currency';
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
$lang->zpay_bank_due_days = 'Payment window (days)';
$lang->zpay_bank_due_days_help = 'The order expires once this period has passed.';

// Checkout
$lang->zpay_checkout_title = 'Checkout';
$lang->zpay_order_summary = 'Order summary';
$lang->zpay_order_code = 'Order number';
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
