@extends('admin.layouts.master')
@section('page_title', __('Edit Method'))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="{{ asset('assets/store/css/bootstrap-select.min.css') }}">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Edit Method')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item"><a
							href="{{ route('admin.bill.method.list') }}">@lang('Available methods')</a></div>
					<div class="breadcrumb-item">@lang('Edit Method')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-8">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Edit Method')</h6>
									<div class="d-flex flex-row align-items-center justify-content-end">
										@if(config('billservices.'.$billMethod->code))
											<a href="javascript:void(0)" data-target="#importService"
											   data-toggle="modal"
											   class="btn btn-sm btn-outline-primary mr-2">
												<i class="fas fa-arrow-down"></i> @lang('Import Services')</a>
										@endif
										<a href="{{ route('admin.bill.method.list') }}"
										   class="btn btn-sm btn-outline-primary">
											<i class="fas fa-arrow-left"></i> @lang('Back')</a>
									</div>
								</div>
								<div class="card-body">
									<form action="{{ route('admin.bill.method.save',$billMethod->id) }}" method="post"
										  enctype="multipart/form-data">
										@csrf
										@method('post')
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="methodName">@lang('Method Name')</label>
													<input type="text" name="methodName"
														   value="{{ old('methodName',$billMethod->methodName) }}"
														   placeholder="@lang('Payment method name')"
														   class="form-control @error('methodName') is-invalid @enderror">
													<div class="invalid-feedback">
														@error('methodName') @lang($message) @enderror
													</div>
												</div>
											</div>
										</div>
										@if($billMethod->parameters)
											<div class="row">
												@foreach ($billMethod->parameters as $key => $parameter)
													<div class="col-md-6">
														<div class="form-group">
															<label
																for="{{ $key }}">{{ __(strtoupper(str_replace('_',' ', $key))) }}</label>
															<input type="text" name="{{ $key }}"
																   value="{{ old($key, $parameter) }}"
																   id="{{ $key }}"
																   class="form-control @error($key) is-invalid @enderror">
															<div class="invalid-feedback">
																@error($key) @lang($message) @enderror
															</div>
														</div>
													</div>
												@endforeach
											</div>
										@endif
										@if($billMethod->extra_parameters)
											<div class="row">
												@foreach($billMethod->extra_parameters as $key => $param)
													<div class="col-md-6">
														<div class="form-group">
															<label
																for="{{ $key }}">{{ __(strtoupper(str_replace('_',' ', $key))) }}</label>
															<div class="input-group">
																<input type="text" name="{{ $key }}"
																	   class="form-control @error($key) is-invalid @enderror"
																	   value="{{ old($key, route($param, $billMethod->code )) }}"
																	   disabled>
																<div class="input-group-append">
																	<button type="button"
																			class="btn btn-info copy-btn btn-sm">
																		<i class="fas fa-copy"></i>
																	</button>
																</div>
															</div>
															<div class="invalid-feedback">
																@error($key) @lang($message) @enderror
															</div>
														</div>
													</div>
												@endforeach
											</div>
										@endif
										<div class="row align-items-center">
											<div class="col-md-6">
												<div class="form-group mb-4">
													<label class="col-form-label">@lang('Choose logo')</label>
													<div id="image-preview" class="image-preview"
														 style="background-image: url({{ getFile($billMethod->driver,$billMethod->logo) }});">
														<label for="image-upload"
															   id="image-label">@lang('Choose File')</label>
														<input type="file" name="logo"
															   class="@error('logo') is-invalid @enderror"
															   id="image-upload"/>
													</div>
													<div class="invalid-feedback">
														@error('logo') @lang($message) @enderror
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="description">@lang('Description')</label>
													<textarea
														class="form-control @error('description') is-invalid @enderror"
														name="description"
														rows="5">{{ old('description', $billMethod->description) }}</textarea>
													<div
														class="invalid-feedback">@error('description') @lang($message)@enderror
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<button type="submit"
														class="btn btn-primary btn-sm btn-block">@lang('Save Changes')</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Conversion Rate')</h6>
								</div>
								<div class="card-body">
									<form action="{{ route('admin.bill.service.rate',$billMethod->id) }}" method="post">
										@csrf
										@method('put')
										<div class="convertion-rate-list">
											<div class="row">
												@if($isoCodes)
													@foreach($isoCodes as $key => $value)
														<div class="col-md-12">
															<div class="form-group">
																<div class="input-group">
																	<div class="input-group-append">
															<span
																class="form-control">@lang('1 '){{config('basic.base_currency')}}</span>
																	</div>
																	<input type="text" name="convert_rate[{{$key}}]"
																		   @if($billMethod->convert_rate)
																			   @foreach($billMethod->convert_rate as $key1 => $rate)
																				   @php
																					   if($key == $key1){
																						$rate = $rate;
																						break;
																					   }else{
																						   $rate = 1;
																					   }
																				   @endphp
																			   @endforeach
																		   @endif
																		   value="{{$rate??1}}"
																		   class="form-control @error('convert_rate') is-invalid @enderror">
																	<div class="input-group-prepend">
																		<span class="form-control">{{$key}}</span>
																	</div>
																</div>
																<div class="invalid-feedback">
																	@error('convert_rate') @lang($message) @enderror
																</div>
															</div>
														</div>
													@endforeach
												@endif
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<button type="submit"
														class="btn btn-primary btn-sm btn-block">@lang('Save Changes')</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	@if(config('billservices.'.$billMethod->code))
		<div id="importService" class="modal fade" tabindex="-1" role="dialog"
			 aria-labelledby="primary-header-modalLabel"
			 aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-dark font-weight-bold"
							id="primary-header-modalLabel">@lang('Confirm Import Service')</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<form action="{{route('admin.bill.fetch.service',$billMethod->id)}}" method="get">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<lable class="font-weight-bold">@lang('Select Category:')</lable>
									<select class="form-control" id="api-service" name="api_service" required>
										<option selected="" disabled="">@lang('Select Service')</option>
										@foreach(config('billservices.'.$billMethod->code) as $key => $service)
											<option value="{{$key}}">{{$service['name']}}</option>
										@endforeach
									</select>
									@error('api_service')
									<span class="text-danger">{{$message}}</span>
									@enderror
								</div>
							</div>
							@if($billMethod->code == 'bloc')
								<div class="row mt-3">
									<div class="col-md-12">
										<lable class="font-weight-bold">@lang('Select Operators:')</lable>
										<select class="form-control" id="dynamic-operators" name="operator_id" required>

										</select>
										@error('operator')
										<span class="text-danger">{{$message}}</span>
										@enderror
									</div>
								</div>
							@endif
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
							<button type="submit" class="btn btn-primary">@lang('Yes')</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@endif
