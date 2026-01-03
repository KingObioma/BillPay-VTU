@extends('admin.layouts.login-register')
@section('page_title', __('Admin | Reset Password'))
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

            <div class="card card-primary">
              <div class="card-header"><h4>@lang('Reset Password')</h4></div>

              <div class="card-body">
                <form action="{{ route('admin.password.reset.update') }}" method="post">
					@csrf
					<input type="hidden" name="token" value="{{ $token }}">
					<div class="form-group">
						<label for="email">@lang('Email')</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" tabindex="1" autofocus value="{{ $email ?? old('email') }}" placeholder="@lang('Enter Email')">
						<div class="invalid-feedback">
							@error('email') @lang($message) @enderror
						</div>
					</div>

                  <div class="form-group">
                    <label for="password">@lang('New Password')</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" tabindex="2" placeholder="@lang('Enter password')">
					<div class="invalid-feedback">
						@error('password') @lang($message) @enderror
					</div>
                  </div>

                  <div class="form-group">
                    <label for="password_confirmation">@lang('Confirm Password')</label>
                    <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" tabindex="2" placeholder="@lang('Confirm Password')">
					<div class="invalid-feedback">
						@error('password_confirmation') @lang($message) @enderror
					</div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" tabindex="4">
						@lang('Reset Password')
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
