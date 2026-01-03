@extends('admin.layouts.master')
@section('page_title', __('Google reCaptcha Control'))
@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Google reCaptcha Control')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('settings') }}">@lang('Settings')</a>
				</div>
				<div class="breadcrumb-item active">
					<a href="{{ route('plugin.config') }}">@lang('Plugin')</a>
				</div>

				<div class="breadcrumb-item">@lang('Google reCaptcha Control')</div>
			</div>
		</div>

		<div class="section-body">
			<div class="row mt-sm-4">
				<div class="col-12 col-md-4 col-lg-3">
					@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.plugin'), 'suffix' => ''])
				</div>
				<div class="col-12 col-md-8 col-lg-9">
					<div class="container-fluid" id="container-wrapper">
						<div class="card mb-4 card-primary shadow">
							<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
								<h6 class="m-0 font-weight-bold text-primary">@lang('Google reCaptcha Control')</h6>
							</div>
							<div class="card-body">
								<div class="row justify-content-center">
									<div class="col-md-10">
										<form action="{{ route('google.recaptcha.control') }}" method="post">
											@csrf
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="NOCAPTCHA_SECRET">@lang('NOCAPTCHA SECRET')</label>
														<input type="text" name="NOCAPTCHA_SECRET" value="{{ old('NOCAPTCHA_SECRET',env('NOCAPTCHA_SECRET')) }}" placeholder="@lang('NOCAPTCHA_SECRET')"
															   class="form-control @error('NOCAPTCHA_SECRET') is-invalid @enderror">
														<div class="invalid-feedback">@error('NOCAPTCHA_SECRET') @lang($message) @enderror</div>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label for="NOCAPTCHA_SITEKEY">@lang('NOCAPTCHA SITEKEY')</label>
														<input type="text" name="NOCAPTCHA_SITEKEY" value="{{ old('NOCAPTCHA_SITEKEY',env('NOCAPTCHA_SITEKEY')) }}" placeholder="@lang('NOCAPTCHA SITEKEY')"
															   class="form-control @error('NOCAPTCHA_SITEKEY') is-invalid @enderror">
														<div class="invalid-feedback">@error('NOCAPTCHA_SITEKEY') @lang($message) @enderror</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>@lang('Login Status')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="reCaptcha_status_login" value="0"
																	   class="selectgroup-input" {{ old('reCaptcha_status_login', $basicControl->reCaptcha_status_login) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="reCaptcha_status_login" value="1"
																	   class="selectgroup-input" {{ old('reCaptcha_status_login', $basicControl->reCaptcha_status_login) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('reCaptcha_status_login')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
														@enderror
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>@lang('Registration Status')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="reCaptcha_status_registration" value="0"
																	   class="selectgroup-input" {{ old('reCaptcha_status_registration', $basicControl->reCaptcha_status_registration) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="reCaptcha_status_registration" value="1"
																	   class="selectgroup-input" {{ old('reCaptcha_status_registration', $basicControl->reCaptcha_status_registration) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('reCaptcha_status_registration')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
														@enderror
													</div>
												</div>
											</div>
											<div class="form-group">
												<button type="submit" name="submit" class="btn btn-primary btn-sm btn-block">@lang('Save changes')</button>
											</div>
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
