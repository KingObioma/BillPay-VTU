@extends($theme.'layouts.user')
@section('page_title',__('Fund Request'))

@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-between border-0">
			<h4>@lang('Fund Request')</h4>
		</div>
		@if(count($deposits) > 0)
			<div class="card-body">
				<div class="cmn-table">
					<div class="table-responsive overflow-hidden">
						<table class="table align-middle">
							<thead>
							<tr>
								<th scope="col">@lang('Date')</th>
								<th scope="col">@lang('Trx Number')</th>
								<th scope="col">@lang('Method')</th>
								<th scope="col">@lang('Amount')</th>
								<th scope="col">@lang('Charge')</th>
								<th scope="col">@lang('Payable')</th>
								<th scope="col">@lang('Status')</th>
								<th scope="col">@lang('Action')</th>
							</tr>
							</thead>
							<tbody>
							@foreach($deposits as $key => $fund)
								<tr>
									<td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
									<td data-label="@lang('Trx Number')"
										class="font-weight-bold">{{ $fund->utr }}</td>
									<td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
									<td data-label="@lang('Amount')"
										class="font-weight-bold">{{ getAmount($fund->amount,config('basic.fraction_number')) }} {{ config('basic.base_currency') }}</td>
									<td data-label="@lang('Charge')"
										class="text-danger">{{ getAmount($fund->charge,config('basic.fraction_number'))}} {{ config('basic.base_currency') }}</td>
									<td data-label="@lang('Payable')"
										class="font-weight-bold">{{ getAmount($fund->payable_amount,config('basic.fraction_number')) }} {{$fund->payment_method_currency}}</td>


									<td data-label="@lang('Status')">
										@if($fund->status == 2)
											<span
												class="badge text-bg-warning"> @lang('Pending')</span>
										@elseif($fund->status == 1)
											<span
												class="badge text-bg-success"> @lang('Approved')</span>
										@elseif($fund->status == 3)
											<span
												class="badge text-bg-danger"> @lang('Rejected')</span>
										@endif
									</td>
									<td data-label="@lang('Action')">
										@php
											if($fund->detail){
													$details =[];
													  foreach($fund->detail as $k => $v){
															if($v->type == "file"){
																$details[kebab2Title($k)] = [
																	'type' => $v->type,
																	'field_name' => getFile($v->field_name->driver,$v->field_name->path)
																	];
															}else{
																$details[kebab2Title($k)] =[
																	'type' => $v->type,
																	'field_name' => $v->field_name
																];
															}
													  }
												}else{
													$details = null;
												}
										@endphp
										<a href="javascript:void(0)" data-bs-target="#viewModal"
										   data-bs-toggle="modal"
										   data-feedback="{{ $fund->feedback }}"
										   data-info="{{json_encode($details)}}"
										   class="action-btn view"><i class="fa-regular fa-eye"></i></a>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@else
			@include($theme.'user.empty')
		@endif
	</div>
	{{ $deposits->appends($_GET)->links($theme.'partials.user.pagination') }}

	<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="describeModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="describeModalLabel"> @lang('Admin Feedback')</h4>
					<button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
						<i class="fal fa-times"></i>
					</button>
				</div>
				<div class="modal-body">
					<ul class="list-group withdraw-detail">
					</ul>

					<div class="get-feedback">

					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="cmn-btn3"
							data-bs-dismiss="modal">@lang('Close')</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		"use strict";
		$(document).on("click", ".view", function () {
			var feedback = $(this).data('feedback');
			var details = Object.entries($(this).data('info'));
			var list = [];
			details.map(function (item, i) {
				if (item[1].type == 'file') {
					var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
				} else {
					var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
				}
				list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
			});
			$('.withdraw-detail').html(list);

			var $res = `<h5>{{trans('Feedback')}}</h5>
                    <p>${feedback}</p>`

			$('.get-feedback').html($res)
		});
	</script>
@endsection
