@include('_tabs')

<section class="section">
	<h2>{{ $lang->zpay_order_detail }}</h2>

	<table class="x_table">
		<tbody>
			<tr>
				<th>{{ $lang->zpay_order_code }}</th>
				<td>{{ $order->order_code }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_status }}</th>
				<td>{{ $status_labels[$order->status] ?? $order->status }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_product }}</th>
				<td>{{ $order->title }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_source }}</th>
				<td>{{ $order->source_module }} #{{ $order->source_srl }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_payer }}</th>
				<td>
					{{ $order->payer_name }}
					@if($order->payer_phone) / {{ $order->payer_phone }} @endif
					@if($order->payer_email) / {{ $order->payer_email }} @endif
				</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_amount }}</th>
				<td>{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($order->amount, $order->currency ?? 'KRW') }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_cancelled_amount }}</th>
				<td>
					{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($order->cancelled_amount, $order->currency ?? 'KRW') }}
					({{ $lang->zpay_remain_amount }} {{ \Zittme\Modules\Zittme_pay\Models\Currency::money($order->remain_amount, $order->currency ?? 'KRW') }})
				</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_gateway }}</th>
				<td>{{ $order->gateway }} @if($order->pay_method) / {{ $order->pay_method }} @endif</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_pg_tid }}</th>
				<td>{{ $order->pg_tid }}</td>
			</tr>
			<tr>
				<th>{{ $lang->zpay_regdate }}</th>
				<td>{{ zdate($order->regdate, 'Y-m-d H:i:s') }}</td>
			</tr>
			@if($order->paid_date)
			<tr>
				<th>{{ $lang->zpay_paid_date }}</th>
				<td>{{ zdate($order->paid_date, 'Y-m-d H:i:s') }}</td>
			</tr>
			@endif
			@if($order->cancelled_date)
			<tr>
				<th>{{ $lang->zpay_cancelled_date }}</th>
				<td>{{ zdate($order->cancelled_date, 'Y-m-d H:i:s') }}</td>
			</tr>
			@endif
			@if($order->confirm_date)
			<tr>
				<th>{{ $lang->zpay_confirm_date }}</th>
				<td>{{ zdate($order->confirm_date, 'Y-m-d H:i:s') }}</td>
			</tr>
			@endif
			@if($order->due_date)
			<tr>
				<th>{{ $lang->zpay_due_date }}</th>
				<td>{{ zdate($order->due_date, 'Y-m-d H:i') }}</td>
			</tr>
			@endif
			<tr>
				<th>{{ $lang->zpay_ipaddress }}</th>
				<td>{{ $order->ipaddress }}</td>
			</tr>
		</tbody>
	</table>

	@if($can_confirm_deposit)
	<div class="x_well">
		<h3>{{ $lang->zpay_confirm_deposit }}</h3>
		<p class="x_help-block">{{ $lang->zpay_confirm_deposit_help }}</p>
		<form action="./" method="post">
			<input type="hidden" name="module" value="zittme_pay" />
			<input type="hidden" name="act" value="procZittme_payAdminConfirmDeposit" />
			<input type="hidden" name="order_srl" value="{{ $order->order_srl }}" />
			<button type="submit" class="x_btn x_btn-primary">{{ $lang->zpay_confirm_deposit }}</button>
		</form>
	</div>
	@endif

	{{-- 돌려주기로 했는데 아직 송금하지 않은 돈. 이 상자가 보이면 관리자가 할 일이 남았다. --}}
	@if($needs_manual_refund)
	<div class="x_well">
		<h3 class="x_text-error">{{ $lang->zpay_manual_refund_title }}</h3>
		<p class="x_help-block">{{ $lang->zpay_manual_refund_help }}</p>
		<p>
			<strong>{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($order->refund_amount, $order->currency ?? 'KRW') }}</strong>
			@if($order->extra['bank'] ?? '')
			— {{ $order->extra['bank'] }} {{ $order->extra['account'] ?? '' }}
			({{ $order->extra['depositor_name'] ?? ($order->extra['holder'] ?? '') }})
			@foreach($order->extra['bank_extra'] ?? [] as $zpay_ex) / {{ $zpay_ex['label'] ?? '' }} {{ $zpay_ex['value'] ?? '' }}@endforeach
			@endif
		</p>
		<form action="./" method="post">
			<input type="hidden" name="module" value="zittme_pay" />
			<input type="hidden" name="act" value="procZittme_payAdminCompleteRefund" />
			<input type="hidden" name="order_srl" value="{{ $order->order_srl }}" />
			<button type="submit" class="x_btn x_btn-primary">{{ $lang->zpay_manual_refund_done }}</button>
		</form>
	</div>
	@elseif($order->refund_state === 'done')
	<div class="x_well">
		<h3>{{ $lang->zpay_manual_refund_title }}</h3>
		<p>
			{{ $lang->zpay_manual_refund_sent }} —
			{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($order->refund_amount, $order->currency ?? 'KRW') }}
			@if($order->refund_date) ({{ zdate($order->refund_date, 'Y-m-d H:i') }}) @endif
		</p>
	</div>
	@endif

	@if($can_cancel || $can_force_cancel)
	<div class="x_well">
		<h3>{{ $lang->zpay_cancel_payment }}</h3>

		@if(!$supports_auto_cancel)
		<p class="x_help-block x_text-error">{{ $lang->zpay_no_auto_cancel_help }}</p>
		@endif

		@if($can_force_cancel)
		<p class="x_help-block x_text-error">{{ $lang->zpay_force_cancel_help }}</p>
		@endif

		<form action="./" method="post" class="x_form-horizontal">
			<input type="hidden" name="module" value="zittme_pay" />
			<input type="hidden" name="act" value="procZittme_payCancel" />
			<input type="hidden" name="order_srl" value="{{ $order->order_srl }}" />

			<div class="x_control-group">
				<label class="x_control-label" for="zpay_cancel_amount">{{ $lang->zpay_cancel_amount }}</label>
				<div class="x_controls">
					<input type="number" id="zpay_cancel_amount" name="cancel_amount" value="{{ $order->remain_amount }}" min="1" max="{{ $order->remain_amount }}" />
					<p class="x_help-block">{{ $lang->zpay_cancel_amount_help }}</p>
				</div>
			</div>

			<div class="x_control-group">
				<label class="x_control-label" for="zpay_cancel_reason">{{ $lang->zpay_cancel_reason }}</label>
				<div class="x_controls">
					@if(count($cancel_reasons))
					<select name="cancel_reason" id="zpay_cancel_reason">
						@foreach($cancel_reasons as $reason)
						<option value="{{ $reason }}">{{ $reason }}</option>
						@endforeach
					</select>
					@else
					<input type="text" id="zpay_cancel_reason" name="cancel_reason" class="x_full-width" />
					@endif
				</div>
			</div>

			@if($can_force_cancel)
			<div class="x_control-group">
				<label class="x_control-label">{{ $lang->zpay_force_cancel }}</label>
				<div class="x_controls">
					<label class="x_inline">
						<input type="checkbox" name="force_cancel" value="Y" />
						{{ $lang->zpay_force_cancel_confirm }}
					</label>
				</div>
			</div>
			@endif

			<button type="submit" class="x_btn x_btn-danger">{{ $lang->zpay_cancel_payment }}</button>
		</form>
	</div>
	@endif

	<h3>{{ $lang->zpay_communication_log }}</h3>
	<table class="x_table x_table-striped">
		<thead>
			<tr>
				<th>{{ $lang->zpay_regdate }}</th>
				<th>{{ $lang->zpay_log_action }}</th>
				<th>{{ $lang->zpay_log_result }}</th>
				<th>{{ $lang->zpay_amount }}</th>
				<th>{{ $lang->zpay_log_response }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($order_logs as $log)
			<tr>
				<td>{{ zdate($log->regdate, 'Y-m-d H:i:s') }}</td>
				<td>{{ $log->action }}</td>
				<td>
					@if($log->result === 'F')
					<span class="x_text-error">F</span>
					@else
					S
					@endif
				</td>
				<td>{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($log->amount, $order->currency ?? 'KRW') }}</td>
				<td><code>{{ mb_substr($log->response_data, 0, 300) }}</code></td>
			</tr>
			@endforeach

			@if(!count($order_logs))
			<tr>
				<td colspan="5" class="x_text-center">{{ $lang->zpay_no_logs }}</td>
			</tr>
			@endif
		</tbody>
	</table>

	<p>
		<a class="x_btn" href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrders') }}">{{ $lang->cmd_list }}</a>
	</p>
</section>
