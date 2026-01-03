@extends('admin.layouts.master')
@section('page_title', __('Profile'))
@push('extra_styles')
@endpush
@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Profile')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('Profile')</div>
			</div>
		</div>
		<div class="row mb-3">
			<div class="container-fluid" id="container-wrapper">
				<div class="row justify-content-md-center">
					<div class="col-lg-6">
						<div class="card mb-4 card-primary shadow">
							<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
								<div class="media">
									<img class="align-self-start mr-3 img-profile-view img-thumbnail" src="{{ getFile($adminProfile->driver,$adminProfile->profile_picture) }}" alt="@lang('Generic placeholder image')">
									<div class="media-body">
										<h5 class="mt-0 font-weight-bold text-primary">{{ __($admin->name) }}</h5>
										<p>
											<i class="fas fa-user"></i> {{ __($admin->username) }} <br>
											<i class="fas fa-mobile-alt"></i> {{ __($adminProfile->phone) }} <br>
											<i class="fas fa-envelope"></i> {{ __($admin->email) }} <br>
										</p>
									</div>
								</div>
							</div>
							<div class="card-body">
								<form method="post" action="{{ route('admin.profile') }}" enctype="multipart/form-data">
									@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="name">@lang('Name')</label>
												<input type="text" name="name" placeholder="@lang('Enter your full name')"
													   value="{{ old('name',$admin->name) }}"
													   class="form-control @error('name') is-invalid @enderror">
												<div class="invalid-feedback">@error('name') @lang($message) @enderror</div>
											</div>
											<div class="form-group">
												<label for="name">@lang('Email')</label>
												<input type="email" name="email" placeholder="@lang('Enter your email')"
													   value="{{ old('email',$admin->email) }}"
													   class="form-control @error('email') is-invalid @enderror">
												<div class="invalid-feedback">@error('email') @lang($message) @enderror</div>
											</div>
											<div class="form-group">
												<label for="city">@lang('City')</label>
												<input type="text" name="city" placeholder="@lang('Enter your city')"
													   value="{{ old('city',$adminProfile->city ) }}"
													   class="form-control @error('city') is-invalid @enderror">
												<div class="invalid-feedback">@error('city') @lang($message) @enderror</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="phone">@lang('Phone')</label>
												<input type="text" name="phone" placeholder="@lang('Enter your phone number')" value="{{ old('phone',$adminProfile->phone ) }}"
													   class="form-control @error('phone') is-invalid @enderror">
												<div class="invalid-feedback">@error('phone') @lang($message) @enderror</div>
											</div>
											<div class="form-group">
												<label for="name">@lang('Username')</label>
												<input type="text" name="username" placeholder="@lang('Enter your username')"
													   value="{{ old('username',$admin->username) }}"
													   class="form-control @error('username') is-invalid @enderror">
												<div class="invalid-feedback">@error('username') @lang($message) @enderror</div>
											</div>
											<div class="form-group">
												<label for="state">@lang('State')</label>
												<input type="text" name="state" placeholder="@lang('Enter your state')" value="{{ old('state',$adminProfile->state) }}"
													   class="form-control @error('state') is-invalid @enderror">
												<div class="invalid-feedback">@error('state') @lang($message) @enderror</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="address">@lang('Address')</label>
										<textarea class="form-control @error('address') is-invalid @enderror"
												name="address" rows="5">{{ old('address', $adminProfile->address) }}</textarea>
										<div class="invalid-feedback">@error('address') @lang($message) @enderror</div>
									</div>

									<div class="form-group">
										<div class="custom-file">
											<input type="file" class="custom-file-input file-upload-input" id="profile_picture" name="profile_picture">
											<label class="custom-file-label form-control-sm @error('profile_picture') is-invalid @enderror" for="profile_picture">@lang('Choose profile picture')</label>
											<div class="invalid-feedback">@error('profile_picture') @lang($message) @enderror</div>
										</div>
									</div>
									<button type="submit" class="btn btn-primary btn-sm btn-block">@lang('Update Profile')</button>
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
@push('extra_scripts')
@endpush
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$(document).on('change', '.file-upload-input', function () {
				let _this = $(this);
				let reader = new FileReader();
				reader.readAsDataURL(this.files[0]);
				reader.onload = function (e) {
					$('.img-profile-view').attr('src', e.target.result);
				}
			});
		})
	</script>
@endsection
