<?php

$lang->zittme_pay = 'Zittme 支付';

// 管理选项卡
$lang->zpay_tab_config = '基本';
$lang->zpay_tab_gateway = '支付方式';
$lang->zpay_tab_orders = '支付记录';
$lang->zpay_tab_logs = '通信日志';

$lang->about_zpay_config = '设置支付引擎的通用行为。商城、预约等需要支付的模块共用这些设置。';
$lang->about_zpay_gateway = '启用需要的支付方式并填写密钥。未填写密钥的支付方式不会出现在结算页面。';
$lang->about_zpay_logs = '与支付相关的全部请求与响应。这是发生纠纷时的凭据，请设置足够长的保留期限。';

// 支付方式名称
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = '银行转账';

// 基本设置
$lang->zpay_enabled = '启用支付';
$lang->zpay_enabled_help = '关闭后将不再接受新的支付。';
$lang->zpay_test_mode = '测试模式';
$lang->zpay_test_mode_help = '表示当前使用支付网关的测试密钥，不会发生真实扣款。';
$lang->zpay_currency = '结算货币';
$lang->zpay_order_prefix = '订单号前缀';
$lang->zpay_order_prefix_help = '便于在支付网关后台辨认本站订单。字母与数字，最多 8 个字符。';

$lang->zpay_group_cancel = '取消与退款';
$lang->zpay_allow_partial_cancel = '允许部分取消';
$lang->zpay_allow_partial_cancel_help = '允许只退还部分已支付金额。';
$lang->zpay_cancel_reasons = '取消原因列表';
$lang->zpay_cancel_reasons_help = '每行一个。留空则每次手动输入。';

$lang->zpay_group_notify = '通知';
$lang->zpay_notify_admin_email = '管理员通知邮箱';
$lang->zpay_notify_events = '通知时机';
$lang->zpay_notify_on_paid = '支付完成';
$lang->zpay_notify_on_cancel = '支付取消';

$lang->zpay_group_security = '安全与日志';
$lang->zpay_log_retention_days = '日志保留天数';
$lang->zpay_log_retention_days_help = '设为 0 则永不删除。这些日志是纠纷凭据，请勿设置过短。';
$lang->zpay_webhook_ip_whitelist = 'Webhook 允许 IP';
$lang->zpay_webhook_ip_whitelist_help = '每行一个。留空则不限制 IP。结尾加 * 可指定网段。IP 只是辅助手段，真正的防线是每次收到 Webhook 都向支付网关重新查询。';

$lang->zpay_group_notice = '政策声明';
$lang->zpay_biz_notice = '结算页面声明文字';
$lang->zpay_biz_notice_help = '营业执照编号等需要显示在结算页面底部的文字。';

// 支付方式设置
$lang->zpay_enabled_gateways = '启用的支付方式';
$lang->zpay_enabled_gateways_help = '只有勾选的支付方式会显示在结算页面。';
$lang->zpay_not_configured = '未填写密钥';
$lang->zpay_toss_client_key = '客户端密钥';
$lang->zpay_toss_secret_key = '私密密钥';
$lang->zpay_toss_key_help = '在 Toss Payments 商户后台获取。测试密钥以 test_ 开头，正式密钥以 live_ 开头。';
$lang->zpay_webhook_url = 'Webhook 地址';
$lang->zpay_webhook_url_help = '请在支付网关后台的 Webhook 设置中登记此地址，用于接收虚拟账户入账等异步通知。';
$lang->zpay_bank_accounts = '收款账户';
$lang->zpay_bank_accounts_help = '只有同时填写银行和账号的行才会保存。清空该行即可删除。';
$lang->zpay_bank_name = '银行';
$lang->zpay_bank_account = '账号';
$lang->zpay_bank_holder = '开户名';
$lang->zpay_bank_due_days = '付款期限（天）';
$lang->zpay_bank_due_days_help = '超过期限后订单将按过期处理。';

