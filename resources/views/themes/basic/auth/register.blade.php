@extends($theme.'layouts.app')
@section('title',__('Sign Up'))
@push('css-lib')
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/intlTelInput.min.css')}}"/>
@endpush
@section('content')
	<!-- login-signup section start -->
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-8">
					<div class="login-signup-form">
						<form action="{{ route('register') }}" method="post">
							@csrf
							<div class="login-signup-header">
								<h4>@lang(optional(@$template->description)->title)</h4>
								<div class="description">@lang(optional(@$template->description)->sub_title)</div>
							</div>
							<div class="row g-3">
								<div class="col-12">
									<input type="text" name="name" value="{{ old('name') }}" class="form-control"
										   id="exampleInputEmail0"
										   placeholder="@lang('Full Name')">
									<div class="text-danger">@error('name') @lang($message) @enderror</div>
								</div>
								<div class="col-12">
									<input type="email" name="email" value="{{ old('email') }}" class="form-control"
										   id="exampleInputEmail4"
										   placeholder="@lang('Email')">
									<div class="text-danger">@error('email') @lang($message) @enderror</div>
								</div>
								<div class="col-12">
									<input type="text" name="username" value="{{ old('username') }}"
										   class="form-control" id="exampleInputEmail3"
										   placeholder="@lang('User Name')">
									<div class="text-danger">@error('username') @lang($message) @enderror</div>
								</div>
								@if($referral)
									<div class="col-12">
										<input type="text" name="referral" value="{{ old('referral',$referral) }}"
											   class="form-control"
											   id="exampleInputEmail4"
											   placeholder="@lang('Username')">
										<div class="text-danger">@error('referral') @lang($message) @enderror</div>
									</div>
								@endif
								<div class="col-12">
									<input type="hidden" id="country" name="phone_code" value="">
									<input id="telephone" class="form-control" name="phone" type="tel">
									<div class="text-danger">@error('phone') @lang($message) @enderror</div>
								</div>
								<div class="col-12">
									<div class="password-box">
										<input type="password" name="password" value="{{ old('password') }}"
											   class="form-control password" id="exampleInputPassword1"
											   placeholder="@lang('Password')">
										<i class="password-icon fa-regular fa-eye"></i>
									</div>
									<div class="text-danger">@error('password') @lang($message) @enderror</div>
								</div>
								<div class="col-12">
									<input type="password" name="password_confirmation" class="form-control"
										   id="exampleInputPassword2"
										   placeholder="@lang('Confirm Password')">
								</div>
								@if(basicControl()->reCaptcha_status_registration &&  basicControl()->google_reCaptcha_status)

									<div class="form-group">
										{!! NoCaptcha::renderJs() !!}
										{!! NoCaptcha::display() !!}
										@error('g-recaptcha-response')
										<div class="text-danger">@lang($message)</div>
										@enderror
									</div>
								@endif

								@if((basicControl()->manual_reCaptcha_status == 1) && (basicControl()->reCaptcha_status_registration))
									<div class="input-box mb-4">
										<input type="text" tabindex="2"
											   class="form-control @error('captcha') is-invalid @enderror"
											   name="captcha" id="captcha" autocomplete="off"
											   placeholder="@lang('Enter captcha code')" >

										@error('captcha')
										<div class="text-danger">@lang($message)</div>
										@enderror
									</div>

									<div class="mb-4">
										<div
											class="input-group input-group-merge d-flex justify-content-between"
											data-hs-validation-validate-class>
											<img src="{{route('captcha').'?rand='. rand()}}"
												 id='captcha_image2'>
											<a class="input-group-append input-group-text text-white"
											   href='javascript: refreshCaptcha2();'>
												<i class="fal fa-sync"></i>
											</a>
										</div>
									</div>
								@endif

							</div>
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('signup')</button>
							<div class="pt-20 text-center">
								@lang('Already have an account')?
								<p class="mb-0 highlight"><a href="{{ route('login') }}">@lang('Login here')</a></p>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- login-signup section end -->
@endsection

@push('extra-js')
	<script src="{{ asset($themeTrue . 'js/intlTelInput.min.js')}}"></script>
@endpush
@section('scripts')
	<script>
		"use strict";

		// International Telephone Input start
		const input = document.querySelector("#telephone");
		window.intlTelInput(input, {
			initialCountry: "bd",
			separateDialCode: true,
		});
		// International Telephone Input end

		// input field show hide password start
		const password = document.querySelector('.password');
		const passwordIcon = document.querySelector('.password-icon');

		passwordIcon.addEventListener("click", function () {
			if (password.type == 'password') {
				password.type = 'text';
				passwordIcon.classList.add('fa-eye-slash');
			} else {
				password.type = 'password';
				passwordIcon.classList.remove('fa-eye-slash');
			}
		})
		// input field show hide password end

		$('.iti__country-list li').on('click',function () {
			$("#country").val($(this).data('dial-code'));
		})

		function refreshCaptcha() {
			let img = document.images['captcha_image'];
			img.src = img.src.substring(
				0, img.src.lastIndexOf("?")
			) + "?rand=" + Math.random() * 1000;
		}
		function refreshCaptcha2() {
			let img = document.images['captcha_image2'];
			img.src = img.src.substring(
				0, img.src.lastIndexOf("?")
			) + "?rand=" + Math.random() * 1000;
		}

		$(document).on('click','.btn-custom', function (){
			$('.text-danger').html('');
			refreshCaptcha();
		})

	</script>
@endsection
