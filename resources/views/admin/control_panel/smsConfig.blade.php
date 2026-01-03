@extends('admin.layouts.master')
@section('page_title', __('SMS Control'))

@push('extra_styles')
    <link href="{{ asset('assets/dashboard/css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('SMS Control')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('SMS Control')</div>
			</div>
		</div>

		<div class="section-body">
			<div class="row mt-sm-4">
				<div class="col-12 col-md-4 col-lg-3">
					@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.sms'), 'suffix' => ''])
				</div>
				<div class="col-12 col-md-8 col-lg-9">
					<div class="container-fluid" id="container-wrapper">
						<div class="row justify-content-md-center">
							<div class="col-lg-12">
								<div class="card mb-4 card-primary shadow">
									<div class="card-body">
										<div class="table-responsive">
											<table class="table table-hover align-items-center table-borderless">
												<thead class="thead-light">
												<tr>
													<th> @lang('SHORTCODE') </th>
													<th> @lang('DESCRIPTION') </th>
												</tr>
												</thead>
												<tbody>
												<tr>
													<td>
														<pre>@lang('[[receiver]]')</pre>
													</td>
													<td> @lang("Receiver's number will replace here.") </td>
												</tr>
												<tr>
													<td>
														<pre>@lang('[[message]]')</pre>
													</td>
													<td>@lang("Application notification message will replace here.")</td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						@php
							if (old()){
								$headerData = array_combine(old('headerDataKeys'),old('headerDataValues'));
								$paramData = array_combine(old('paramKeys'),old('paramValues'));
								$formData = array_combine(old('formDataKeys'),old('formDataValues'));
								$headerData = (empty(array_filter($headerData))) ? null : json_encode($headerData);
								$paramData = (empty(array_filter($paramData))) ? null : json_encode($paramData);
								$formData = (empty(array_filter($formData))) ? null : json_encode($formData);
							} else {
								$headerData = $smsControl->headerData;
								$paramData = $smsControl->paramData;
								$formData = $smsControl->formData;
							}
							$headerActive = false;
							$paramActive = false;
							$formActive = false;
							if ($errors->has('headerDataKeys.*') || $errors->has('headerDataValues.*')) {
								$headerActive = true;
							}elseif ($errors->has('paramKeys.*') || $errors->has('paramValues.*')) {
								$paramActive = true;
							} elseif ($errors->has('formDataKeys.*') || $errors->has('formDataValues.*')) {
								$formActive = true;
							} else {
								$headerActive = true;
							}
						@endphp
						<div class="row justify-content-md-center">
							<div class="col-lg-12">
								<div class="card mb-4 card-primary shadow">
									<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
										<h6 class="m-0 font-weight-bold text-primary">@lang('SMS Configuration')</h6>
									</div>
									<div class="card-body">
										<form action="{{ route('sms.config') }}" method="post">
											@csrf
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="actionMethod">@lang('Method')</label>
														<select name="actionMethod" id="actionMethod"
																class="form-control @error('actionMethod') is-invalid @enderror">
															<option value="GET" {{ (old('actionMethod',$smsControl->actionMethod) == 'GET') ? 'selected' : '' }}>
																GET
															</option>
															<option value="POST" {{ (old('actionMethod',$smsControl->actionMethod) == 'POST') ? 'selected' : '' }} >
																POST
															</option>
														</select>
														<div class="invalid-feedback">
															@error('actionMethod') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="form-group">
														<label for="actionUrl">@lang('URL')</label>
														<input type="text" name="actionUrl"
																value="{{ old('actionUrl',$smsControl->actionUrl) }}"
																placeholder="@lang('Enter request URL')"
																class="form-control @error('actionUrl') is-invalid @enderror">
														<div class="invalid-feedback">
															@error('actionUrl') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>@lang('SMS Notification')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="sms_notification" value="0"
																	   class="selectgroup-input" {{ old('sms_notification', $basicControl->sms_notification) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="sms_notification" value="1"
																	   class="selectgroup-input" {{ old('sms_notification', $basicControl->sms_notification) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('sms_notification')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
														@enderror
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>@lang('SMS Verification')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="sms_verification" value="0"
																	   class="selectgroup-input" {{ old('sms_verification', $basicControl->sms_verification) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="sms_verification" value="1"
																	   class="selectgroup-input" {{ old('sms_verification', $basicControl->sms_verification) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('sms_verification')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
														@enderror
													</div>
												</div>
											</div>

											<hr>

											<ul class="nav nav-tabs" id="myTab" role="tablist">
												<li class="nav-item">
													<a class="nav-link {{ ($headerActive) ? 'active' : '' }}" id="headerData-tab"
														data-toggle="tab" href="#headerData" role="tab" aria-controls="headerData"
														aria-selected="{{ ($headerActive) ? 'true' : 'false' }}">@lang('Headers')</a>
												</li>
												<li class="nav-item">
													<a class="nav-link {{ ($paramActive) ? 'active' : '' }}" id="params-tab"
														data-toggle="tab" href="#params" role="tab" aria-controls="params"
														aria-selected="{{ ($paramActive) ? 'true' : 'false' }}">@lang('Params')</a>
												</li>
												<li class="nav-item">
													<a class="nav-link {{ ($formActive) ? 'active' : '' }}" id="formData-tab"
														data-toggle="tab" href="#formData" role="tab" aria-controls="contact"
														aria-selected="{{ ($formActive) ? 'true' : 'false' }}">@lang('Form Data')</a>
												</li>
											</ul>
											<div class="tab-content" id="myTabContent">
												<div class="tab-pane fade {{ ($headerActive) ? 'show active' : '' }}" id="headerData"
														role="tabpanel" aria-labelledby="headerData-tab">
													<label for="headerData">@lang('Headers')</label>
													<div class="headerDataWrapper">
														@if(is_null($headerData))
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<input type="text" name="headerDataKeys[]" value=""
																				placeholder="@lang('Key')"
																				class="form-control headerDataKeys">
																	</div>
																</div>
																<div class="col-md-7">
																	<div class="form-group">
																		<input type="text" name="headerDataValues[]" value=""
																				placeholder="@lang('Value')"
																				class="form-control form-control-sm">
																	</div>
																</div>
																<div class="col-md-1">
																	<div class="form-group">
																		<a href="javascript:void(0);"
																			class="btn btn-primary btn-sm addHeaderData"><i
																					class="fas fa-plus"></i></a>
																	</div>
																</div>
															</div>
														@else
															@foreach(json_decode($headerData) as $key => $value)
																<div class="row">
																	<div class="col-md-4">
																		<div class="form-group">
																			<input type="text" name="headerDataKeys[]" value="{{$key}}"
																					placeholder="@lang('Key')" autocomplete="off"
																					class="form-control headerDataKeys @error('headerDataKeys.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error("headerDataKeys.".$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-7">
																		<div class="form-group">
																			<input type="text" name="headerDataValues[]"
																					value="{{$value}}" placeholder="@lang('Value')"
																					class="form-control @error('headerDataValues.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error("headerDataValues.".$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-1">
																		<div class="form-group">
																			@if($loop->first)
																				<a href="javascript:void(0);"
																					class="btn btn-primary btn-sm addHeaderData"><i
																							class="fas fa-plus"></i></a>
																			@else
																				<a href="javascript:void(0);"
																					class="btn btn-danger btn-sm removeDiv"><i
																							class="fas fa-minus"></i></a>
																			@endif
																		</div>
																	</div>
																</div>
															@endforeach
														@endif
													</div>
												</div>
												<div class="tab-pane fade {{ ($paramActive) ? 'show active' : '' }}" id="params"
														role="tabpanel" aria-labelledby="params-tab">
													<label for="params">@lang('Params')</label>
													<div class="paramsWrapper">
														@if(is_null($paramData))
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<input type="text" name="paramKeys[]" value=""
																				placeholder="@lang('Key')"
																				class="form-control form-control-sm">
																	</div>
																</div>
																<div class="col-md-7">
																	<div class="form-group">
																		<input type="text" name="paramValues[]" value=""
																				placeholder="@lang('Value')"
																				class="form-control form-control-sm">
																	</div>
																</div>
																<div class="col-md-1">
																	<div class="form-group">
																		<a href="javascript:void(0);"
																			class="btn btn-primary btn-sm addParams"><i
																					class="fas fa-plus"></i></a>
																	</div>
																</div>
															</div>
														@else
															@foreach(json_decode($paramData) as $key => $value)
																<div class="row">
																	<div class="col-md-4">
																		<div class="form-group">
																			<input type="text" name="paramKeys[]" value="{{ $key }}"
																					placeholder="@lang('Key')"
																					class="form-control @error('paramKeys.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error("paramKeys".$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-7">
																		<div class="form-group">
																			<input type="text" name="paramValues[]" value="{{ $value }}"
																					placeholder="@lang('Value')"
																					class="form-control @error('paramValues.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error("paramValues.".$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-1">
																		<div class="form-group">
																			@if($loop->first)
																				<a href="javascript:void(0);"
																					class="btn btn-primary btn-sm addParams"><i
																							class="fas fa-plus"></i></a>
																			@else
																				<a href="javascript:void(0);"
																					class="btn btn-danger btn-sm removeDiv"><i
																							class="fas fa-minus"></i></a>
																			@endif
																		</div>
																	</div>
																</div>
															@endforeach
														@endif
													</div>
												</div>

												<div class="tab-pane fade{{ ($formActive) ? 'show active' : '' }}" id="formData"
														role="tabpanel" aria-labelledby="formData-tab">
													<label for="formData">@lang('Form Data')</label>
													<div class="formDataWrapper">
														@if(is_null($formData))
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<input type="text" name="formDataKeys[]" value=""
																				placeholder="@lang('Key')"
																				class="form-control form-control-sm">
																	</div>
																</div>
																<div class="col-md-7">
																	<div class="form-group">
																		<input type="text" name="formDataValues[]" value=""
																				placeholder="@lang('Value')"
																				class="form-control form-control-sm">
																	</div>
																</div>
																<div class="col-md-1">
																	<div class="form-group">
																		<a href="javascript:void(0);"
																			class="btn btn-primary btn-sm addFormData"><i
																					class="fas fa-plus"></i></a>
																	</div>
																</div>
															</div>
														@else
															@foreach(json_decode($formData) as $key => $value)
																<div class="row">
																	<div class="col-md-4">
																		<div class="form-group">
																			<input type="text" name="formDataKeys[]" value="{{ $key }}"
																					placeholder="@lang('Key')"
																					class="form-control @error('formDataKeys.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error('formDataKeys.'.$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-7">
																		<div class="form-group">
																			<input type="text" name="formDataValues[]"
																					value="{{ $value }}" placeholder="@lang('Value')"
																					class="form-control @error('formDataValues.'.$loop->index) is-invalid @enderror">
																			<div class="invalid-feedback">
																				@error('formDataValues.'.$loop->index) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																	<div class="col-md-1">
																		<div class="form-group">
																			@if($loop->first)
																				<a href="javascript:void(0);"
																					class="btn btn-primary btn-sm addFormData"><i
																							class="fas fa-plus"></i></a>
																			@else
																				<a href="javascript:void(0);"
																					class="btn btn-danger btn-sm removeDiv"><i
																							class="fas fa-minus"></i></a>
																			@endif
																		</div>
																	</div>
																</div>
															@endforeach
														@endif
													</div>
												</div>
											</div>
											<button type="submit" class="btn btn-primary btn-block btn-sm">@lang('Save Changes')</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="paramHtml d-none">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" name="paramKeys[]" value="" placeholder="@lang('Key')"
											class="form-control form-control-sm">
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<input type="text" name="paramValues[]" value="" placeholder="@lang('Value')"
											class="form-control form-control-sm">
								</div>
							</div>
							<div class="col-md-1">
								<div class="form-group">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm removeDiv"><i class="fas fa-minus"></i></a>
								</div>
							</div>
						</div>
					</div>

					<div class="formDataHtml d-none">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" name="formDataKeys[]" value="" placeholder="@lang('Key')"
											class="form-control form-control-sm">
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<input type="text" name="formDataValues[]" value="" placeholder="@lang('Value')"
											class="form-control form-control-sm">
								</div>
							</div>
							<div class="col-md-1">
								<div class="form-group">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm removeDiv"><i class="fas fa-minus"></i></a>
								</div>
							</div>
						</div>
					</div>

					<div class="headerDataHtml d-none">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" name="headerDataKeys[]" value="" placeholder="@lang('Key')"
											class="form-control headerDataKeys">
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<input type="text" name="headerDataValues[]" value="" placeholder="@lang('Value')"
											class="form-control form-control-sm">
								</div>
							</div>
							<div class="col-md-1">
								<div class="form-group">
									<a href="javascript:void(0)" class="btn btn-danger btn-sm removeDiv"><i class="fas fa-minus"></i></a>
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

@push('extra_scripts')
    <script src="{{ asset('assets/dashboard/js/jquery-ui.min.js') }}"></script>
@endpush
@section('scripts')
    <script>
        'use strict'
        $(document).ready(function () {
            $(document).on('click', '.addHeaderData', function () {
                let headerDataHtml = $('.headerDataHtml').html();
                $('.headerDataWrapper').append(headerDataHtml);
            });

            $(document).on('click', '.addFormData', function () {
                let formDataHtml = $('.formDataHtml').html();
                $('.formDataWrapper').append(formDataHtml);
            });

            $(document).on('click', '.addParams', function () {
                let paramHtml = $('.paramHtml').html();
                $('.paramsWrapper').append(paramHtml);
            });

            $(document).on('click', '.removeDiv', function (e) {
                e.preventDefault();
                $(this).closest('.row').remove();
            });

            let availableTags = ["Accept", "Accept-CH", "Accept-CH-Lifetime", "Accept-Charset", "Accept-Encoding", "Accept-Language", "Accept-Patch", "Accept-Post", "Accept-Ranges", "Access-Control-Allow-Credentials", "Access-Control-Allow-Headers", "Access-Control-Allow-Methods", "Access-Control-Allow-Origin", "Access-Control-Expose-Headers", "Access-Control-Max-Age", "Access-Control-Request-Headers", "Access-Control-Request-Method", "Age", "Allow", "Alt-Svc", "Authorization", "Cache-Control", "Clear-Site-Data", "Connection", "Content-Disposition", "Content-Encoding", "Content-Language", "Content-Length", "Content-Location", "Content-Range", "Content-Security-Policy", "Content-Security-Policy-Report-Only", "Content-Type", "Cookie", "Cookie2", "Cross-Origin-Embedder-Policy", "Cross-Origin-Opener-Policy", "Cross-Origin-Resource-Policy", "DNT", "DPR", "Date", "Device-Memory", "Digest", "ETag", "Early-Data", "Expect", "Expect-CT", "Expires", "Feature-Policy", "Forwarded", "From", "Host", "If-Match", "If-Modified-Since", "If-None-Match", "If-Range", "If-Unmodified-Since", "Index", "Keep-Alive", "Large-Allocation", "Last-Modified", "Link", "Location", "NEL", "Origin", "Pragma", "Proxy-Authenticate", "Proxy-Authorization", "Public-Key-Pins", "Public-Key-Pins-Report-Only", "Range", "Referer", "Referrer-Policy", "Retry-After", "Save-Data", "Sec-Fetch-Dest", "Sec-Fetch-Mode", "Sec-Fetch-Site", "Sec-Fetch-User", "Sec-WebSocket-Accept", "Server", "Server-Timing", "Set-Cookie", "Set-Cookie2", "SourceMap", "Strict-Transport-Security", "TE", "Timing-Allow-Origin", "Tk", "Trailer", "Transfer-Encoding", "Upgrade", "Upgrade-Insecure-Requests", "User-Agent", "Vary", "Via", "WWW-Authenticate", "Want-Digest", "Warning", "X-Content-Type-Options", "X-DNS-Prefetch-Control", "X-Forwarded-For", "X-Forwarded-Host", "X-Forwarded-Proto", "X-Frame-Options", "X-XSS-Protection"];
            $(document).on('click', '.addHeaderData', function () {
                $(".headerDataKeys").autocomplete({
                    autoFocus: true,
                    source: function (request, response) {
                        var results = $.ui.autocomplete.filter(availableTags, request.term);
                        response(results.slice(0, 10));
                    }
                });
            });
        });
    </script>
@endsection


