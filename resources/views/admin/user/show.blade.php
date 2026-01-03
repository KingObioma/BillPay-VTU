@extends('admin.layouts.master')
@section('page_title', __('User profile'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('User profile')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('User profile')</div>
				</div>
			</div>
			<div class="row mb-3">
				<div class="container-fluid user-profile" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-10">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<div class="media custom--media flex-wrap d-flex flex-row justify-content-center">
										<div class="mr-2">
											<img class="align-self-start mr-3 img-profile-view img-thumbnail"
												 src="{{ getFile($userProfile->driver,$userProfile->profile_picture) }}"
												 alt="{{ __($user->name) }}">
										</div>
										<div class="media-body">
											<h5 class="mt-0 font-weight-bold text-primary">{{ __($user->name) }}</h5>
											<p>
												<i class="fas fa-user"></i> {{ __($user->username) }} <br>
												<i class="fas fa-mobile-alt"></i> {{ __($userProfile->phone) }} <br>
												<i class="fas fa-envelope"></i> {{ __( $user->email) }} <br>
											</p>
										</div>
									</div>
								</div>
								<div class="card-body">
									<form method="post" action="{{ route('user.edit',$user) }}"
										  enctype="multipart/form-data">
										@csrf
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="username">@lang('Username')</label>
													<input type="text" name="username"
														   placeholder="@lang('Username of uesr')"
														   value="{{ old('username',$user->username) }}"
														   class="form-control @error('username') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('username') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="email">@lang('E-Mail')</label>
													<input type="text" name="email"
														   placeholder="@lang('User email address')"
														   value="{{ old('email',$user->email) }}"
														   class="form-control @error('email') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('email') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="name">@lang('Name')</label>
													<input type="text" name="name" placeholder="@lang('User full name')"
														   value="{{ old('name',$user->name) }}"
														   class="form-control @error('name') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('name') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="city">@lang('City')</label>
													<input type="text" name="city" placeholder="@lang('User city')"
														   value="{{ old('city',$userProfile->city ) }}"
														   class="form-control @error('city') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('city') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="phone">@lang('Phone')</label>
													<div class="input-group-sm media">
														<div class="input-group-prepend">
															<select name="phone_code"
																	class="form-control-sm country_code">
																@foreach($countries as $value)
																	<option
																		value="{{$value['phone_code']}}"{{ $userProfile->phone_code == $value['phone_code'] ? 'selected' : '' }}>
																		{{ __($value['phone_code']) }}
																		<strong>({{ __($value['name']) }})</strong>
																	</option>
																@endforeach
															</select>
														</div>
														<input type="text" name="phone" class="form-control"
															   value="{{old('phone',$userProfile->phone)}}"
															   placeholder="@lang('User Phone Number')">
													</div>
													<div
														class="invalid-feedback">@error('phone') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group search-currency-dropdown">
													<label for="language">@lang('Language')
														<i class="fas fa-info-circle" data-toggle="tooltip"
														   data-placement="top"
														   title="@lang('Select language to get notification on preferred language')"></i>
													</label>
													<select name="language"
															class="form-control @error('language') is-invalid @enderror">
														@foreach($languages as $language)
															<option
																value="{{ $language->id }}" {{ old('language', $user->language_id) == $language->id ? 'selected' : '' }}>
																{{ __($language->name) }}
															</option>
														@endforeach
													</select>
													<div
														class="invalid-feedback">@error('language') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="state">@lang('State')</label>
													<input type="text" name="state" placeholder="@lang('User state')"
														   value="{{ old('state',$userProfile->state) }}"
														   class="form-control @error('state') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('state') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="password">@lang('Password')</label>
													<input type="password" name="password"
														   placeholder="@lang('User password')"
														   value="{{ old('password') }}"
														   class="form-control @error('password') is-invalid @enderror">
													<div
														class="invalid-feedback">@error('password') @lang($message) @enderror</div>
												</div>
											</div>

											<div class="col-md-4">
												<div class="form-group">
													<label>@lang('Email Verification')</label>
													<div class="selectgroup w-100">
														<label class="selectgroup-item">
															<input type="radio" name="email_verification" value="0"
																   class="selectgroup-input" {{ old('email_verification', $user->email_verification) == 0 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Unverified')</span>
														</label>
														<label class="selectgroup-item">
															<input type="radio" name="email_verification" value="1"
																   class="selectgroup-input" {{ old('email_verification', $user->email_verification) == 1 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Verified')</span>
														</label>
													</div>
													@error('email_verification')
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
																   class="selectgroup-input" {{ old('sms_verification', $user->sms_verification) == 0 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Unverified')</span>
														</label>
														<label class="selectgroup-item">
															<input type="radio" name="sms_verification" value="1"
																   class="selectgroup-input" {{ old('sms_verification', $user->sms_verification) == 1 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Verified')</span>
														</label>
													</div>
													@error('sms_verification')
													<span class="text-danger" role="alert">
														<strong>{{ __($message) }}</strong>
													</span>
													@enderror
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>@lang('User Status')</label>
													<div class="selectgroup w-100">
														<label class="selectgroup-item">
															<input type="radio" name="status" value="0"
																   class="selectgroup-input" {{ old('status', $user->status) == 0 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Suspend')</span>
														</label>
														<label class="selectgroup-item">
															<input type="radio" name="status" value="1"
																   class="selectgroup-input" {{ old('status', $user->status) == 1 ? 'checked' : ''}}>
															<span class="selectgroup-button">@lang('Active')</span>
														</label>
													</div>
													@error('status')
													<span class="text-danger" role="alert">
														<strong>{{ __($message) }}</strong>
													</span>
													@enderror
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="address">@lang('Address')</label>
													<textarea
														class="form-control @error('address') is-invalid @enderror"
														name="address"
														rows="5">{{ old('address', $userProfile->address) }}</textarea>
													<div
														class="invalid-feedback">@error('address') @lang($message) @enderror</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<div class="custom-file">
														<input type="file" class="custom-file-input file-upload-input"
															   id="profile_picture" name="profile_picture">
														<label class="custom-file-label form-control-sm"
															   for="profile_picture">@lang('Choose profile picture')</label>
													</div>
												</div>
											</div>
										</div>
										<button type="submit"
												class="btn btn-primary btn-sm btn-block">@lang('Update Profile')</button>
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
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$('[data-toggle="tooltip"]').tooltip();
			$(document).on('change', '.file-upload-input', function () {
				let _this = $(this);
				let reader = new FileReader();
				reader.readAsDataURL(this.files[0]);
				reader.onload = function (e) {
					$('.img-profile-view').attr('src', e.target.result);
				}
			});
		});
	</script>
@endsection
