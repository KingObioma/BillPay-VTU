@extends($theme.'layouts.app')
@section('title',__('Confirm Password'))

@section('content')
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-6 order-2 order-md-1">
					<div class="login-signup-form">
						<form action="{{ route('password.confirm') }}" method="post">
							@csrf
							<div class="section-header">
								<h4>@lang(optional(@$template->description)->title)</h4>
								<div class="description">@lang(optional(@$template->description)->sub_title)</div>
							</div>
							<div class="row g-4">
								<div class="col-12">
									<input type="password" name="password" value="{{ old('identity') }}"
										   class="form-control" id="exampleInputEmail1"
										   autocomplete="current-password" required>
									<div class="text-danger">
										@error('password') @lang($message) @enderror
									</div>
								</div>
							</div>
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Confirm Password')</button>
							@if (Route::has('password.request'))
								<hr class="divider">
								<div class="pt-20 text-center">
									<p class="mb-0 highlight"><a
											href="{{ route('password.request') }}">@lang('Forgot Your Password')?</a>
									</p>
								</div>
							@endif
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

