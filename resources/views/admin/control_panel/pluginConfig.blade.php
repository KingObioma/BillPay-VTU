@extends('admin.layouts.master')
@section('page_title', __('Plugin Configuration'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Plugin Configuration')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('settings') }}">@lang('Settings')</a>
					</div>
					<div class="breadcrumb-item">@lang('Plugin Configuration')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="row justify-content-md-center">
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Plugin Configuration')</h6>
										</div>
										<div class="card-body py-5">
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-2"><img
																		src="{{ asset('assets/upload/tawk.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Message your customers,they\'ll love you for it')</div>
																<div class="col-md-4"><a
																		href="{{ route('tawk.control') }}"
																		class="btn btn-sm btn-primary"
																		target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-2"><img
																		src="{{ asset('assets/upload/messenger.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Message your customers,they\'ll love you for it')</div>
																<div class="col-md-4"><a
																		href="{{ route('fb.messenger.control') }}"
																		class="btn btn-sm btn-primary"
																		target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col-md-2"><img
																		src="{{ asset('assets/upload/reCaptcha.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('reCAPTCHA protects your website from fraud and abuse.')</div>
																<div class="col-md-4">
																	<div class="d-flex flex-wrap align-items-start ">
																		<a href="{{ route('google.recaptcha.control') }}"
																		   class="btn btn-sm btn-primary mr-2 "
																		   target="_blank">@lang('Configuration')</a>
																		<div class="selectgroup  ">
																			<label class="selectgroup-item form-switch-google">
																				<input type="radio" name="google_reCaptcha_status"
																					   value="0" id="selectgroup-input-off"
																					   class="selectgroup-input " {{ old('google_reCaptcha_status', basicControl()->google_reCaptcha_status) == 0 ? 'checked' : ''}}>
																				<span class="selectgroup-button selectgroup-button-sm">@lang('OFF')</span>
																			</label>
																			<label class="selectgroup-item form-switch-google">
																				<input type="radio" name="google_reCaptcha_status" value="1" id="selectgroup-input-on"
																					   class="selectgroup-input " {{ old('google_reCaptcha_status', basicControl()->google_reCaptcha_status) == 1 ? 'checked' : ''}}>
																				<span class="selectgroup-button selectgroup-button-sm">@lang('ON')</span>
																			</label>
																		</div>
																		@error('google_reCaptcha_status')
																		<span class="text-danger" role="alert">
																				<strong>{{ __($message) }}</strong>
																			</span>
																		@enderror
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col-md-2"><img
																		src="{{ asset('assets/upload/manual_recaptcha.svg') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Manual reCAPTCHA protects your website from fraud and abuse.')</div>
																<div class="col-md-4">
																	<div class="d-flex flex-wrap align-items-start ">
																		<a href="{{ route('manual.recaptcha.control') }}"
																		   class="btn btn-sm btn-primary mr-2 "
																		   target="_blank">@lang('Configuration')</a>
																		<div class="selectgroup  ">
																			<label class="selectgroup-item form-switch-google">
																				<input type="radio" name="manual_reCaptcha_status"
																					   value="0" id="selectgroup-manual-off"
																					   class="selectgroup-input" {{ old('manual_reCaptcha_status', basicControl()->manual_reCaptcha_status) == 0 ? 'checked' : ''}}>

																				<span class="selectgroup-button selectgroup-button-sm">@lang('OFF')</span>
																			</label>
																			<label class="selectgroup-item form-switch-google">
																				<input type="radio"
																					   name="manual_reCaptcha_status"
																					   value="1" id="selectgroup-manual-on"
																					   class="selectgroup-input" {{ old('manual_reCaptcha_status', basicControl()->manual_reCaptcha_status) == 1 ? 'checked' : ''}}>
																				<span class="selectgroup-button selectgroup-button-sm">@lang('ON')</span>
																			</label>
																		</div>
																		@error('manual_reCaptcha_status')
																		<span class="text-danger" role="alert">
																				<strong>{{ __($message) }}</strong>
																			</span>
																		@enderror
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-2">
																	<img src="{{ asset('assets/upload/analytics.png') }}"
																		 class="w-25"></div>
																<div class="col-md-6">@lang('Google Analytics is a web analytics service offered by Google.')</div>
																<div class="col-md-4">
																	<a href="{{ route('google.analytics.control') }}" class="btn btn-sm btn-primary" target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection

@push('extra_styles')
	<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
		  rel="stylesheet">
@endpush
@push('extra_scripts')
	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
@endpush

@push("extra_scripts")
	<script>
		"use strict";
		$(document).ready(function() {
			console.log("lol")
			$('#selectgroup-input-off').on('click',function() {
				$.ajax({
					url: "{{ route('active.recaptcha') }}",
					type: "GET",
					data: {
						status: 0,
					},
					success: function(response) {
					},
					error: function(error) {
					}
				});
			});

			$('#selectgroup-input-on').on('click',function() {
				$.ajax({
					url: "{{ route('active.recaptcha') }}",
					type: "GET",
					data: {
						status: 1,
					},
					success: function(response) {
					},
					error: function(error) {
					}
				});
			});

			$('#selectgroup-manual-off').on('click',function() {
				$.ajax({
					url: "{{ route('active.manual.recaptch') }}",
					type: "GET",
					data: {
						status: 0,
					},
					success: function(response) {
					},
					error: function(error) {
					}
				});
			});
			$('#selectgroup-manual-on').on('click',function() {
				$.ajax({
					url: "{{ route('active.manual.recaptch') }}",
					type: "GET",
					data: {
						status: 1,
					},
					success: function(response) {
					},
					error: function(error) {
					}
				});
			});

		});


	</script>
@endpush

