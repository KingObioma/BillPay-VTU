@extends('admin.layouts.login-register')
@section('page_title', __('Admin | Forget Password'))

@section('content')
	<div id="app">
		<section class="section">
			<div class="container mt-5">
				<div class="row">
				<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
					<div class="login-brand">
						<a href="{{ route('home') }}">
							<img src="{{ getFile(config('location.logo.path').'logo.png') }}" class="dashboard-logo" alt="@lang('Logo')">
						</a>
					</div>

					@if (session('status'))
						<div class="alert alert-success" role="alert">
							{{ __(session('status')) }}
						</div>
					@endif

					<div class="card card-primary shadow">
						<div class="card-header"><h4>@lang('Forgot Password')</h4></div>

						<div class="card-body">
							<p class="text-muted">@lang('We will send a link to reset your password')</p>
							<form action="{{ route('admin.password.email') }}" method="post">
								@csrf
								<div class="form-group">
									<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" tabindex="1" autofocus value="{{ old('email') }}" placeholder="@lang('Enter Email Address')">
									<div class="invalid-feedback">
										@error('email') @lang($message) @enderror
									</div>
									<div class="valid-feedback"></div>
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary btn-block" tabindex="4">
										@lang('Send Password Reset Link')
									</button>
								</div>
							</form>
						</div>
					</div>
					<div class="simple-footer">
						@lang('Copyright') &copy; <b>{{ __(basicControl()->site_title) }}</b> {{ __(date('Y')) }}
					</div>
				</div>
				</div>
			</div>
		</section>
	</div>
@endsection