@endsection
@push('extra_scripts')
	<script src="{{ asset('assets/store/js/bootstrap-select.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict'
		var methodCode = "{{$billMethod->code}}"
		$(document).on('change', '#api-service', function () {
			let category = $(this).find(':selected').val();

			if (methodCode == 'bloc') {
				fetchOperators(category, methodCode);
			}
		})

		function fetchOperators(category) {
			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('admin.bill.fetch.operators') }}",
				data: {category: category, methodCode: methodCode},
				dataType: 'json',
				type: "post",
				success: function (data) {
					if (data.status == 'success') {
						let services = data.data.data;
						showOperators(services);
					}
				}
			});
		}

		function showOperators(services) {
			$('#dynamic-operators').html('');
			var options = `<option disabled value="" selected>@lang("Select Operators")</option>`;
			for (let i = 0; i < services.length; i++) {
				options += `<option value="${services[i].id}">${services[i].desc}</option>`;
			}
			$('#dynamic-operators').html(options);
		}


		$(document).ready(function () {
			$.uploadPreview({
				input_field: "#image-upload",
				preview_box: "#image-preview",
				label_field: "#image-label",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
		});

		$(document).on('click', '.copy-btn', function () {
			let _this = $(this)[0];
			let copyText = $(this).parents('.input-group-append').siblings('input');
			$(copyText).prop('disabled', false);
			copyText.select();
			document.execCommand("copy");
			$(copyText).prop('disabled', true);
			$(this).text('Coppied');
			setTimeout(function () {
				$(_this).text('');
				$(_this).html('<i class="fas fa-copy"></i>');
			}, 500);
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
