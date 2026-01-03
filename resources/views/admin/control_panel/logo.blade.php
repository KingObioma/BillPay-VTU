@extends('admin.layouts.master')
@section('page_title',__('Logo, Favicon & Breadcrumb Settings'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Logo, Favicon & Breadcrumb Settings')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Logo, Favicon & Breadcrumb Settings')</div>
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
									<div class="bd-callout bd-callout-warning mx-2">
										<i class="fas fa-info-circle mr-2"></i> @lang('After changes logo/seo. Please clear your browser\'s cache to see changes.')
									</div>


									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Logo, Favicon & Breadcrumb Settings')</h6>
										</div>
										<div class="card-body">
											<form method="post" action="{{ route('logo.update') }}"
												  enctype="multipart/form-data">
												@csrf
												<div class="row">
													<div class="form-group mb-4 col-md-4">
														<label class="col-form-label">@lang('Logo')</label>
														<div id="image-preview" class="image-preview"
															 style="background-image: url({{ getFile(config('basic.default_file_driver'),config('basic.logo_image')) ? : 0 }});">
															<label for="image-upload"
																   id="image-label">@lang('Choose File')</label>
															<input type="file" name="logo"
																   class=" @error('logo') is-invalid @enderror"
																   id="image-upload"/>
														</div>
														<div class="invalid-feedback">
															@error('logo') @lang($message) @enderror
														</div>
													</div>
													<div class="form-group mb-4 col-md-4">
														<label class="col-form-label">@lang('Footer Logo')</label>
														<div id="image-preview-footer" class="image-preview"
															 style="background-image: url({{ getFile(config('basic.default_file_driver'),config('basic.footer_image')) ? : 0 }});">
															<label for="image-upload-footer"
																   id="image-label-footer">@lang('Choose File')</label>
															<input type="file" name="footer_logo"
																   class=" @error('footer_logo') is-invalid @enderror"
																   id="image-upload-footer"/>
														</div>
														<div class="invalid-feedback">
															@error('footer_logo') @lang($message) @enderror
														</div>
													</div>

													<div class="form-group mb-4 col-md-4">
														<label class="col-form-label">@lang('Favicon')</label>
														<div id="image-preview-favicon" class="image-preview"
															 style="background-image: url({{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) ? : 0 }});">
															<label for="image-upload-favicon"
																   id="image-label-favicon">@lang('Choose File')</label>
															<input type="file" name="favicon"
																   class=" @error('favicon') is-invalid @enderror"
																   id="image-upload-favicon"/>
														</div>
														<div class="invalid-feedback">
															@error('favicon') @lang($message) @enderror
														</div>
													</div>
													<div class="form-group mb-4 col-md-4">
														<label class="col-form-label">@lang('Admin Logo')</label>
														<div id="image-preview-breadcrumb" class="image-preview"
															 style="background-image: url({{ getFile(config('basic.default_file_driver'),config('basic.admin_logo')) ? : 0 }});">
															<label for="image-upload-breadcrumb"
																   id="image-label-breadcrumb">@lang('Choose File')</label>
															<input type="file" name="admin_logo"
																   class=" @error('breadcrumb') is-invalid @enderror"
																   id="image-upload-breadcrumb"/>
														</div>
														<div class="invalid-feedback">
															@error('admin_logo') @lang($message) @enderror
														</div>
													</div>

													<div class="form-group mb-4 col-md-4">
														<label class="col-form-label">@lang('Banner Image')</label>
														<div id="image-preview-banner" class="image-preview"
															 style="background-image: url({{ getFile(config('basic.default_file_driver'),config('basic.banner_image')) ? : 0 }});">
															<label for="image-upload-banner"
																   id="image-label-banner">@lang('Choose File')</label>
															<input type="file" name="banner_image"
																   class=" @error('banner_image') is-invalid @enderror"
																   id="image-upload-banner"/>
														</div>
														<div class="invalid-feedback">
															@error('banner_image') @lang($message) @enderror
														</div>
													</div>
												</div>

												<button type="submit"
														class="btn btn-primary btn-sm btn-block mt-1">@lang('Save Change')</button>
											</form>
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
	<script type="text/javascript">
		$(document).ready(function () {
			$.uploadPreview({
				input_field: "#image-upload",   // Default: .image-upload
				preview_box: "#image-preview",  // Default: .image-preview
				label_field: "#image-label",    // Default: .image-label
				label_default: "Choose File",   // Default: Choose File
				label_selected: "Change File",  // Default: Change File
				no_label: false                 // Default: false
			});
			$.uploadPreview({
				input_field: "#image-upload-footer",
				preview_box: "#image-preview-footer",
				label_field: "#image-label-footer",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
			$.uploadPreview({
				input_field: "#image-upload-admin",
				preview_box: "#image-preview-admin",
				label_field: "#image-label-admin",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
			$.uploadPreview({
				input_field: "#image-upload-favicon",
				preview_box: "#image-preview-favicon",
				label_field: "#image-label-favicon",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
			$.uploadPreview({
				input_field: "#image-upload-breadcrumb",
				preview_box: "#image-preview-breadcrumb",
				label_field: "#image-label-breadcrumb",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
			$.uploadPreview({
				input_field: "#image-upload-banner",
				preview_box: "#image-preview-banner",
				label_field: "#image-label-banner",
				label_default: "Choose File",
				label_selected: "Change File",
				no_label: false
			});
		});
	</script>
@endsection
