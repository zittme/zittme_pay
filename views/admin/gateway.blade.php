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

		{{-- 공용 환율 - 행은 활성 통화(기준 설정의 추가 결제 통화 + 기준 통화가 KRW 가 아니면 기준 통화)로 자동 구성된다 --}}
		{{-- 자동 갱신이 켜져 있으면 값은 API 가 채우고, 수동 고정을 켠 통화만 직접 입력한다 --}}
		<h3>{{ $lang->zpay_exchange_rates }}</h3>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_exchange_rates }}</label>
			<div class="x_controls">
				@php
				$zfx_auto = ($pay_config->exchange_auto ?? 'N') === 'Y';
				$zfx_names = \Zittme\Modules\Zittme_pay\Models\Currency::MAJOR_CURRENCIES;
				$zfx_base = strtoupper((string)$pay_config->currency ?: 'KRW');
				$zfx_active = is_array($pay_config->extra_currencies ?? null) ? $pay_config->extra_currencies : [];
				@endphp
				@if ($zfx_base !== 'KRW' && !in_array($zfx_base, $zfx_active, true))
				@php $zfx_active[] = $zfx_base; @endphp
				@endif
				@if (!count($zfx_active))
				<p class="x_help-block" style="margin:6px 0">{{ $lang->zpay_fx_no_active }}</p>
				@else
				<table class="x_table" id="zpay-fx-table">
					<thead>
						<tr>
							<th style="width:190px">{{ $lang->zpay_fx_code }}</th>
							<th>{{ $lang->zpay_fx_rate }}</th>
							<th style="width:120px">{{ $lang->zpay_fx_manual }}</th>
						</tr>
					</thead>
					<tbody>
						{{-- 체크박스는 체크된 것만 전송되므로, 줄마다 인덱스를 박아 통화·환율·수동고정을 짝지운다 --}}
						@php $fx_i = 0; @endphp
						@foreach($zfx_active as $fx_code)
						@php $fx_manual_on = ($pay_config->exchange_rates_manual[$fx_code] ?? '') === 'Y'; @endphp
						<tr>
							<td>
								<input type="hidden" name="fx_code[{{ $fx_i }}]" value="{{ $fx_code }}" />
								<strong>{{ $fx_code }}</strong> <span style="color:#8b95a1">{{ $zfx_names[$fx_code] ?? '' }}</span>
							</td>
							<td><input type="text" name="fx_rate[{{ $fx_i }}]" value="{{ $pay_config->exchange_rates[$fx_code] ?? '' }}" placeholder="{{ $zfx_auto ? $lang->zpay_fx_auto_ph : '1350' }}" @if ($zfx_auto && !$fx_manual_on) readonly style="background:rgba(128,128,128,.08)" @endif /></td>
							<td><label class="x_inline"><input type="checkbox" name="fx_manual[{{ $fx_i }}]" value="Y" @if ($fx_manual_on) checked @endif onchange="var r=this.closest('tr').querySelector('[name^=fx_rate]'); r.readOnly={{ $zfx_auto ? '!this.checked' : 'false' }}; r.style.background=r.readOnly?'rgba(128,128,128,.08)':'';" /> {{ $lang->zpay_fx_manual_short }}</label></td>
						</tr>
						@php $fx_i++; @endphp
						@endforeach
					</tbody>
				</table>
				@endif
				<p class="x_help-block">
					{{ $lang->zpay_exchange_rates_help }}
					@if ($zfx_auto && $pay_config->exchange_updated)
					<br />{{ $lang->zpay_fx_updated }}: {{ zdate($pay_config->exchange_updated, 'Y-m-d H:i') }}
					@endif
				</p>
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

		{{-- 테스트/운영 주소가 갈리는 대행사다. 키가 이 모드의 것이 아니면 인증부터 실패한다 --}}
		<p class="zpay-mode @if ($pay_config->test_mode === 'Y') is-test @endif">{{ $pay_config->test_mode === 'Y' ? $lang->paypal_mode_sandbox : $lang->paypal_mode_live }}</p>

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

		<p class="zpay-mode @if ($pay_config->test_mode === 'Y') is-test @endif">{{ $pay_config->test_mode === 'Y' ? $lang->paypal_mode_sandbox : $lang->paypal_mode_live }}</p>

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

		<p class="zpay-mode @if ($pay_config->test_mode === 'Y') is-test @endif">{{ $pay_config->test_mode === 'Y' ? $lang->paypal_mode_sandbox : $lang->paypal_mode_live }}</p>

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
			<label class="x_control-label">{{ $lang->zpay_paypal_mode }}</label>
			<div class="x_controls">
				<p class="zpay-mode @if ($pay_config->test_mode === 'Y') is-test @endif">
					{{ $pay_config->test_mode === 'Y' ? $lang->paypal_mode_sandbox : $lang->paypal_mode_live }}
				</p>
				<p class="x_help-block">{{ $lang->zpay_paypal_mode_help }}</p>
			</div>
		</div>

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
				<p><button type="button" class="x_btn" id="zpayPaypalTest">{{ $lang->zpay_paypal_test }}</button></p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label" for="zpay_paypal_currency">{{ $lang->zpay_paypal_currency }}</label>
			<div class="x_controls">
				<select id="zpay_paypal_currency" name="paypal_currency">
					@foreach ($paypal_currencies ?? [] as $zpay_cur)
					<option value="{{ $zpay_cur }}" @if ($pay_config->paypal_currency === $zpay_cur) selected @endif>{{ $zpay_cur }}</option>
					@endforeach
				</select>
				<p class="x_help-block">{{ $lang->zpay_paypal_currency_help }}</p>
			</div>
		</div>

		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->zpay_paypal_allow_krw }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="checkbox" name="paypal_allow_krw" value="Y" @if (($pay_config->paypal_allow_krw ?? 'N') === 'Y') checked @endif />
					{{ $lang->zpay_paypal_allow_krw_label }}
				</label>
				<p class="x_help-block">{{ $lang->zpay_paypal_allow_krw_help }}</p>
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
							<th>{{ $lang->zpay_bank_extra }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($pay_config->bank_accounts as $account)
						@php $zpay_extra_text = implode("\n", array_map(function($e) { return ($e['label'] ?? '') . '=' . ($e['value'] ?? ''); }, $account['extra'] ?? [])); @endphp
						<tr>
							<td><input type="text" name="bank_name[]" value="{{ $account['bank'] ?? '' }}" /></td>
							<td><input type="text" name="bank_account[]" value="{{ $account['account'] ?? '' }}" /></td>
							<td><input type="text" name="bank_holder[]" value="{{ $account['holder'] ?? '' }}" /></td>
							<td><textarea name="bank_extra[]" rows="2" placeholder="{{ $lang->zpay_bank_extra_ph }}">{{ $zpay_extra_text }}</textarea></td>
						</tr>
						@endforeach
						{{-- 항상 빈 줄을 하나 둬서 계좌를 더 넣을 수 있게 한다. --}}
						<tr>
							<td><input type="text" name="bank_name[]" value="" /></td>
							<td><input type="text" name="bank_account[]" value="" /></td>
							<td><input type="text" name="bank_holder[]" value="" /></td>
							<td><textarea name="bank_extra[]" rows="2" placeholder="{{ $lang->zpay_bank_extra_ph }}"></textarea></td>
						</tr>
						<tr>
							<td><input type="text" name="bank_name[]" value="" /></td>
							<td><input type="text" name="bank_account[]" value="" /></td>
							<td><input type="text" name="bank_holder[]" value="" /></td>
							<td><textarea name="bank_extra[]" rows="2" placeholder="{{ $lang->zpay_bank_extra_ph }}"></textarea></td>
						</tr>
					</tbody>
				</table>
				<p class="x_help-block">{{ $lang->zpay_bank_accounts_help }} {{ $lang->zpay_bank_extra_help }}</p>
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

<style>
.zpay-mode { display: inline-block; margin: 0; padding: 5px 12px; border-radius: 999px; background: rgba(38,119,227,.1); color: #2677e3; font-size: 13px; font-weight: 700; }
.zpay-mode.is-test { background: rgba(234,152,8,.14); color: #a06a08; }
</style>

<script>
(function () {
	var btn = document.getElementById('zpayPaypalTest');
	if (!btn) return;
	btn.addEventListener('click', function () {
		var label = btn.textContent;
		btn.textContent = {!! json_encode($lang->zpay_paypal_testing) !!};
		btn.disabled = true;
		exec_json('zittme_pay.procZittme_payAdminTestPaypal', {
			paypal_client_id: document.getElementById('zpay_paypal_client_id').value,
			paypal_secret: document.getElementById('zpay_paypal_secret').value
		}, function (ret) {
			alert(ret.message);
		}, function (ret) {
			alert(ret.message);
		}).always(function () {
			btn.textContent = label;
			btn.disabled = false;
		});
	});
})();
</script>
