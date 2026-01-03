@extends('admin.layouts.master')
@section('page_title', __('Google Analytics Control'))
@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Google Analytics Control')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('settings') }}">@lang('Settings')</a>
				</div>
				<div class="breadcrumb-item active">
					<a href="{{ route('plugin.config') }}">@lang('Plugin')</a>
				</div>

				<div class="breadcrumb-item">@lang('Google Analytics Control')</div>
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
								<h6 class="m-0 font-weight-bold text-primary">@lang('Google Analytics Control')</h6>
							</div>
							<div class="card-body">
								<div class="row justify-content-center">
									<div class="col-md-8">
										<form action="{{ route('google.analytics.control') }}" method="post">
											@csrf
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="MEASUREMENT_ID">@lang('MEASUREMENT_ID')</label>
														<input type="text" name="MEASUREMENT_ID" value="{{ old('MEASUREMENT_ID',$basicControl->MEASUREMENT_ID) }}" placeholder="@lang('MEASUREMENT_ID')"
															   class="form-control @error('MEASUREMENT_ID') is-invalid @enderror">
														<div class="invalid-feedback">@error('MEASUREMENT_ID') @lang($message) @enderror</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>@lang('Google Analytics')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="analytic_status" value="0"
																	   class="selectgroup-input" {{ old('analytic_status', $basicControl->analytic_status) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="analytic_status" value="1"
																	   class="selectgroup-input" {{ old('analytic_status', $basicControl->analytic_status) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('analytic_status')
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
