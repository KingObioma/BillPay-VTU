@extends('admin.layouts.master')
@section('page_title',__('Deposit List'))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
	<!-- Container Fluid-->
	<div class="container-fluid" id="container-wrapper">

		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">@lang('Deposit List')</h1>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="./">@lang('Home')</a></li>
				<li class="breadcrumb-item active" aria-current="page">@lang('Deposit List')</li>
			</ol>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<!-- Filter -->
				<div class="card mb-4">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
					</div>
					<div class="card-body">
						@if(isset($userId))
							<form action="{{ route('admin.user.deposit.search',$userId) }}" method="get">
								@include('admin.deposit.searchForm')
							</form>
						@else
							<form action="{{ route('admin.deposit.search') }}" method="get">
								@include('admin.deposit.searchForm')
							</form>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<!-- Transfer list -->
				<div class="card mb-4">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">@lang('Deposit List')</h6>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-striped table-hover align-items-center table-borderless">
								<thead class="thead-light">
								<tr>
									<th>@lang('Sender')</th>
									<th>@lang('Receiver')</th>
									<th>@lang('Receiver E-Mail')</th>
									<th>@lang('Currency')</th>
									<th>@lang('Transaction ID')</th>
									<th>@lang('Requested Amount')</th>
									<th>@lang('Status')</th>
									<th>@lang('Created time')</th>
								</tr>
								</thead>
								<tbody>
								@foreach($deposits as $key => $value)
									<tr>
										<td data-label="@lang('Sender')">{{ optional($value->sender)->name ?? __('N/A') }}</td>
										<td data-label="@lang('Receiver')">{{ optional($value->receiver)->name ?? __('N/A') }}</td>
										<td data-label="@lang('Receiver E-Mail')">{{ $value->email }}</td>
										<td data-label="@lang('Currency')">{{ optional($value->currency)->name ?? __('N/A') }}</td>
										<td data-label="@lang('Transaction ID')">{{ $value->utr }}</td>
										<td data-label="@lang('Requested Amount')">{{ number_format($value->amount,'2','.',',').' '.optional($value->currency)->code }}</td>
										<td data-label="@lang('Status')">
											@if($value->status)
												<span class="badge badge-success">@lang('Success')</span>
											@else
												<span class="badge badge-warning">@lang('Pending')</span>
											@endif
										</td>
										<td data-label="@lang('Created time')">{{ date('Y-m-d h:i a',strtotime($value->created_at)) }}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="card-footer">
							{{ $deposits->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!---Container Fluid-->
@endsection
@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/bootstrap-datepicker.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict';
		$(document).ready(function(){
			$('#created_at').datepicker({
			format: 'yyyy-mm-dd',
			todayBtn: 'linked',
			todayHighlight: true,
			autoclose: true,
			orientation: "bottom auto",
		});
		});
	</script>
@endsection
