@extends($theme.'layouts.user')
@section('page_title',__('Preview Payment'))

@section('content')
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h4>@lang('Select payment method')</h4>
				</div>
				<div class="card-body">
					<form class="paybill-confirm-form" action="{{route('pay.bill.previewConfirm',$billPay->utr)}}"
						  method="post">
						@csrf
						<div class="payment-section">
							<ul class="payment-container-list">
								<li class="item">
									<input class="form-check-input wallet" type="radio" name="gatewayId"
										   value="-1"
										   id="flexRadioDefault-1">
									<label class="form-check-label" for="flexRadioDefault-1">
										<div class="image-area">
											<img src="{{asset($themeTrue.'images/wallet.jpg')}}"
												 alt="@lang('Wallet')">
										</div>
										<div class="content-area">
											<h5>@lang('Wallet')</h5>
										</div>
									</label>
								</li>
								@foreach($gateways as $key => $gateway)
									<li class="item">
										<input class="form-check-input gateway" type="radio" name="gatewayId"
											   value="{{$gateway->id}}"
											   id="flexRadioDefault{{$key}}">
										<label class="form-check-label" for="flexRadioDefault{{$key}}">
											<div class="image-area">
												<img src="{{getFile($gateway->driver,$gateway->image)}}"
													 alt="{{$gateway->name}}">
											</div>
											<div class="content-area">
												<h5>{{$gateway->name}}</h5>
											</div>
										</label>
									</li>
								@endforeach
							</ul>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4 mt-2">
			<div class="card">
				<div class="card-header d-flex justify-content-between">
					<h4>@lang('Preview Payment')</h4>
				</div>
				<div class="card-body">
					<ul class="paybill-confirm-list">
						<li class="item">
							<h6>@lang('Category')</h6>
							<span>{{str_replace('_',' ',ucfirst($billPay->category_name))}}</span>
						</li>
						<li class="item">
							<h6>@lang('Service')</h6><span>{{optional($billPay->service)->type}}</span>
						</li>
						<li class="item">
							<h6>@lang('Country Code')</h6><span>{{$billPay->country_name}}</span>
						</li>
						<li class="item">
							<h6>@lang('Amount')</h6>
							<span>{{getAmount($billPay->amount,config('basic.fraction_number'))}} {{$billPay->currency}}</span>
						</li>
						<li class="item text-danger">
							<h6 class="text-danger">@lang('Charge')</h6>
							<span>{{getAmount($billPay->charge,config('basic.fraction_number'))}} {{$billPay->currency}}</span>
						</li>
						<li class="item">
							<h6>@lang('Exchange Rate')</h6>
							<span>1 {{config('basic.base_currency')}} = {{getAmount($billPay->exchange_rate,config('basic.fraction_number'))}} {{$billPay->currency}}</span>
						</li>
						<li class="item">
							<h6 class="">@lang('Min - Max Amount')</h6>
							<span id="minMax">-</span>
						</li>
						<li class="item text-danger">
							<h6 class="text-danger">@lang('Gateway Charge')</h6>
							<span id="gatewayCharge">-</span>
						</li>
						<li class="item">
							<h6>@lang('Payable Amount')</h6>
							<span id="payableAmount">-</span>
						</li>

					</ul>
					<div class="paybill-form-btn-area">
						<button type="button" class="cmn-btn w-100 submitBtn" disabled>@lang('pay')</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		'use strict';
		var userBalance = "{{getAmount(auth()->user()->balance,2)}}"
		var billInBase = "{{getAmount($billPay->pay_amount_in_base,2)}}";
		var baseCurrency = "{{config('basic.base_currency')}}"
		$(document).on("click", ".submitBtn", function () {
			$(".paybill-confirm-form").submit();
		});

		$(document).on("change", ".gateway", function () {
			var gatewayId = $(this).val();
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('gatewayChargeFetch') }}",
				data: {gatewayId: gatewayId, billInBase: billInBase},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						$('.submitBtn').attr('disabled', false);
						$('#minMax').text(`${data.min_amount} - ${data.max_amount} ${baseCurrency}`)
						$('#gatewayCharge').text(`${data.fixed_charge} + ${data.percentage_charge}%`)
						$('#payableAmount').text(`${data.totalPay} ${baseCurrency}`)
					}
					if (data.status == 'error') {
						Notiflix.Notify.Failure(data.msg);
					}
				}
			});
		});

		$(document).on("click", ".wallet", function () {
			$('.submitBtn').attr('disabled', false);
			$('#payableAmount').text(`${billInBase} ${baseCurrency}`)
			$('#minMax').text('-')
			$('#gatewayCharge').text('-')
			if (userBalance < billInBase) {
				$('.submitBtn').attr('disabled', true);
				Notiflix.Notify.Failure(`Insufficient wallet balance available balance ${userBalance} ${baseCurrency}`);
			}
		});
	</script>

	@if ($errors->any())
		@php
			$collection = collect($errors->all());
			$errors = $collection->unique();
		@endphp
		<script>
			"use strict";
			@foreach ($errors as $error)
			Notiflix.Notify.Failure("{{ trans($error) }}");
			@endforeach
		</script>
	@endif
@endsection
