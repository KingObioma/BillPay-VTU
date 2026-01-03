@extends($theme.'layouts.user')
@section('page_title',__('Add Fund'))

@section('content')
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h4>@lang('Select method')</h4>
				</div>
				<div class="card-body">
					<form class="paybill-confirm-form" action=""
						  method="post">
						@csrf
						<input type="hidden" name="amount" value="">
						<div class="payment-section">
							<ul class="payment-container-list">
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
												<span class="text-danger">{{getAmount($gateway->fixed_charge,2)}} + {{getAmount($gateway->percentage_charge,2)}}% {{config('basic.base_currency')}} in total charges.</span>
												<br>
												<span>@lang('Minimum deposit limit- ') {{getAmount($gateway->min_amount,2)}} {{config('basic.base_currency')}}  @lang('Maximum deposit limit- '){{getAmount($gateway->max_amount,2)}} {{config('basic.base_currency')}} </span>
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
					<h5>@lang('How much would you like to add to your wallet?')</h5>
				</div>
				<div class="card-body">
					<div class="">
						<label>@lang('Amount')</label>
						<div class="input-group">
							<input type="number" step="0.01" class="form-control amountType">
							<span class="input-group-text" id="basic-addon2">{{config('basic.base_currency')}}</span>
						</div>
					</div>
					<div class="paybill-form-btn-area">
						<button type="button" class="cmn-btn w-100 submitBtn" disabled>@lang('pay')</button>
					</div>
				</div>
			</div>
			<div class="card mt-2">
				<div class="card-header d-flex justify-content-between">
					<h4>@lang('Preview Fund')</h4>
				</div>
				<div class="card-body">
					<ul class="paybill-confirm-list">
						<li class="item">
							<h6>@lang('Amount')</h6>
							<span id="amount">-</span>
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
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		'use strict';
		var amount;
		var gatewayId;
		var baseCurrency = "{{config('basic.base_currency')}}";
		$(document).on("click", ".submitBtn", function () {
			$(".paybill-confirm-form").submit();
		});

		$(document).on("keyup", ".amountType", function () {
			amount = $(this).val();
			$("input[name='amount']").val(amount);
			if (amount && gatewayId) {
				fetchCharge();
			}
		});

		$(document).on("change", ".gateway", function () {
			gatewayId = $(this).val();
			if (amount && gatewayId) {
				fetchCharge();
			}
		});

		function fetchCharge() {
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('gatewayChargeFetch') }}",
				data: {gatewayId: gatewayId, billInBase: amount},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						$('.submitBtn').attr('disabled', false);
						$('#amount').text(`${amount} ${baseCurrency}`)
						$('#minMax').text(`${data.min_amount} - ${data.max_amount} ${baseCurrency}`)
						$('#gatewayCharge').text(`${data.fixed_charge} + ${data.percentage_charge}%`)
						$('#payableAmount').text(`${data.totalPay} ${baseCurrency}`)
					}
					if (data.status == 'error') {
						Notiflix.Notify.Failure(data.msg);
					}
				}
			});
		}

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
