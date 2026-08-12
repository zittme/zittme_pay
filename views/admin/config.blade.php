@include('_tabs')

<section class="section">
	<h2>{{ $lang->zpay_tab_config }}</h2>
	<p class="x_help-block">{{ $lang->about_zpay_config }}</p>

	<form action="./" method="post" class="x_form-horizontal">
		<input type="hidden" name="module" value="zittme_pay" />
		<input type="hidden" name="act" value="procZittme_payAdminInsertConfig" />
		<input type="hidden" name="tab" value="config" />
		<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', $act) }}" />

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_enabled }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="enabled" value="Y" @if($pay_config->enabled === 'Y') checked="checked" @endif />
					{{ $lang->zpay_enabled_help }}
				</label>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_test_mode }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="test_mode" value="Y" @if($pay_config->test_mode === 'Y') checked="checked" @endif />
					{{ $lang->zpay_test_mode_help }}
				</label>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_currency">{{ $lang->zpay_currency }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_currency" name="currency" value="{{ $pay_config->currency }}" maxlength="8" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_order_prefix">{{ $lang->zpay_order_prefix }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_order_prefix" name="order_prefix" value="{{ $pay_config->order_prefix }}" maxlength="8" />
				<p class="x_help-block">{{ $lang->zpay_order_prefix_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->zpay_group_cancel }}</h3>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_allow_partial_cancel }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="allow_partial_cancel" value="Y" @if($pay_config->allow_partial_cancel === 'Y') checked="checked" @endif />
					{{ $lang->zpay_allow_partial_cancel_help }}
				</label>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_cancel_reasons">{{ $lang->zpay_cancel_reasons }}</label>
			<div class="x_controls">
				<textarea id="zpay_cancel_reasons" name="cancel_reasons" rows="4" class="x_full-width">{{ $pay_config->cancel_reasons }}</textarea>
				<p class="x_help-block">{{ $lang->zpay_cancel_reasons_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_auto_cancel_days">{{ $lang->zpay_auto_cancel_days }}</label>
			<div class="x_controls">
				<input type="number" id="zpay_auto_cancel_days" name="auto_cancel_days" value="{{ $pay_config->auto_cancel_days }}" min="0" max="365" />
				<p class="x_help-block">{{ $lang->zpay_auto_cancel_days_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_allow_force_cancel }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="allow_force_cancel" value="Y" @if($pay_config->allow_force_cancel === 'Y') checked="checked" @endif />
					{{ $lang->zpay_allow_force_cancel_help }}
				</label>
			</div>
		</div>

		<h3>{{ $lang->zpay_group_notify }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_notify_admin_email">{{ $lang->zpay_notify_admin_email }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_notify_admin_email" name="notify_admin_email" value="{{ $pay_config->notify_admin_email }}" class="x_full-width" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_notify_events }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="notify_on_paid" value="Y" @if($pay_config->notify_on_paid === 'Y') checked="checked" @endif />
					{{ $lang->zpay_notify_on_paid }}
				</label>
				<label class="x_inline">
					<input type="checkbox" name="notify_on_cancel" value="Y" @if($pay_config->notify_on_cancel === 'Y') checked="checked" @endif />
					{{ $lang->zpay_notify_on_cancel }}
				</label>
			</div>
		</div>

		<h3>{{ $lang->zpay_group_security }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_log_retention_days">{{ $lang->zpay_log_retention_days }}</label>
			<div class="x_controls">
				<input type="number" id="zpay_log_retention_days" name="log_retention_days" value="{{ $pay_config->log_retention_days }}" min="0" max="3650" />
				<p class="x_help-block">{{ $lang->zpay_log_retention_days_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_webhook_ip_whitelist">{{ $lang->zpay_webhook_ip_whitelist }}</label>
			<div class="x_controls">
				<textarea id="zpay_webhook_ip_whitelist" name="webhook_ip_whitelist" rows="3" class="x_full-width">{{ $pay_config->webhook_ip_whitelist }}</textarea>
				<p class="x_help-block">{{ $lang->zpay_webhook_ip_whitelist_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->zpay_group_notice }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_biz_notice">{{ $lang->zpay_biz_notice }}</label>
			<div class="x_controls">
				<textarea id="zpay_biz_notice" name="biz_notice" rows="4" class="x_full-width">{{ $pay_config->biz_notice }}</textarea>
				<p class="x_help-block">{{ $lang->zpay_biz_notice_help }}</p>
			</div>
		</div>

		<div class="x_clearfix">
			<div class="x_pull-right">
				<button type="submit" class="x_btn x_btn-primary">{{ $lang->cmd_save }}</button>
			</div>
		</div>
	</form>

	<div class="section" style="margin-top:18px">
		<h1>스킨 설정</h1>
		<form action="./" method="post">
			<input type="hidden" name="module" value="zittme_pay" />
			<input type="hidden" name="act" value="procZittme_payAdminUpdateSkin" />
			<div class="x_control-group">
				<label class="x_control-label" for="zpay_skin">결제 화면 스킨</label>
				<div class="x_controls">
					<select id="zpay_skin" name="skin">
						<option value="/USE_DEFAULT/" @if (($zpay_instance->skin ?? '') === '/USE_DEFAULT/' || ($zpay_instance->skin ?? '') === '') selected @endif>기본 디자인 따름 (현재: {{ $zpay_default_skin }})</option>
						@foreach ($zpay_skins as $zpay_sk)
						<option value="{{ $zpay_sk->skin }}" @if (($zpay_instance->skin ?? '') === $zpay_sk->skin) selected @endif>{{ $zpay_sk->title ?: $zpay_sk->skin }}</option>
						@endforeach
					</select>
					<p class="x_help-block">기본 디자인 따름으로 두면 사이트 디자인 설정(테마 적용 포함)의 스킨을 그대로 씁니다.</p>
				</div>
			</div>
			<div class="x_clearfix">
				<div class="x_pull-right">
					<button type="submit" class="x_btn x_btn-primary">스킨 적용</button>
				</div>
			</div>
		</form>
	</div>
</section>
