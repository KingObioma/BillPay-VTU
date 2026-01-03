@extends($theme.'layouts.user')
@section('page_title',__('Bill Details'))

@section('content')
	<div class="section dashboard">
		<div class="row">
			<div class="account-settings-profile-section">
				<div class="card">
					<div class="card-body shadow">
						<div class="d-flex justify-content-between align-items-center">
							<h4 class="card-title">@lang("Bill Details")</h4>

							<div>
								<a href="{{route('pay.bill.list')}}"
								   class="cmn-btn3 me-2">
									<span><i class="fas fa-arrow-left"></i> @lang('Back')</span>
								</a>
							</div>
						</div>
						<hr>
						<div class="p-4 card-body shadow">
							<div class="row">
								<div class="col-md-6 border-end">
									<ul class="list-style-none ms-4">
										<li class="my-2 border-bottom pb-3">
                                            <span class="font-weight-medium "><i
													class="fas fa-exchange-alt me-2 text-base"></i> @lang("Transaction"): <small
													class="float-end">{{$billDetails->created_at}} </small></span>
										</li>

										<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle me-2 text-base"></i> @lang("Bill method") : <span
													class="font-weight-medium">{{ __(optional($billDetails->method)->methodName) }}</span></span>
										</li>

										<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle me-2 text-base"></i> @lang("Payment Gateway") : <span
													class="font-weight-medium">{{$billDetails->payment_method_id == -1 ? 'Wallet' : optional($billDetails->gateway)->name}}</span></span>
										</li>

										<li class="my-3">
                                            <span class="font-weight-bold"><i
													class="fas fa-check-circle me-2 text-base"></i> @lang('Transaction Id') : <span
													class="font-weight-medium">{{ __($billDetails->utr) }}</span></span>
										</li>

										<li class="my-3">
                                            <span><i class="fas fa-check-circle me-2 text-base"></i> @lang('Status') :
                                                @if($billDetails->status == 2)
													<p class="badge text-bg-warning">@lang('Pending')</p>
												@elseif($billDetails->status == 3)
													<p
														class="badge text-bg-success">@lang('Completed')</p>
												@elseif($billDetails->status == 4)
													<p class="badge text-bg-danger">@lang('Return')</p>
												@elseif($billDetails->status == 5)
													<p class="badge text-bg-info">@lang('Processing')</p>
												@endif
                                            </span>
										</li>


										<li class="my-3">
                                            <span><i class="fas fa-check-circle me-2 text-base"></i> @lang('Exchange Rate') : <span
													class="font-weight-bold ">1 {{config('basic.base_currency')}} <i
														class="fas fa-exchange-alt"></i> {{getAmount($billDetails->exchange_rate,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
										</li>

										<li class="my-3">
                                            <span><i class="fas fa-check-circle me-2 text-base"></i> @lang('Pay In Base') : <span
													class="font-weight-bold text-danger">{{getAmount($billDetails->pay_amount_in_base,config('basic.fraction_number'))}} {{config('basic.base_currency')}}
                                                </span>
                                            </span>
										</li>
									</ul>
								</div>


								<div class="col-md-6 ">
									<ul class="list-style-none ms-4">
										<li class="my-2 border-bottom pb-3">
                                            <span class="font-weight-medium "><i
													class="fas fa-lightbulb me-2 text-base"></i> @lang('Bill Information')</span>
										</li>

										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Category') : <span
													class="font-weight-bold text-danger">{{ucfirst(str_replace("_"," ",$billDetails->category_name))}}
                                                </span>
                                            </span>
										</li>
										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Type') : <span
													class="font-weight-bold">{{$billDetails->type}}
                                                </span>
                                            </span>
										</li>
										@if($billDetails->customer)
											@foreach($billDetails->customer as $key => $info)
												<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> {{snake2Title($key)}} : <span
													class="font-weight-bold">{{$info->$key}}
                                                </span>
                                            </span>
												</li>
											@endforeach
										@endif
										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Country Code') : <span
													class="font-weight-bold">{{$billDetails->country_name}}
                                                </span>
                                            </span>
										</li>
										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Amount') : <span
													class="font-weight-bold">{{getAmount($billDetails->amount,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
										</li>
										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Charge') : <span
													class="font-weight-bold text-danger">{{getAmount($billDetails->charge,config('basic.fraction_number'))}} {{$billDetails->currency}}
                                                </span>
                                            </span>
										</li>
										<li class="my-3">
                                            <span>
												<i class="fas fa-check-circle me-2 text-base"></i> @lang('Payable Amount') : <span
													class="font-weight-bold ">{{getAmount($billDetails->payable_amount,config('basic.fraction_number'))}} {{$billDetails->currency}}
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
	</div>
@endsection
