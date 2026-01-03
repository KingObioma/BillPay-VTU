@extends('admin.layouts.master')
@section('page_title',__('Bill Service List'))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="{{ asset('assets/dashboard/css/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Bill Service List')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Bill Service List')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Bill Service List') <sup
											class="ml-1 text-dark font-weight-bold"> {{$activeMethod->methodName}}</sup>
									</h6>
									<button type="button" class="btn btn-outline-primary bulkAdd"
											data-target="#limit_charge"
											data-route="{{route('admin.bill.chargeLimit.add')}}"
											data-toggle="modal"><i
											class="fas fa-plus-circle"></i> @lang('Add Bulk Limit and Charge')</button>
								</div>
								@if(count($services)>0)
									<div class="card-body">
										<div class="table-responsive">
											<table
												class="table table-striped table-hover align-items-center table-flush table-sm"
												id="chargeTable">
												<thead class="thead-light">
												<tr>
													<th>@lang('SL.')</th>
													<th>@lang('Type')</th>
													<th>@lang('Code')</th>
													<th>@lang('Category')</th>
													<th>@lang('Country')</th>
													<th>@lang('Charges')</th>
													<th>@lang('Limit')</th>
													<th>@lang('Action')</th>
												</tr>
												</thead>
												<tbody>
												@foreach($services as $key => $value)
													<tr>
														<td data-label="@lang('SL.')">{{++$key}}</td>
														<td data-label="@lang('Type')">{{ __($value->type) }}</td>
														<td data-label="@lang('Code')">{{ __($value->code) }}</td>
														<td data-label="@lang('Name')">{{str_replace('_',' ',ucfirst($value->service)) }}</td>
														<td data-label="@lang('Country')">{{ __(getCountryName($value->country)) }}</td>
														<td data-label="@lang('Charges')"><span class="text-danger">{{$value->percent_charge}}%
															+ {{$value->fixed_charge}} {{$value->currency}}</span></td>
														<td data-label="@lang('Limit')"><span
																class="text-dark font-weight-bold">{{$value->min_amount}}
															- {{$value->max_amount}} {{$value->currency}}</span></td>
														<td data-label="@lang('Action')">
															<button data-target="#limit_charge_edit"
																	data-toggle="modal"
																	data-resource="{{json_encode($value)}}"
																	data-re="{{json_encode($countryList)}}"
																	data-route="{{route('admin.bill.chargeLimit.edit',$value->id)}}"
																	class="btn btn-sm btn-round btn-outline-primary editCharge">
																<i
																	class="fas fa-edit"></i>
															</button>
															@if($value->status == 0)
																<button data-target="#status_change"
																		data-toggle="modal"
																		title="@lang('Enable')"
																		data-route="{{route('admin.bill.status.change',$value->id)}}"
																		class="btn btn-sm btn-round btn-outline-success enableStatus">
																	<i
																		class="fas fa-check"></i>
																</button>
															@else
																<button data-target="#status_change"
																		data-toggle="modal"
																		title="@lang('Disable')"
																		data-route="{{route('admin.bill.status.change',$value->id)}}"
																		class="btn btn-sm btn-round btn-outline-danger disableStatus">
																	<i
																		class="fas fa-times"></i>
																</button>
															@endif
														</td>
													</tr>
												@endforeach
												</tbody>
											</table>
										</div>
{{--										<div class="card-footer">--}}
{{--											{{ $services->links() }}--}}
{{--										</div>--}}
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

	{{--	 Status Change--}}
	<div id="status_change" class="modal fade" tabindex="-1" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-dark font-weight-bold"
						id="primary-header-modalLabel">@lang('Status Change Confirmation')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<form action="" method="post" class="statusRoute">
					@csrf
					<div class="modal-body">
						<div id="tag-body">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary">@lang('Submit')</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{--	charge limit bulk add--}}
	<div id="limit_charge" class="modal fade" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-dark font-weight-bold"
						id="primary-header-modalLabel">@lang('Add Bulk Limit and Charges')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<form action="" method="post" class="formRoute">
					@csrf
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<label>@lang('Select Service')</label>
								<select class="form-control selectService" name="service" required>
									<option selected="" disabled="" value="">@lang('Select Service')</option>
									@if($categories)
										@foreach($categories as $category)
											<option
												value="{{$category->service}}">{{str_replace('_',' ',ucfirst($category->service))}}</option>
										@endforeach
									@endif
								</select>
								@error('service')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-12">
								<label>@lang('Select Country')</label>
								<select class="form-control changeCountry" name="country"
										data-re="{{json_encode($countryList)}}" required>
									<option selected="" disabled="" value="">@lang('Select Country')</option>
									@if($countries)
										@foreach($countries as $country)
											<option value="{{$country->country}}">{{$country->countryName}}</option>
										@endforeach
									@endif
								</select>
								@error('country')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-6">
								<label>@lang('Percent Charge')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="percent_charge" class="form-control"
										   required>
									<div class="input-group-append">
										<span class="form-control">%</span>
									</div>
								</div>
								@error('percent_charge')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<div class="col-md-6">
								<label>@lang('Fixed Charge')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="fixed_charge" class="form-control" required>
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('fixed_charge')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-6">
								<label>@lang('Minimum Amount')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="min_amount" class="form-control">
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('min_amount')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<div class="col-md-6">
								<label>@lang('Maximum Amount')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="max_amount" class="form-control">
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('max_amount')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<input type="hidden" name="currency" class="hdCurrency" value="">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary">@lang('Add')</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{--	charge limit edit --}}
	<div id="limit_charge_edit" class="modal fade" tabindex="-1" role="dialog"
		 aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-dark font-weight-bold"
						id="primary-header-modalLabel">@lang('Edit Limit and Charges')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<form action="" method="post" class="formRouteEdit">
					@csrf
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<label>@lang('Select Service')</label>
								<select class="form-control showService" name="service">
								</select>
								@error('service')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-12">
								<label>@lang('Select Country')</label>
								<select class="form-control showCountry" name="country">
								</select>
								@error('country')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-6">
								<label>@lang('Percent Charge')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="percent_charge" class="form-control percent"
										   required>
									<div class="input-group-append">
										<span class="form-control">%</span>
									</div>
								</div>
								@error('percent_charge')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<div class="col-md-6">
								<label>@lang('Fixed Charge')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="fixed_charge" class="form-control fix"
										   required>
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('fixed_charge')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-md-6">
								<label>@lang('Minimum Amount')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="min_amount" class="form-control min">
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('min_amount')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<div class="col-md-6">
								<label>@lang('Maximum Amount')</label>
								<div class="input-group">
									<input type="number" step="0.001" name="max_amount" class="form-control max">
									<div class="input-group-append">
										<span class="form-control showCurrency"></span>
									</div>
								</div>
								@error('max_amount')
								<span class="text-danger">{{$message}}</span>
								@enderror
							</div>
							<input type="hidden" name="currency" class="hdCurrency" value="">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary">@lang('Update')</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
	<script>
		'use strict'
		$(document).ready(function () {
			$('#chargeTable').DataTable();
			$('.changeCountry').select2({
				width: "100%"
			});
			$('.selectService').select2({
				width: "100%"
			});
		});
		$(document).on('click', '.enableStatus', function () {
			$('#tag-body').html('');
			var route = $(this).data('route');
			$('.statusRoute').attr('action', route)
			$('#tag-body').append(`<p>Are you sure enable the service<p>`)
		});

		$(document).on('click', '.disableStatus', function () {
			$('#tag-body').html('');
			var route = $(this).data('route');
			$('.statusRoute').attr('action', route)
			$('#tag-body').append(`<p>Are you sure disable the service<p>`)
		});

		$(document).on('click', '.editCharge', function () {
			var route = $(this).data('route');
			var resource = $(this).data('resource');
			$('.formRouteEdit').attr('action', route)
			$('.showCountry').html('')
			$('.showService').html('')
			$('.percent').val(resource.percent_charge)
			$('.fix').val(resource.fixed_charge)
			$('.min').val(resource.min_amount)
			$('.max').val(resource.max_amount)
			$('.showCurrency').text(resource.currency)
			let replaceService = resource.service.replace('_', ' ')
			$('.showCountry').append(`<option value="${resource.country}">${resource.countryName}</option>`);
			$('.showService').append(`<option value="${resource.service}">${replaceService}</option>`);
			var country = $(this).data('re');
			currencCode(country, resource.country);
		});

		$(document).on('click', '.bulkAdd', function () {
			var route = $(this).data('route');
			$('.formRoute').attr('action', route)
		});

		$(document).on('change', '.changeCountry', function () {
			var country = $(this).data('re');
			var code = $(this).find(':selected').val()

			currencCode(country, code);
		})

		function currencCode(country, code) {
			Object.keys(country).forEach(key => {
				let singleCode = country[key].code;
				if (singleCode == code) {
					$('.showCurrency').text(country[key].iso_code)
					$('.hdCurrency').val(country[key].iso_code)
					return;
				}
			});
		}

	</script>
@endpush

