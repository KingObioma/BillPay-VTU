@extends('admin.layouts.master')
@section('page_title',__('Bill Method List'))
@push('extra_styles')

@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Bill Method List')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Bill Method List')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-start">
						@foreach($billMethods as $key => $value)
							<div class="col-lg-4 mt-4">
								<div class="bill-method-item shadow bg-white rounded-3 p-3">
									<div class="d-flex justify-content-between">
										<div class="d-flex">
											<img class="rounded-circle bill-method-img"
												 src="{{getFile($value->driver,$value->logo)}}"
												 alt="">
											<div class="mx-3">
												<h5>{{ $value->methodName }}</h5>
												@if($value->is_active)
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-success font-12"></i> @lang('Active')</span>
												@else
													<span
														class="badge badge-light"><i
															class="fa fa-circle text-warning font-12"></i> @lang('DeActive')</span>
												@endif
											</div>
										</div>
										<div class="dropdown">
											<button class="btn btn-primary dropdown-toggle" type="button"
													id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
													aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right"
												 aria-labelledby="dropdownMenuButton">
												<a href="{{ route('admin.bill.method.edit',$value->id) }}"
												   class="dropdown-item">@lang('Edit')</a>
												@if($value->code == 'flutterwave')
													<a href="javascript:void(0)" data-toggle="modal"
													   data-target="#selectCurrency" data-name="{{$value->methodName}}"
													   data-code="{{$value->code}}"
													   class="dropdown-item">@lang('Balance')</a>
												@else
													<a href="javascript:void(0)" data-toggle="modal"
													   data-target="#showAmount" data-name="{{$value->methodName}}"
													   data-code="{{$value->code}}"
													   class="dropdown-item showAmount">@lang('Balance')</a>
												@endif
												@if($value->is_active ==  0)
													<a href="javascript:void(0)" data-target="#makeActive"
													   data-toggle="modal"
													   data-name="{{$value->methodName}}"
													   data-route="{{route('admin.bill.method.activated',$value->id)}}"
													   class="dropdown-item makeActive">@lang('Make Active')</a>
												@endif
											</div>
										</div>
									</div>
									<div class="mt-3">
										<p>{{ __($value->description) }}</p>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</section>
	</div>

	<div id="showAmount" class="modal fade" tabindex="-1" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title titleShow" id="exampleModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body mt-2">
					<div class="bouncing-loader">
						<div></div>
						<div></div>
						<div></div>
					</div>
					<p class="text-center font-style font-weight-bold text-dark" id="nbr"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
				</div>
			</div>
		</div>
	</div>

	<div id="selectCurrency" class="modal fade" tabindex="-1" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title titleShow" id="exampleModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body mt-2">
					<label>@lang('Select Currency')</label>
					<select class="form-control currencySelect" name="selectCurrency">
						<option value="AOA">AOA</option>
						<option value="AUD">AUD</option>
						<option value="BRL">BRL</option>
						<option value="BRL">CAD</option>
						<option value="EGP">EGP</option>
						<option value="ETB">ETB</option>
						<option value="EUR">EUR</option>
						<option value="GBP">GBP</option>
						<option value="GHS">GHS</option>
						<option value="ILS">ILS</option>
						<option value="INR">INR</option>
						<option value="JPY">JPY</option>
						<option value="KES">KES</option>
						<option value="MAD">MAD</option>
						<option value="MUR">MUR</option>
						<option value="MWK">MWK</option>
						<option value="MZN">MZN</option>
						<option value="NGN">NGN</option>
						<option value="RWF">RWF</option>
						<option value="SLL">SLL</option>
						<option value="TZS">TZS</option>
						<option value="UGX">UGX</option>
						<option value="USD">USD</option>
						<option value="XAF">XAF</option>
						<option value="XOF">XOF</option>
						<option value="ZAR">ZAR</option>
						<option value="ZMW">ZMW</option>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
					<button type="button" class="btn btn-primary showAmount" data-name="Flutterwave"
							data-code="flutterwave" data-toggle="modal"
							data-target="#showAmount">@lang('Confirm')</button>
				</div>
			</div>
		</div>
	</div>

	<div id="makeActive" class="modal fade" tabindex="-1" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-dark font-weight-bold"
						id="primary-header-modalLabel">@lang('Activated Confirmation')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<form action="" method="post" class="activeForm">
					@csrf
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<h5 id="msg"></h5>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary">@lang('Yes')</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('extra_scripts')

@endpush
@section('scripts')
	<script>
		'use strict'

		$(document).on("click", ".makeActive", function () {
			let name = $(this).data('name');
			$('#msg').text(`Are you want to activated ${name} ?`)
			$('.activeForm').attr('action', $(this).data('route'))
		});

		$(document).on("click", ".showAmount", function () {
			$("#nbr").text('');
			$('.bouncing-loader').removeClass('d-none');
			let name = $(this).data('name');
			let code = $(this).data('code');
			$('.titleShow').text(`${name} Balance:`);
			let currencyCode = $('.currencySelect').find(':selected').val();
			getBalance(code, currencyCode);
		});

		function getBalance(code, currencyCode = null) {
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('admin.fetch.balance') }}",
				data: {code: code, currencyCode: currencyCode},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						$('.bouncing-loader').addClass('d-none');
						$("#nbr").text(`${data.balance} ${data.currencyCode}`)
					}
				}
			});
		}

	</script>
@endsection

