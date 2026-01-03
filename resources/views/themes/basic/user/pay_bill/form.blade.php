@extends($theme.'layouts.user')
@section('page_title',__('Pay Bill'))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-between">
			<h4>@lang('Pay Bill')</h4>
		</div>
		<div class="card-body">
			<form class="paybill-form" action="{{route('pay.bill.submit')}}" method="post">
				@csrf
				<div class="row g-4">
					<div class="col-md-6">
						<label for="From-Country" class="form-label">@lang('Country')</label>
						<select class="form-select country" id="From-Country" name="country" required>
							<option value="">@lang('Select Country')</option>
							@foreach($countries->sortBy('asc') as $key => $country)
								<option value="{{$key}}"
										data-currency="{{$country['currency']}}"> {{$country['name']}}</option>
							@endforeach
						</select>
						@error('country')
						<span class="text-danger">{{$message}}</span>
						@enderror
					</div>
					<div class="col-md-6 mb-2">
						<label for="dynamic-services" class="form-label">@lang('Services')</label>
						<select class="form-select" id="dynamic-services" name="service" required>

						</select>
						@error('service')
						<span class="text-danger">{{$message}}</span>
						@enderror
					</div>
					<div class="bouncing-loader d-none">
						<div></div>
						<div></div>
						<div></div>
					</div>
					<div class="row dynamicForm">

					</div>
					<div class="showAmount">

					</div>
				</div>
				<div class="paybill-form-btn-area">
					<button type="submit"
							class="cmn-btn w-100">@lang('continue')</button>
				</div>
			</form>
		</div>
	</div>
@endsection
@section('scripts')
	<script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>
	<script>
		'use strict';
		$(document).ready(function () {
			$('.country').select2();
			$('#dynamic-services').select2();
		});
		var category = "{{$category}}";
		var currency = "";
		var phoneCode = "";
		var countryList = @json(config('country'));

		$(document).on('change', '.country', function () {
			$('.dynamicForm').html('');
			$('.showAmount').html('');
			var code = $(this).find(':selected').val();
			for (let i = 0; i < countryList.length; i++) {
				if (countryList[i].code == code) {
					phoneCode = countryList[i].phone_code;
				}
			}
			fetchService(category, code);
		})

		function fetchService(category, code) {
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('fetch.services') }}",
				data: {category: category, code: code},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						let services = data.data;
						showServices(services);
					}
				}
			});
		}

		function showServices(services) {
			$('#dynamic-services').html('');
			var options = `<option disabled value="" selected>@lang("Select Service")</option>`;
			for (let i = 0; i < services.length; i++) {
				options += `<option value="${services[i].id}" data-percent="${services[i].percent_charge}"
                data-fixed="${services[i].fixed_charge}" data-min="${services[i].min_amount}" data-max="${services[i].max_amount}"
                data-currency="${services[i].currency}" data-amount="${services[i].amount}" data-label="${services[i].label_name}">${services[i].type}</option>`;
			}
			$('#dynamic-services').html(options);
		}

		$(document).on('change', '#dynamic-services', function () {
			$('.bouncing-loader').removeClass('d-none');
			let label = $(this).find(':selected').data("label");
			currency = $('.country').find(':selected').data('currency');
			const inputString = label;
			const delimiter = ",";
			const stringToArray = inputString.split(delimiter);

			let amount = $(this).find(':selected').data("amount");

			if (amount == -1) {
				let serviceId = $(this).find(':selected').val();
				getMultiAmount(serviceId);
			} else {
				let min = $(this).find(':selected').data("min");
				let max = $(this).find(':selected').data("max");

				$('.showAmount').html('');
				let amountShow = `<div class="col-md-6">
					<label for="Amount" class="form-label">@lang('Amount')
				${max > 0 ? `<sup> <i class="fas fa-info-circle"></i>
							You can pay min ${min} ${currency} and max ${max} ${currency}</sup>` : ''}
					</label>
					<div class="input-group">
						<input class="form-control" type="text" id="Amount" name="amount"
							   placeholder="@lang('Enter Amount')" required>
						<span class="input-group-text" id="basic-addon2">${currency}</span>
					</div>
					@error('amount')
				<span class="text-danger">{{$message}}</span>
					@enderror
				</div>`;

				$('.showAmount').html(amountShow);

				$('#Amount').val("")
				$('#Amount').attr('readonly', false);

				if (parseInt(amount) > 0) {
					$('#Amount').val(amount)
					$('#Amount').attr('readonly', true);
				}
			}
			dynamicInput(stringToArray);
		});

		function getMultiAmount(serviceId) {
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('fetch.amount') }}",
				data: {serviceId: serviceId},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						let resAmounts = data.data;
						showAmount(resAmounts);
					}
				}
			});
		}

		function showAmount(resAmounts) {
			$('#dynamic-amounts').html('');
			$('.showAmount').html('');
			var amountShow = `<div class="col-md-6 mb-2">
						<label for="dynamic-amounts" class="form-label">@lang('Amount')</label>
						<div class="input-group">
							<select class="form-select" id="dynamic-amounts" name="amount" required>

							</select>
							<span class="input-group-text" id="basic-addon2">${currency}</span>
						</div>
						@error('amount')
			<span class="text-danger">{{$message}}</span>
						@enderror
			</div>`
			$('.showAmount').html(amountShow);

			var options = `<option disabled value="" selected>@lang("Select Amount")</option>`;
			for (let i = 0; i < resAmounts.length; i++) {
				options += `<option value="${resAmounts[i].id}">${resAmounts[i].description}</option>`;
			}
			$('#dynamic-amounts').html(options);
		}

		function dynamicInput(stringToArray) {
			$('.dynamicForm').html('');
			let form = '';
			for (let i = 0; i < stringToArray.length; i++) {
				const formattedString = formatString(stringToArray[i]);
				form += `
				    <div class="col-md-6 mt-1">
					<label for="phone" class="form-label">${formattedString}</label>
					<div class="input-group">
					${stringToArray[i] == 'Recipient_Phone' ?
					`<span class="input-group-text" id="basic-addon2">${phoneCode}</span>` : ''}
					<input id="telephone" type="text" class="form-control" name="${stringToArray[i]}" placeholder="${formattedString}" required>
					</div>
					</div>
					@error('customer')
				<span class="text-danger">{{$message}}</span>
					@enderror
				`;
			}
			$('.bouncing-loader').addClass('d-none');
			$('.dynamicForm').append(form);
		}

		function formatString(input) {
			const stringWithSpaces = input.replace(/_/g, ' ');
			const formattedString = stringWithSpaces.charAt(0).toUpperCase() + stringWithSpaces.slice(1);
			return formattedString;
		}

		$(document).on('keyup', '#Amount', function () {
			this.value = this.value.replace(/[^0-9.]/g, '');
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
