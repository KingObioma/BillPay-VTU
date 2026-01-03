@extends($theme.'layouts.app')
@section('title',__('2 Step Verification'))
@section('content')
	<!-- login-signup section start -->
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-6 order-2 order-md-1">
					<div class="login-signup-form">
						<form action="{{ route('user.twoFA-Verify') }}" method="post">
							@csrf
							<div class="section-header">
								<h4>@lang('Verification here!')</h4>
								<div class="description">@lang('Hey Enter your code to verify you')</div>
							</div>
							<div class="row g-4">
								<div class="col-12">
									<input type="text" name="code" value="{{ old('code') }}"
										   class="form-control" id="exampleInputEmail1"
										   placeholder="@lang('2 FA Code')">
									<div class="text-danger">
										@error('code') @lang($message) @enderror
										@error('error') @lang($message) @enderror
									</div>
								</div>
							</div>
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Submit')</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- login-signup section end -->
@endsection
