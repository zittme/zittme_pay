@include('_tabs')

<section class="section">
	<h2>{{ $lang->zpay_tab_logs }}</h2>
	<p class="x_help-block">{{ $lang->about_zpay_logs }}</p>

	<form action="./" method="get" class="x_form-inline">
		<input type="hidden" name="module" value="admin" />
		<input type="hidden" name="act" value="dispZittme_payAdminLogs" />

		<select name="log_action">
			<option value="">{{ $lang->zpay_filter_all_action }}</option>
			<option value="ready" @if($filter_action === 'ready') selected="selected" @endif>ready</option>
			<option value="approve" @if($filter_action === 'approve') selected="selected" @endif>approve</option>
			<option value="cancel" @if($filter_action === 'cancel') selected="selected" @endif>cancel</option>
			<option value="webhook" @if($filter_action === 'webhook') selected="selected" @endif>webhook</option>
			<option value="query" @if($filter_action === 'query') selected="selected" @endif>query</option>
			<option value="confirm" @if($filter_action === 'confirm') selected="selected" @endif>confirm</option>
			<option value="refund" @if($filter_action === 'refund') selected="selected" @endif>refund</option>
			<option value="expire" @if($filter_action === 'expire') selected="selected" @endif>expire</option>
		</select>

		<select name="log_result">
			<option value="">{{ $lang->zpay_filter_all_result }}</option>
			<option value="S" @if($filter_result === 'S') selected="selected" @endif>{{ $lang->zpay_result_success }}</option>
			<option value="F" @if($filter_result === 'F') selected="selected" @endif>{{ $lang->zpay_result_fail }}</option>
		</select>

		<input type="text" name="order_code" value="{{ $filter_order_code }}" placeholder="{{ $lang->zpay_order_code }}" />
		<button type="submit" class="x_btn">{{ $lang->cmd_search }}</button>
	</form>

	<table class="x_table x_table-striped">
		<caption>{{ sprintf($lang->zpay_total_logs, number_format($total_count)) }}</caption>
		<thead>
			<tr>
				<th>{{ $lang->zpay_regdate }}</th>
				<th>{{ $lang->zpay_order_code }}</th>
				<th>{{ $lang->zpay_gateway }}</th>
				<th>{{ $lang->zpay_log_action }}</th>
				<th>{{ $lang->zpay_log_result }}</th>
				<th>{{ $lang->zpay_amount }}</th>
				<th>{{ $lang->zpay_log_response }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($log_list as $log)
			<tr>
				<td>{{ zdate($log->regdate, 'Y-m-d H:i:s') }}</td>
				<td>
					@if($log->order_srl)
					<a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrderView', 'order_srl', $log->order_srl) }}">{{ $log->order_code }}</a>
					@else
					{{ $log->order_code }}
					@endif
				</td>
				<td>{{ $log->gateway }}</td>
				<td>{{ $log->action }}</td>
				<td>
					@if($log->result === 'F')
					<span class="x_text-error">{{ $lang->zpay_result_fail }}</span>
					@else
					{{ $lang->zpay_result_success }}
					@endif
				</td>
				<td>{{ number_format($log->amount) }}</td>
				<td><span title="{{ mb_substr($log->response_data, 0, 500) }}">{{ \Zittme\Modules\Zittme_pay\Models\Log::summarize((string)$log->response_data) }}</span></td>
			</tr>
			@endforeach

			@if(!count($log_list))
			<tr>
				<td colspan="7" class="x_text-center">{{ $lang->zpay_no_logs }}</td>
			</tr>
			@endif
		</tbody>
	</table>

	@if($total_page > 1)
	<div class="x_page-navigation">
		@foreach($page_navigation as $page_no)
		@if($page_no == $page)
		<strong class="x_active">{{ $page_no }}</strong>
		@else
		<a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminLogs', 'page', $page_no, 'log_action', $filter_action, 'log_result', $filter_result, 'order_code', $filter_order_code) }}">{{ $page_no }}</a>
		@endif
		@endforeach
	</div>
	@endif

	<form action="./" method="post" class="x_pull-right">
		<input type="hidden" name="module" value="zittme_pay" />
		<input type="hidden" name="act" value="procZittme_payAdminPurgeLogs" />
		<button type="submit" class="x_btn">{{ sprintf($lang->zpay_purge_logs, $pay_config->log_retention_days) }}</button>
	</form>
</section>
