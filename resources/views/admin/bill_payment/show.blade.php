@extends('admin.layouts.master')
@section('page_title', __('Bill Details'))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Bill Details')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Bill Details')</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					@if($billDetails->last_api_error)
						<div class="card">
							<div class="card-body shadow">
								<div class="media align-items-center d-flex justify-content-between text-danger">
									<div>
										<i class="fas fa-exclamation-triangle"></i> @lang('Last Api error message:-') {{$billDetails->last_api_error}}
									</div>
								</div>
							</div>
						</div>
					@endif

					<div class="card">
						<div class="card-body shadow">
							<div class="d-flex justify-content-between align-items-center">
								<h4 class="card-title">@lang("Bill Details")</h4>

								<div>
									<a href="{{route('bill.pay.list')}}"
									   class="btn btn-sm  btn-outline-primary mr-2">
										<span><i class="fas fa-arrow-left"></i> @lang('Back')</span>
									</a>
									@if($billDetails->status == 2)
										<button
											data-target="#confirmModal" data-toggle="modal"
											data-route="{{route('bill.pay.confirm',$billDetails->utr)}}"
											class="btn btn-outline-success btn-sm confirmButton"><i
												class="fas fa-check"></i> @lang('Pay Bill')</button>
										<button
											data-toggle="modal"
											data-target="#confirmModal"
											data-route="{{route('bill.pay.return',$billDetails->utr)}}"
											class="btn btn-outline-danger btn-sm returnButton"><i
												class="fas fa-undo"></i> @lang('Return Bill')</button>
									@endif
								</div>
							</div>
							<hr>
							<div class="p-4 card-body shadow">
								<div class="row">
									<div class="col-md-6 border-right">
										<ul class="list-style-none">
											<li class="my-2 border-bottom pb-3">
                                            <span class="font-weight-medium text-dark"><i
													class="fas fa-exchange-alt mr-2 text-primary"></i> @lang("Transaction"): <small
													class="float-right">{{$billDetails->created_at}} </small></span>
											</li>

											<li class="my-3 d-flex align-items-center">
												<span><i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Sender :')</span>

												<a class="ml-3 text-decoration-none"
												   href="{{route('user.edit',$billDetails->user_id)}}">
													<div class="d-lg-flex d-block align-items-center">
														<div class="mr-1"><img
																src="{{$billDetails->user->profilePicture() }}"
																alt="user" class="rounded-circle" width="45"
																height="45"></div>
														<div class="">
															<h5 class="text-dark mb-0 font-16 font-weight-medium">@lang(optional($billDetails->user)->username)</h5>
															<p class="text-muted mb-0 font-12 font-weight-medium">@lang(optional($billDetails->user)->email)</p>
														</div>
													</div>
												</a>
											</li>

											<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle mr-2 text-primary"></i> @lang("Bill method") : <span
													class="font-weight-medium text-dark">{{ __(optional($billDetails->method)->methodName) }}</span></span>
											</li>

											<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle mr-2 text-primary"></i> @lang("Payment Gateway") : <span
													class="font-weight-medium text-dark">{{$billDetails->payment_method_id == -1 ? 'Wallet' : optional($billDetails->gateway)->name}}</span></span>
											</li>

											<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle mr-2 text-primary"></i> @lang('Transaction Id') : <span
													class="font-weight-medium text-dark">{{ __($billDetails->utr) }}</span></span>
											</li>

											<li class="my-3">
                                            <span><i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Status') :
                                                @if($billDetails->status == 2)
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
												@elseif($billDetails->status == 3)
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-success font-12"></i> @lang('Completed')</span>
												@elseif($billDetails->status == 4)
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-danger font-12"></i> @lang('Return')</span>
												@elseif($billDetails->status == 5)
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-info font-12"></i> @lang('Processing')</span>
												@endif
                                            </span>
											</li>


											<li class="my-3">
                                            <span><i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Exchange Rate') : <span
													class="font-weight-bold text-dark">1 {{config('basic.base_currency')}} <i
														class="fas fa-exchange-alt"></i> {{getAmount($billDetails->exchange_rate,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
											</li>

											<li class="my-3">
                                            <span><i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Pay In Base') : <span
													class="font-weight-bold text-danger">{{getAmount($billDetails->pay_amount_in_base,config('basic.fraction_number'))}} {{config('basic.base_currency')}}
                                                </span>
                                            </span>
											</li>
										</ul>
									</div>


									<div class="col-md-6 ">
										<ul class="list-style-none">
											<li class="my-2 border-bottom pb-3">
                                            <span class="font-weight-medium text-dark"><i
													class="fas fa-lightbulb mr-2 text-primary"></i> @lang('Bill Information')</span>
											</li>

											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Category') : <span
													class="font-weight-bold text-danger">{{ucfirst(str_replace("_"," ",$billDetails->category_name))}}
                                                </span>
                                            </span>
											</li>
											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Type') : <span
													class="font-weight-bold">{{$billDetails->type}}
                                                </span>
                                            </span>
											</li>
											@if($billDetails->customer)
												@foreach($billDetails->customer as $key => $info)
													<li class="my-3">
												<span>
													<i class="fas fa-check-circle mr-2 text-primary"></i> {{snake2Title($key)}} : <span
														class="font-weight-bold">{{$info->$key}}
													</span>
												</span>
													</li>
												@endforeach
											@endif
											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Country Code') : <span
													class="font-weight-bold">{{$billDetails->country_name}}
                                                </span>
                                            </span>
											</li>
											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Amount') : <span
													class="font-weight-bold">{{getAmount($billDetails->amount,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
											</li>
											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Charge') : <span
													class="font-weight-bold text-danger">{{getAmount($billDetails->charge,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
											</li>
											<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle mr-2 text-primary"></i> @lang('Payable Amount') : <span
													class="font-weight-bold text-dark">{{getAmount($billDetails->payable_amount,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>


	<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header text-white bg-primary">
					<h5 class="modal-title" id="exampleModalLabel"><i
							class="fas fa-info-circle"></i> @lang('Confirmation !')</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="" method="post" id="confirmForm">
					@csrf
					<div class="modal-body text-center">
						<p>@lang('Are you sure you want to confirm this action?')</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark"
								data-dismiss="modal">@lang('Close')</button>
						<input type="submit" class="btn btn-primary" value="@lang('Confirm')">
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		'use strict'
		$(document).on('click', '.confirmButton,.returnButton', function () {
			let submitUrl = $(this).data('route');
			$('#confirmForm').attr('action', submitUrl)
		})
	</script>
@endsection
