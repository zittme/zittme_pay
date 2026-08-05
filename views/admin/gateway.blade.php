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
