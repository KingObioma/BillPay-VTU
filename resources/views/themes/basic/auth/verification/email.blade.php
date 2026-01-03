@extends($theme.'layouts.app')
@section('title',__('Email Verification'))
@section('content')
	<!-- login-signup section start -->
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-6 order-2 order-md-1">
					<div class="login-signup-form">
						<form action="{{ route('user.mailVerify') }}" method="post">
							@csrf
							<div class="section-header">
								<h4>@lang(optional(@$template->description)->title)</h4>
								<div class="description">@lang(optional(@$template->description)->sub_title)</div>
							</div>
							<div class="row g-4">
								<div class="col-12">
									<input type="text" name="code"
										   class="form-control" id="exampleInputEmail1"
										   placeholder="@lang('Code')">
									<div class="text-danger">
										@error('code') @lang($message) @enderror
										@error('error') @lang($message) @enderror
									</div>
								</div>
							</div>
							@if (Route::has('user.resendCode'))
								<div class="text-end mt-1 me-2">
									<p class="mb-0 highlight"><a
											href="{{route('user.resendCode')}}?type=email">@lang('Resend code')?</a></p>
									@error('resend')
									<p class="text-danger mt-1">@lang($message)</p>
									@enderror
								</div>
							@endif
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Submit')</button>
						</form>
					</div>
				</div>
				@if(isset($template->media) && $template->templateMedia())
					<div class="col-xl-6 col-md-6 order-1 order-md-2 d-none d-md-block">
						<div class="login-signup-thums">
							<img
								src="{{getFile(optional($template->media)->driver,@$template->templateMedia()->image)}}"
								alt="...">
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>
	<!-- login-signup section end -->
@endsection
