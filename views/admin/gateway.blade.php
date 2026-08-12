@include('_tabs')

<section class="section">
	<h2>{{ $lang->zpay_tab_gateway }}</h2>
	<p class="x_help-block">{{ $lang->about_zpay_gateway }}</p>

	<form action="./" method="post" class="x_form-horizontal">
		<input type="hidden" name="module" value="zittme_pay" />
		<input type="hidden" name="act" value="procZittme_payAdminInsertConfig" />
		<input type="hidden" name="tab" value="gateway" />
		<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', $act) }}" />

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_enabled_gateways }}</label>
			<div class="x_controls">
				@foreach($drivers as $driver_name => $driver)
				<label class="x_inline">
					<input type="checkbox" name="enabled_gateways[]" value="{{ $driver_name }}" @if($driver['enabled']) checked="checked" @endif />
					{{ $driver['title'] }}
					@if(!$driver['configured'])
					<span class="x_text-error">({{ $lang->zpay_not_configured }})</span>
					@endif
				</label>
				@endforeach
				<p class="x_help-block">{{ $lang->zpay_enabled_gateways_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_toss }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_toss_client_key">{{ $lang->zpay_toss_client_key }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_toss_client_key" name="toss_client_key" value="{{ $pay_config->toss_client_key }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_toss_secret_key">{{ $lang->zpay_toss_secret_key }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_toss_secret_key" name="toss_secret_key" value="{{ $pay_config->toss_secret_key }}" class="x_full-width" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_toss_key_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_webhook_url }}</label>
			<div class="x_controls">
				<input type="text" value="{{ $webhook_url }}" class="x_full-width" readonly onclick="this.select()" />
				<p class="x_help-block">{{ $lang->zpay_webhook_url_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->zpay_exchange_rates }}</h3>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_exchange_rates }}</label>
			<div class="x_controls">
				<table class="x_table" id="zpay-fx-table">
					<thead>
						<tr>
							<th style="width:120px">{{ $lang->zpay_fx_code }}</th>
							<th>{{ $lang->zpay_fx_rate }}</th>
							<th style="width:120px">{{ $lang->zpay_fx_manual }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($pay_config->exchange_rates as $fx_code => $fx_rate)
						<tr>
							<td><input type="text" name="fx_code[]" value="{{ $fx_code }}" maxlength="3" /></td>
							<td><input type="text" name="fx_rate[]" value="{{ $fx_rate }}" /></td>
							<td><label class="x_inline"><input type="checkbox" name="fx_manual[]" value="Y" @if (($pay_config->exchange_rates_manual[$fx_code] ?? '') === 'Y') checked @endif /> {{ $lang->zpay_fx_manual_short }}</label></td>
						</tr>
						@endforeach
						{{-- 항상 빈 줄을 하나 둬서 통화를 더 넣을 수 있게 한다. --}}
						<tr>
							<td><input type="text" name="fx_code[]" value="" maxlength="3" placeholder="USD" /></td>
							<td><input type="text" name="fx_rate[]" value="" placeholder="1350" /></td>
							<td><label class="x_inline"><input type="checkbox" name="fx_manual[]" value="Y" /> {{ $lang->zpay_fx_manual_short }}</label></td>
						</tr>
					</tbody>
				</table>
				<p class="x_help-block">{{ $lang->zpay_exchange_rates_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_exchange_auto }}</label>
			<div class="x_controls">
				<label class="x_inline"><input type="checkbox" name="exchange_auto" value="Y" @if ($pay_config->exchange_auto === 'Y') checked @endif /> {{ $lang->zpay_exchange_auto_label }}</label>
				<select name="exchange_source" style="margin-left:8px">
					<option value="erapi" @if ($pay_config->exchange_source === 'erapi') selected @endif>{{ $lang->zpay_fx_source_erapi }}</option>
					<option value="koreaexim" @if ($pay_config->exchange_source === 'koreaexim') selected @endif>{{ $lang->zpay_fx_source_koreaexim }}</option>
				</select>
				<input type="text" name="exchange_api_key" value="{{ $pay_config->exchange_api_key }}" placeholder="{{ $lang->zpay_fx_api_key }}" style="width:220px;margin-left:8px" autocomplete="off" />
				<p class="x_help-block">
					{{ $lang->zpay_exchange_auto_help }}
					@if ($pay_config->exchange_updated)
					({{ $lang->zpay_fx_updated }}: {{ zdate($pay_config->exchange_updated, 'Y-m-d H:i') }})
					@endif
				</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_inicis }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_inicis_mid">{{ $lang->zpay_inicis_mid }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_inicis_mid" name="inicis_mid" value="{{ $pay_config->inicis_mid }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_inicis_sign_key">{{ $lang->zpay_inicis_sign_key }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_inicis_sign_key" name="inicis_sign_key" value="{{ $pay_config->inicis_sign_key }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_inicis_api_key">{{ $lang->zpay_inicis_api_key }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_inicis_api_key" name="inicis_api_key" value="{{ $pay_config->inicis_api_key }}" class="x_full-width" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_inicis_key_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_kcp }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_kcp_site_cd">{{ $lang->zpay_kcp_site_cd }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_kcp_site_cd" name="kcp_site_cd" value="{{ $pay_config->kcp_site_cd }}" style="width:160px" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_kcp_cert_info">{{ $lang->zpay_kcp_cert_info }}</label>
			<div class="x_controls">
				<textarea id="zpay_kcp_cert_info" name="kcp_cert_info" rows="4" class="x_full-width" autocomplete="off">{{ $pay_config->kcp_cert_info }}</textarea>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_kcp_priv_key">{{ $lang->zpay_kcp_priv_key }}</label>
			<div class="x_controls">
				<textarea id="zpay_kcp_priv_key" name="kcp_priv_key" rows="4" class="x_full-width" autocomplete="off">{{ $pay_config->kcp_priv_key }}</textarea>
				<input type="password" name="kcp_priv_pass" value="{{ $pay_config->kcp_priv_pass }}" placeholder="{{ $lang->zpay_kcp_priv_pass }}" style="width:220px;margin-top:6px" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_kcp_key_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_nicepay }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_nicepay_client_id">{{ $lang->zpay_nicepay_client_id }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_nicepay_client_id" name="nicepay_client_id" value="{{ $pay_config->nicepay_client_id }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_nicepay_secret_key">{{ $lang->zpay_nicepay_secret_key }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_nicepay_secret_key" name="nicepay_secret_key" value="{{ $pay_config->nicepay_secret_key }}" class="x_full-width" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_nicepay_key_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_portone }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_portone_store_id">{{ $lang->zpay_portone_store_id }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_portone_store_id" name="portone_store_id" value="{{ $pay_config->portone_store_id }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_portone_channel_key">{{ $lang->zpay_portone_channel_key }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_portone_channel_key" name="portone_channel_key" value="{{ $pay_config->portone_channel_key }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_portone_api_secret">{{ $lang->zpay_portone_api_secret }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_portone_api_secret" name="portone_api_secret" value="{{ $pay_config->portone_api_secret }}" class="x_full-width" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_portone_key_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_paypal }}</h3>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_paypal_client_id">{{ $lang->zpay_paypal_client_id }}</label>
			<div class="x_controls">
				<input type="text" id="zpay_paypal_client_id" name="paypal_client_id" value="{{ $pay_config->paypal_client_id }}" class="x_full-width" autocomplete="off" />
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_paypal_secret">{{ $lang->zpay_paypal_secret }}</label>
			<div class="x_controls">
				<input type="password" id="zpay_paypal_secret" name="paypal_secret" value="{{ $pay_config->paypal_secret }}" class="x_full-width" autocomplete="off" />
				<p class="x_help-block">{{ $lang->zpay_paypal_key_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_paypal_currency">{{ $lang->zpay_paypal_currency }}</label>
			<div class="x_controls">
				<select id="zpay_paypal_currency" name="paypal_currency">
					@foreach (['USD', 'EUR', 'JPY', 'GBP', 'AUD', 'CAD', 'SGD', 'HKD', 'TWD', 'CNY'] as $zpay_cur)
					<option value="{{ $zpay_cur }}" @if ($pay_config->paypal_currency === $zpay_cur) selected @endif>{{ $zpay_cur }}</option>
					@endforeach
				</select>
				<p class="x_help-block">{{ $lang->zpay_paypal_currency_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_paypal_exchange_rate }}</label>
			<div class="x_controls">
				<p class="x_help-block">{{ $lang->zpay_paypal_rate_shared_help }}</p>
			</div>
		</div>

		<h3>{{ $lang->gateway_banktransfer }}</h3>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_bank_accounts }}</label>
			<div class="x_controls">
				<table class="x_table" id="zpay-bank-table">
					<thead>
						<tr>
							<th>{{ $lang->zpay_bank_name }}</th>
							<th>{{ $lang->zpay_bank_account }}</th>
							<th>{{ $lang->zpay_bank_holder }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($pay_config->bank_accounts as $account)
						<tr>
							<td><input type="text" name="bank_name[]" value="{{ $account['bank'] ?? '' }}" /></td>
							<td><input type="text" name="bank_account[]" value="{{ $account['account'] ?? '' }}" /></td>
							<td><input type="text" name="bank_holder[]" value="{{ $account['holder'] ?? '' }}" /></td>
						</tr>
						@endforeach
						{{-- 항상 빈 줄을 하나 둬서 계좌를 더 넣을 수 있게 한다. --}}
						<tr>
							<td><input type="text" name="bank_name[]" value="" /></td>
							<td><input type="text" name="bank_account[]" value="" /></td>
							<td><input type="text" name="bank_holder[]" value="" /></td>
						</tr>
						<tr>
							<td><input type="text" name="bank_name[]" value="" /></td>
							<td><input type="text" name="bank_account[]" value="" /></td>
							<td><input type="text" name="bank_holder[]" value="" /></td>
						</tr>
					</tbody>
				</table>
				<p class="x_help-block">{{ $lang->zpay_bank_accounts_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_bank_due_days">{{ $lang->zpay_bank_due_days }}</label>
			<div class="x_controls">
				<input type="number" id="zpay_bank_due_days" name="bank_due_days" value="{{ $pay_config->bank_due_days }}" min="1" max="30" />
				<p class="x_help-block">{{ $lang->zpay_bank_due_days_help }}</p>
			</div>
		</div>

		<div class="x_clearfix">
			<div class="x_pull-right">
				<button type="submit" class="x_btn x_btn-primary">{{ $lang->cmd_save }}</button>
			</div>
		</div>
	</form>
</section>