// 结算页面
$lang->zpay_checkout_title = '结算';
$lang->zpay_order_summary = '订单内容';
$lang->zpay_order_code = '订单号';
$lang->zpay_product = '商品';
$lang->zpay_payer = '付款人';
$lang->zpay_payer_phone = '联系电话';
$lang->zpay_payer_email = '邮箱';
$lang->zpay_amount = '支付金额';
$lang->zpay_select_method = '选择支付方式';
$lang->zpay_depositor_name = '汇款人姓名';
$lang->zpay_bank_due_notice = '请在下单后 %d 天内完成转账。超过期限订单将自动取消。';
$lang->zpay_pay_button = '支付 %s';

// 支付结果
$lang->zpay_result_paid = '支付已完成';
$lang->zpay_result_pending = '正在等待您的转账';
$lang->zpay_result_cancelled = '支付已取消';
$lang->zpay_result_expired = '支付期限已过';
$lang->zpay_result_failed = '支付失败';
$lang->zpay_bank_guide_title = '转账信息';
$lang->zpay_due_date = '付款期限';
$lang->zpay_receipt = '查看收据';
$lang->zpay_back_to_shop = '返回';
$lang->zpay_cancelled_amount = '取消金额';

// 支付记录
$lang->zpay_order_detail = '支付详情';
$lang->zpay_source = '支付对象';
$lang->zpay_gateway = '支付方式';
$lang->zpay_pg_tid = '网关交易号';
$lang->zpay_status = '状态';
$lang->zpay_regdate = '创建时间';
$lang->zpay_paid_date = '支付时间';
$lang->zpay_cancelled_date = '取消时间';
$lang->zpay_ipaddress = 'IP 地址';
$lang->zpay_remain_amount = '剩余金额';
$lang->zpay_total_orders = '共 %s 条';
$lang->zpay_no_orders = '暂无支付记录。';
$lang->zpay_filter_all_status = '全部状态';
$lang->zpay_confirm_deposit = '确认到账';
$lang->zpay_confirm_deposit_help = '在确认款项到账后点击。点击后立即视为支付完成，并通知发起支付的模块。';
$lang->zpay_cancel_payment = '取消支付';
$lang->zpay_cancel_amount = '取消金额';
$lang->zpay_cancel_amount_help = '不能取消超过剩余金额的部分。';
$lang->zpay_cancel_reason = '取消原因';

// 状态
$lang->zpay_status_ready = '待支付';
$lang->zpay_status_pending = '待到账';
$lang->zpay_status_paid = '支付完成';
$lang->zpay_status_cancelled = '已取消';
$lang->zpay_status_partial_cancelled = '部分取消';
$lang->zpay_status_failed = '失败';
$lang->zpay_status_expired = '已过期';

// 日志
$lang->zpay_communication_log = '通信日志';
$lang->zpay_log_action = '动作';
$lang->zpay_log_result = '结果';
$lang->zpay_log_response = '响应';
$lang->zpay_no_logs = '暂无记录。';
$lang->zpay_total_logs = '共 %s 条';
$lang->zpay_filter_all_action = '全部动作';
$lang->zpay_filter_all_result = '全部结果';
$lang->zpay_result_success = '成功';
$lang->zpay_result_fail = '失败';
$lang->zpay_purge_logs = '删除 %d 天前的日志';

// 提示信息
$lang->msg_pay_disabled = '支付功能已关闭。';
$lang->msg_invalid_source = '支付对象无效。';
$lang->msg_invalid_amount = '支付金额无效。';
$lang->msg_order_not_found = '找不到支付订单。';
$lang->msg_no_gateway_available = '没有可用的支付方式，请联系管理员。';
$lang->msg_gateway_not_found = '找不到该支付方式。';
$lang->msg_invalid_ticket = '支付信息已过期，请重新开始。';
$lang->msg_already_settled = '该支付已处理完成。';
$lang->msg_too_many_requests = '支付尝试过于频繁，请稍后再试。';

