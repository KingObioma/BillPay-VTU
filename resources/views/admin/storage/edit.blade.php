@extends('admin.layouts.master')
@section('page_title', __('Edit Storage'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Edit Storage')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('settings') }}">@lang('Settings')</a>
					</div>
					<div class="breadcrumb-item active">
						<a href="{{ route('storage.index') }}">@lang('Storage')</a>
					</div>
					<div class="breadcrumb-item">@lang('Edit Storage')</div>
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
												<h6 class="m-0 font-weight-bold text-primary">@lang('Edit Storage')</h6>
												<a href="{{ route('storage.index') }}"
												   class="btn btn-sm btn-outline-primary"> <i
														class="fas fa-arrow-left"></i> @lang('Back')</a>
											</div>
											<div class="card-body">
												<form method="post" action="{{route('storage.edit',$storage->id)}}"
													  enctype="multipart/form-data">
													@csrf
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="name">@lang('Name')</label>
																<input type="text" name="name"
																	   placeholder="@lang('Enter name')"
																	   value="{{ old('name', $storage->name) }}"
																	   class="form-control @error('name') is-invalid @enderror">
																<div
																	class="invalid-feedback">@error('name') @lang($message) @enderror</div>
															</div>
														</div>
														@if($storage->parameters)
															@foreach ($storage->parameters as $key => $parameter)
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
														@endif
														<div class="col-md-12">
															<div class="form-group mb-4">
																<label
																	class="col-form-label">@lang('Choose Logo')</label>
																<div id="image-preview" class="image-preview"
																	 style="background-image: url({{ getFile($storage->driver,$storage->logo) ? : 0 }});">
																	<label for="image-upload"
																		   id="image-label">@lang('Choose Logo')</label>
																	<input type="file" name="logo"
																		   class=" @error('logo') is-invalid @enderror"
																		   id="image-upload"/>
																</div>
																<div class="invalid-feedback">
																	@error('logo') @lang($message) @enderror
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
	<script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
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
