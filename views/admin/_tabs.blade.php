<div class="x_page-header">
	<h1>{{ $lang->zittme_pay }}</h1>
</div>

<ul class="x_nav x_nav-tabs">
	<li @if($pay_tab === 'config') class="x_active" @endif><a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminConfig') }}">{{ $lang->zpay_tab_config }}</a></li>
	<li @if($pay_tab === 'gateway') class="x_active" @endif><a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminGateway') }}">{{ $lang->zpay_tab_gateway }}</a></li>
	<li @if($pay_tab === 'orders') class="x_active" @endif><a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminOrders') }}">{{ $lang->zpay_tab_orders }}</a></li>
	<li @if($pay_tab === 'logs') class="x_active" @endif><a href="{{ getUrl('', 'module', 'admin', 'act', 'dispZittme_payAdminLogs') }}">{{ $lang->zpay_tab_logs }}</a></li>
</ul>
