@extends('admin.layouts.master')
@section('page_title', __('App Setting'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('App Setting')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('App Setting')</div>
				</div>
			</div>
			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('App Setting')</h6>
								</div>
								<div class="card-body">
									<form action="{{ route('appSetting') }}" method="POST">
										@csrf
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="primaryColor">@lang('APP COLOR')</label>
													<input type="color" name="appColor"
														   value="{{ old('appColor',config('basic.appColor')) }}"
														   class="form-control @error('appColor') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('appColor') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="name">@lang('APP VERSION')</label>
													<input type="text" name="appVersion" value="{{ config('basic.appVersion') }}"
														   class="form-control @error('appVersion') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('appVersion') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="name">@lang('APP BUILD')</label>
													<input type="text" name="appBuild" value="{{ config('basic.appBuild') }}"
														   class="form-control @error('appBuild') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('appBuild') @lang($message) @enderror</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>@lang('IS MAJOR VERSION')</label>
													<div class="selectgroup w-100">
														<label class="selectgroup-item">
															<input type="radio" name="isMajor" value="0"
																   class="selectgroup-input" {{ old('isMajor', config('basic.isMajor')) == 0 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('OFF')</span>
														</label>
														<label class="selectgroup-item">
															<input type="radio" name="isMajor" value="1"
																   class="selectgroup-input" {{ old('isMajor', config('basic.isMajor')) == 1 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('ON')</span>
														</label>
													</div>
													@error('isMajor')
													<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
													@enderror
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-sm btn-primary btn-block">
											<span>@lang('Save Changes')</span>
										</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
