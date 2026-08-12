@include('_tabs')

<section class="section">
	<h2>{{ $lang->zpay_tab_orders }}</h2>

	{{-- 송금 대기가 남아 있으면 눈에 띄게 알린다. 놓치면 고객 돈이 안 돌아간다. --}}
	@if($pending_refund_count > 0)
	<p class="x_alert x_alert-error">
		<a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrders', 'refund_state', 'pending') }}">
			{{ sprintf($lang->zpay_pending_refund_notice, number_format($pending_refund_count)) }}
		</a>
	</p>
	@endif

	<form action="./" method="get" class="x_form-inline">
		<input type="hidden" name="refund_state" value="{{ $filter_refund_state }}" />
		<input type="hidden" name="module" value="admin" />
		<input type="hidden" name="act" value="dispZittme_payAdminOrders" />

		<select name="status">
			<option value="">{{ $lang->zpay_filter_all_status }}</option>
			@foreach($status_labels as $status_key => $status_label)
			<option value="{{ $status_key }}" @if($filter_status === $status_key) selected="selected" @endif>{{ $status_label }}</option>
			@endforeach
		</select>

		<select name="search_target">
			<option value="order_code" @if($search_target === 'order_code') selected="selected" @endif>{{ $lang->zpay_order_code }}</option>
			<option value="payer_name" @if($search_target === 'payer_name') selected="selected" @endif>{{ $lang->zpay_payer }}</option>
			<option value="payer_phone" @if($search_target === 'payer_phone') selected="selected" @endif>{{ $lang->zpay_payer_phone }}</option>
			<option value="payer_email" @if($search_target === 'payer_email') selected="selected" @endif>{{ $lang->zpay_payer_email }}</option>
			<option value="pg_tid" @if($search_target === 'pg_tid') selected="selected" @endif>{{ $lang->zpay_pg_tid }}</option>
			<option value="title" @if($search_target === 'title') selected="selected" @endif>{{ $lang->zpay_product }}</option>
		</select>

		<input type="text" name="search_keyword" value="{{ $search_keyword }}" />
		<button type="submit" class="x_btn">{{ $lang->cmd_search }}</button>
	</form>

	<table class="x_table x_table-striped">
		<caption>{{ sprintf($lang->zpay_total_orders, number_format($total_count)) }}</caption>
		<thead>
			<tr>
				<th>{{ $lang->zpay_order_code }}</th>
				<th>{{ $lang->zpay_product }}</th>
				<th>{{ $lang->zpay_payer }}</th>
				<th>{{ $lang->zpay_amount }}</th>
				<th>{{ $lang->zpay_gateway }}</th>
				<th>{{ $lang->zpay_status }}</th>
				<th>{{ $lang->zpay_regdate }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($order_list as $item)
			<tr>
				<td>
					<a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrderView', 'order_srl', $item->order_srl) }}">{{ $item->order_code }}</a>
				</td>
				<td>{{ $item->title }}</td>
				<td>{{ $item->payer_name }}</td>
				<td>
					{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($item->amount, $item->currency ?? 'KRW') }}
					@if($item->cancelled_amount > 0)
					<span class="x_text-error">(-{{ \Zittme\Modules\Zittme_pay\Models\Currency::money($item->cancelled_amount, $item->currency ?? 'KRW') }})</span>
					@endif
				</td>
				<td>{{ $item->gateway }}</td>
				<td>
					{{ $status_labels[$item->status] ?? $item->status }}
					{{-- 무통장 입금대기 건은 목록에서 바로 입금확인 처리할 수 있게 한다 --}}
					@if(in_array($item->status, ['ready', 'pending'], true))
					<form action="./" method="post" style="display:inline;margin-left:6px" onsubmit="return confirm('{{ $lang->zpay_confirm_deposit_ask ?? '입금을 확인하셨습니까? 확인 처리하면 결제완료로 바뀝니다.' }}')">
						<input type="hidden" name="module" value="zittme_pay" />
						<input type="hidden" name="act" value="procZittme_payAdminConfirmDeposit" />
						<input type="hidden" name="order_srl" value="{{ $item->order_srl }}" />
						<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrders') }}" />
						<button type="submit" class="x_btn x_btn-small x_btn-primary">{{ $lang->zpay_confirm_deposit ?? '입금확인' }}</button>
					</form>
					@endif
				</td>
				<td>{{ zdate($item->regdate, 'Y-m-d H:i') }}</td>
			</tr>
			@endforeach

			@if(!count($order_list))
			<tr>
				<td colspan="7" class="x_text-center">{{ $lang->zpay_no_orders }}</td>
			</tr>
			@endif
		</tbody>
	</table>

	{{-- 페이지네이션은 코어가 HTML 을 만들어 주지 않으므로 직접 그린다 (pitfall #43). --}}
	@if($total_page > 1)
	<div class="x_page-navigation">
		@foreach($page_navigation as $page_no)
		@if($page_no == $page)
		<strong class="x_active">{{ $page_no }}</strong>
		@else
		<a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrders', 'page', $page_no, 'status', $filter_status, 'refund_state', $filter_refund_state, 'search_target', $search_target, 'search_keyword', $search_keyword) }}">{{ $page_no }}</a>
		@endif
		@endforeach
	</div>
	@endif
</section>
