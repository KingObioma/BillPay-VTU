@extends($theme.'layouts.app')
@section('title',__('Recover Password'))

@section('content')
	<section class="login-signup-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-md-6 order-2 order-md-1">
					<div class="login-signup-form">
						<form action="{{ route('password.email') }}" method="post">
							@csrf
							<div class="section-header">
								<h4>@lang(optional(@$template->description)->title)</h4>
								<div class="description">@lang(optional(@$template->description)->sub_title)</div>
							</div>
							<div class="row g-4">
								<div class="col-12">
									<input type="email" name="email" value="{{ old('email') }}"
										   class="form-control" id="exampleInputEmail1"
										   placeholder="@lang('Email address')">
									<div class="text-danger">
										@error('email') @lang($message) @enderror
									</div>
								</div>
							</div>
							<button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Send Link')</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

