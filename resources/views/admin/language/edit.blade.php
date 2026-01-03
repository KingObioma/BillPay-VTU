@extends('admin.layouts.master')
@section('page_title', __('Edit Language'))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Edit Language')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('settings') }}">@lang('Settings')</a>
					</div>
					<div class="breadcrumb-item active">
						<a href="{{ route('language.index') }}">@lang('Language')</a>
					</div>
					<div class="breadcrumb-item">@lang('Edit Language')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
					</div>
					<div class="col-12 col-md-8 col-lg-9">

						<div class="row mb-3">
							<div class="container-fluid" id="container-wrapper">
								<div class="row justify-content-md-center">
									<div class="col-lg-12">
										<div class="card mb-4 card-primary shadow">
											<div
												class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
												<h6 class="m-0 font-weight-bold text-primary">@lang('Edit Language')</h6>
												<a href="{{ url()->previous() }}"
												   class="btn btn-sm btn-outline-primary"> <i
														class="fas fa-arrow-left"></i> @lang('Back')</a>
											</div>
											<div class="card-body">
												<form method="post" action="{{ route('language.update',$language) }}"
													  enctype="multipart/form-data">
													@csrf
													@method('PUT')
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="name">@lang('Name')</label>
																<input type="text" name="name"
																	   placeholder="@lang('Enter name of country')"
																	   value="{{ old('name', $language->name) }}"
																	   class="form-control @error('name') is-invalid @enderror">
																<div
																	class="invalid-feedback">@error('name') @lang($message) @enderror</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="short_name">@lang('Short Name')</label>
																<select name="short_name"
																		class="select2-single form-control @error('short_name') is-invalid @enderror"
																		id="short_name">
																	@foreach(config('languages.langCode') as $key => $value)
																		<option
																			value="{{$key}}" {{ (old('short_name', $language->short_name) == $key ? ' selected' : '') }}>{{ __($key).' - '.__($value) }}</option>
																	@endforeach
																</select>
																<div
																	class="invalid-feedback">@error('short_name') @lang($message) @enderror</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>@lang('Enable Language')</label>
																<div class="selectgroup w-100">
																	<label class="selectgroup-item">
																		<input type="radio" name="is_active" value="0"
																			   class="selectgroup-input" {{ (old('is_active', $language->is_active) == 0 ? ' checked' : '') }}>
																		<span
																			class="selectgroup-button">@lang('OFF')</span>
																	</label>
																	<label class="selectgroup-item">
																		<input type="radio" name="is_active" value="1"
																			   class="selectgroup-input" {{ (old('is_active', $language->is_active) == 1 ? ' checked' : '') }}>
																		<span
																			class="selectgroup-button">@lang('ON')</span>
																	</label>
																</div>
																@error('is_active')
																<span class="text-danger" role="alert">
														<strong>{{ __($message) }}</strong>
													</span>
																@enderror
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>@lang('Enable RTL')</label>
																<div class="selectgroup w-100">
																	<label class="selectgroup-item">
																		<input type="radio" name="rtl" value="0"
																			   class="selectgroup-input" {{ (old('rtl', $language->rtl) == 0 ? ' checked' : '') }}>
																		<span
																			class="selectgroup-button">@lang('OFF')</span>
																	</label>
																	<label class="selectgroup-item">
																		<input type="radio" name="rtl" value="1"
																			   class="selectgroup-input" {{ (old('rtl', $language->rtl) == 1 ? ' checked' : '') }}>
																		<span
																			class="selectgroup-button">@lang('ON')</span>
																	</label>
																</div>
																@error('rtl')
																<span class="text-danger" role="alert">
														<strong>{{ __($message) }}</strong>
													</span>
																@enderror
															</div>
														</div>
														<div class="col-md-12">
															<div class="form-group mb-4">
																<label
																	class="col-form-label">@lang('Choose Flag')</label>
																<div id="image-preview" class="image-preview"
																	 style="background-image: url({{ getFile(config('location.language.path').$language->flag) ? : 0 }});">
																	<label for="image-upload"
																		   id="image-label">@lang('Choose Flag')</label>
																	<input type="file" name="flag"
																		   class=" @error('flag') is-invalid @enderror"
																		   id="image-upload"/>
																</div>
																<div class="invalid-feedback">
																	@error('flag') @lang($message) @enderror
																</div>
															</div>
														</div>
													</div>
													<input type="submit" class="btn btn-primary btn-sm btn-block"
														   value="@lang('Save Changes')">
												</form>
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
@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$('.select2-single').select2();

			$.uploadPreview({
				input_field: "#image-upload",
				preview_box: "#image-preview",
				label_field: "#image-label",
				label_default: "Choose Flag",
				label_selected: "Change Flag",
				no_label: false
			});
		});
	</script>
@endsection