$lang->msg_approve_success = '支付已通过。';
$lang->msg_approve_failed = '支付未能通过。';
$lang->msg_payment_cancelled = '支付已取消。';
$lang->msg_payment_not_completed = '该支付尚未完成。';
$lang->msg_amount_mismatch = '支付金额与订单金额不一致，已中止支付。';
$lang->msg_missing_payment_key = '缺少网关交易号。';
$lang->msg_unknown_pg_status = '未知的支付状态。';
$lang->msg_pg_error = '与支付网关通信时发生错误。';
$lang->msg_pg_unreachable = '无法连接支付网关服务器。';
$lang->msg_query_not_supported = '该支付方式不支持查询。';

$lang->msg_cancel_success = '支付已取消。';
$lang->msg_cancel_failed = '取消支付失败。';
$lang->msg_not_cancellable = '当前状态无法取消。';
$lang->msg_invalid_cancel_amount = '取消金额无效。';
$lang->msg_partial_cancel_disabled = '不允许部分取消。';
$lang->msg_cancel_record_failed = '支付网关已取消，但本站记录更新失败，请联系管理员。';
$lang->cancel_default_reason = '客户要求';

$lang->msg_no_bank_account = '尚未登记收款账户。';
$lang->msg_bank_registered = '已显示收款账户信息，请在期限内完成转账。';
$lang->msg_bank_manual_refund = '银行转账的退款需由管理员手动汇出。';
$lang->msg_not_pending = '该订单不处于待到账状态。';
$lang->msg_deposit_confirmed = '已确认到账，支付处理完成。';
$lang->msg_log_retention_disabled = '日志保留天数设为 0，因此不会删除。';

// 确认收货与手动退款
$lang->zpay_status_confirmed = '已确认收货';
$lang->zpay_confirm_date = '确认收货时间';
$lang->zpay_auto_cancel_days = '网关可取消期限（天）';
$lang->zpay_auto_cancel_days_help = '支付后超过该期限将不再尝试网关取消，转为手动退款。因为结算完成后银行卡取消会被拒绝。设为 0 表示不限制。';
$lang->zpay_allow_force_cancel = '允许强制取消已确认订单';
$lang->zpay_allow_force_cancel_help = '允许管理员取消已确认收货的支付。关闭后，确认收货即为最终状态。';
$lang->zpay_force_cancel = '强制取消';
$lang->zpay_force_cancel_confirm = '我知道该支付已确认收货，仍要取消';
$lang->zpay_force_cancel_help = '该支付已确认收货。如需取消，必须在下方明确勾选强制取消。';
$lang->zpay_no_auto_cancel_help = '该支付方式无法自动取消。取消只会整理记录，实际退款需由管理员手动汇出。';
$lang->zpay_manual_refund_title = '手动退款';
$lang->zpay_manual_refund_help = '取消已记录，但款项尚未汇出。请汇出下列金额后点击完成。';
$lang->zpay_manual_refund_done = '标记为已汇出';
$lang->zpay_manual_refund_sent = '已汇出';
$lang->zpay_pending_refund_notice = '有 %s 笔退款尚未汇出，请及时处理。';

$lang->msg_confirm_success = '已确认收货。';
$lang->msg_already_confirmed = '该支付已确认收货。';
$lang->msg_not_confirmable = '当前状态无法确认收货。';
$lang->msg_confirmed_not_cancellable = '已确认收货的支付无法取消，需要管理员强制取消。';
$lang->msg_force_cancel_disabled = '未允许强制取消已确认的支付。';
$lang->msg_cancel_manual_refund_queued = '已取消。该支付方式无法自动退款，需由管理员手动汇出。';
$lang->msg_no_pending_refund = '没有待汇出的退款。';
$lang->msg_refund_completed = '已记录为汇出完成。';
