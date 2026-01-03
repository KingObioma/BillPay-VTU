@extends('admin.layouts.master')
@section('page_title')
	@lang($page_title)
@endsection
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Payment Pending')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Payment Pending')</div>
				</div>
			</div>
			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow-sm">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
								</div>
								<div class="card-body">
									<form action="{{ route('admin.payment.search') }}" method="get">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<input type="text" name="name" value="{{@request()->name}}"
														   class="form-control"
														   placeholder="@lang('Type Here')">
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													<select name="status" class="form-control">
														<option value="-1"
																@if(@request()->status == '-1') selected @endif>@lang('All Payment')</option>
														<option value="1"
																@if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
														<option value="2"
																@if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
														<option value="3"
																@if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
													</select>
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													<input type="date" class="form-control" name="date_time"
														   id="datepicker"/>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<button type="submit"
															class="btn btn-primary btn-sm btn-block"> @lang('Search') </button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Payment  List')</h6>
								</div>
								@if(count($funds)>0)
									<div class="card-body">
										<div class="table-responsive">
											<table
												class="categories-show-table table table-hover table-striped table-bordered">
												<thead class="thead-dark">
												<tr>
													<th scope="col">@lang('Date')</th>
													<th scope="col">@lang('Trx Number')</th>
													<th scope="col">@lang('User')</th>
													<th scope="col">@lang('Method')</th>
													<th scope="col">@lang('Amount')</th>
													<th scope="col">@lang('Charge')</th>
													<th scope="col">@lang('Payable')</th>
													<th scope="col">@lang('Status')</th>
													<th scope="col">@lang('Action')</th>
												</tr>
												</thead>
												<tbody>
												@foreach($funds as $key => $fund)
													<tr>
														<td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
														<td data-label="@lang('Trx Number')"
															class="font-weight-bold">{{ $fund->utr }}</td>
														<td data-label="@lang('User')">
															<a href="{{route('user-profile', $fund->user_id)}}"
															   class="text-decoration-none">
																<div class="d-lg-flex d-block align-items-center ">
																	<div class="mr-3"><img
																			src="{{getFile(optional($fund->receiver->profile)->driver,optional($fund->receiver->profile)->profile_picture) }}"
																			alt="user" class="rounded-circle" width="35"
																			data-toggle="tooltip" title=""
																			data-original-title="{{optional($fund->receiver)->name}}">
																	</div>
																	<div
																		class="d-inline-flex d-lg-block align-items-center">
																		<p class="text-dark mb-0 font-16 font-weight-medium">
																			{{optional($fund->receiver)->name}}</p>
																		<span
																			class="text-muted font-14 ml-1">{{optional($fund->receiver)->username}}</span>
																	</div>
																</div>
															</a>
														</td>
														<td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
														<td data-label="@lang('Amount')"
															class="font-weight-bold">{{ getAmount($fund->amount,config('basic.fraction_number')) }} {{ config('basic.base_currency') }}</td>
														<td data-label="@lang('Charge')"
															class="text-success">{{ getAmount($fund->charge,config('basic.fraction_number'))}} {{ config('basic.base_currency') }}</td>
														<td data-label="@lang('Payable')"
															class="font-weight-bold">{{ getAmount($fund->payable_amount,config('basic.fraction_number')) }} {{$fund->payment_method_currency}}</td>


														<td data-label="@lang('Status')">
															@if($fund->status == 2)
																<span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
															@elseif($fund->status == 1)
																<span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Approved')</span>
															@elseif($fund->status == 3)
																<span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Rejected')</span>
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

															@if($fund->payment_method_id  > 999)
																<button
																	class="edit_button   btn  {{($fund->status == 2) ?  'btn-outline-primary ' : 'btn-outline-success '}}   btn-sm btn-round"
																	data-toggle="modal" data-target="#myModal"
																	data-title="{{($fund->status == 2) ?  trans('Edit') : trans('Details')}}"

																	data-id="{{ $fund->id }}"
																	data-feedback="{{ $fund->feedback }}"
																	data-info="{{json_encode($details)}}"
																	data-amount="{{ getAmount($fund->amount)}} {{ $basic->currency }}"
																	data-username="{{ optional($fund->user)->username }}"
																	data-route="{{route('admin.payment.action',$fund->id)}}"
																	data-status="{{$fund->status}}">

																	@if(($fund->status == 2))
																		<i class="fa fa-pencil-alt"></i>
																	@else
																		<i class="fa fa-eye"></i>
																	@endif

																</button>
															@else
																-
															@endif
														</td>
													</tr>
												@endforeach
												</tbody>
											</table>
											{{ $funds->appends($_GET)->links('partials.pagination') }}
										</div>
									</div>
								@else
									@include('empty')
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

	<!-- Modal for Edit button -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-colored-header bg-primary">
					<h4 class="modal-title text-white" id="myModalLabel">@lang('Deposit Information')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>

				<form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
					@csrf
					@method('put')
					<div class="modal-body">
						<ul class="list-group withdraw-detail">
						</ul>

						<div class="get-feedback">

						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
						@if(Request::routeIs('admin.payment.pending'))
							<input type="hidden" class="action_id" name="id">
							<button type="submit" class="btn btn-primary" name="status"
									value="1">@lang('Approve')</button>
							<button type="submit" class="btn btn-danger" name="status"
									value="3">@lang('Reject')</button>
						@endif
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		"use strict";
		$(document).ready(function () {
			$(document).on("click", '.edit_button', function (e) {
				var id = $(this).data('id');
				var feedback = $(this).data('feedback');

				$(".action_id").val(id);
				$(".actionRoute").attr('action', $(this).data('route'));
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

				if (feedback == '') {
					var $res = `<div class="form-group"><br>
                                <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
                                <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
                            </div>`
				} else {
					var $res = `<h5>{{trans('Feedback')}}</h5>
                    <p>${feedback}</p>`
				}

				$('.get-feedback').html($res)
			});
		});
	</script>
@endsection
