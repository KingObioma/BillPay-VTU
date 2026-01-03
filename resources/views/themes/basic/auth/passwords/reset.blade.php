@extends($theme.'layouts.app')
@section('title',__('Reset Password'))

@section('content')
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-6 order-2 order-md-1">
					<div class="login-signup-form">
						<form action="{{ route('password.update') }}" method="post">
							@csrf
							<div class="section-header">
								<h4>@lang(optional(@$template->description)->title)</h4>
								<div class="description">@lang(optional(@$template->description)->sub_title)</div>
							</div>
							<div class="row g-4">
								<input type="hidden" name="token" value="{{ $token }}">
								<input type="hidden" name="email" value="{{ $email ?? old('email') }}">
								<div class="col-12">
									<input type="password" name="password" value="{{ old('password') }}"
										   class="form-control" id="exampleInputEmail1"
										   placeholder="@lang('New Password')">
									<div class="text-danger">
										@error('password') @lang($message) @enderror
									</div>
								</div>
								<div class="col-12">
									<input type="password" name="password_confirmation"
										   class="form-control" id="exampleInputEmail1"
										   placeholder="@lang('Confirm New Password')">
									<div class="text-danger">
										@error('password_confirmation') @lang($message) @enderror
									</div>
								</div>
							</div>
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Reset Password')</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

