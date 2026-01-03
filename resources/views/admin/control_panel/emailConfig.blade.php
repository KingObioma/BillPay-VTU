@extends('admin.layouts.master')
@section('page_title',__('Email Configuration'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Email Configuration')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Email Configuration')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.email'), 'suffix' => ''])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="row justify-content-md-center">
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Email Configuration')</h6>


											<div>
												<button class="btn btn-primary btn-sm mb-2" type="button"
														data-toggle="modal" data-target="#testEmail">
													<span class="btn-label"><i class="fas fa-envelope"></i></span>
													@lang('Test Email')
												</button>

												<a href="https://www.youtube.com/watch?v=S0Ddv3UxhWg" target="_blank"
												   class="ml-2 btn btn-primary btn-sm mb-2 text-white float-right"
												   type="button">
													<span class="btn-label"><i class="fab fa-youtube"></i></span>
													@lang('How to set up it?')
												</a>
											</div>

										</div>
										<div class="card-body">
											@include('errors.error')
											<form action="{{ route('email.config') }}" method="post">
												@csrf


												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="mail_from"
																   class="col-form-label">@lang('Mail From')</label>
															<input type="text" name="mail_from"
																   value="{{ old('mail_from',env('MAIL_FROM_ADDRESS')) }}"
																   class="form-control @error('mail_from') is-invalid @enderror"
																   placeholder="@lang('Enter mail from address')">
															<div class="invalid-feedback">
																@error('mail_from') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="mail_host"
																   class="col-form-label">@lang('Mail Host')</label>
															<input type="text" name="mail_host"
																   value="{{ old('mail_host',env('MAIL_HOST')) }}"
																   placeholder="@lang('Enter SMTP mail host')"
																   class="form-control @error('mail_host') is-invalid @enderror">
															<div class="invalid-feedback">
																@error('mail_host') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="mail_port"
																   class="col-form-label">@lang('Mail Port')</label>
															<input type="text" name="mail_port"
																   value="{{ old('mail_port',env('MAIL_PORT')) }}"
																   class="form-control @error('mail_port') is-invalid @enderror"
																   placeholder="@lang('Enter SMTP mail port')">
															<div class="invalid-feedback">
																@error('mail_port') @lang($message) @enderror
															</div>
														</div>
													</div>


													<div class=" col-md-6">
														<div class="form-group">
															<label class="col-form-label">{{trans('Encryption')}}</label>
															<select name="mail_encryption" class="form-control">
																<option value="ssl"
																		@if( old('mail_encryption', env('MAIL_ENCRYPTION')) == "ssl") selected @endif>@lang('ssl')</option>
																<option value="tls"
																		@if( old('mail_encryption',env('MAIL_ENCRYPTION')) == "tls") selected @endif>@lang('tls')</option>
															</select>

															<div class="invalid-feedback">
																@error('mail_encryption') @lang($message) @enderror
															</div>

														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label for="mail_username"
																   class="col-form-label">@lang('Username')</label>
															<input type="text" name="mail_username"
																   value="{{ old('mail_username',env('MAIL_USERNAME')) }}"
																   class="form-control @error('mail_username') is-invalid @enderror"
																   placeholder="@lang('Enter SMTP mail username')">
															<div class="invalid-feedback">
																@error('mail_username') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="mail_password"
																   class="col-form-label">@lang('Password')</label>
															<input type="text" name="mail_password"
																   value="{{ old('mail_password',env('MAIL_PASSWORD')) }}"
																   class="form-control @error('mail_password') is-invalid @enderror"
																   placeholder="@lang('Enter SMTP mail password')">
															<div class="invalid-feedback">
																@error('mail_password') @lang($message) @enderror
															</div>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>@lang('Email Notification')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="email_notification"
																		   value="0"
																		   class="selectgroup-input" {{ old('email_notification', $basicControl->email_notification) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="email_notification"
																		   value="1"
																		   class="selectgroup-input" {{ old('email_notification', $basicControl->email_notification) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('email_notification')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>@lang('Email Verification')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="email_verification"
																		   value="0"
																		   class="selectgroup-input" {{ old('email_verification', $basicControl->email_verification) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="email_verification"
																		   value="1"
																		   class="selectgroup-input" {{ old('email_verification', $basicControl->email_verification) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('email_verification')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
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
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>
	</div>


	<!-- testEmail Modal -->
	<div class="modal fade" id="testEmail">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="{{route('testEmail')}}" class="" enctype="multipart/form-data">
				@csrf
				<!-- Modal Header -->
					<div class="modal-header ">
						<h5 class="modal-title">@lang('Test Email')</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<!-- Modal body -->
					<div class="modal-body">
						<label for="email">@lang('Enter Your Email')</label>
						<input type="email" class="form-control form-control" name="email" id="email"
							   placeholder="@lang('Enter Your Email')">
					</div>
					<!-- Modal footer -->
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('Close')</span>
						</button>
						<button type="submit" class=" btn btn-primary "><span>@lang('Yes')</span>
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
@endsection

