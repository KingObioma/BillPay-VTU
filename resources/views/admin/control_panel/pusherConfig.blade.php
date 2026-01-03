@extends('admin.layouts.master')
@section('page_title',__('In App Notification Control'))
@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Pusher Configuration')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('In App Notification Control')</div>
			</div>
		</div>

		<div class="section-body">
			<div class="row mt-sm-4">
				<div class="col-12 col-md-4 col-lg-3">
					@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.in-app-notification'), 'suffix' => ''])
				</div>
				<div class="col-12 col-md-8 col-lg-9">
					<div class="container-fluid" id="container-wrapper">
						<div class="row justify-content-md-center">
							<div class="col-lg-12">
								<div class="card mb-4 card-primary shadow">
									<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
										<h6 class="m-0 font-weight-bold text-primary">@lang('Instructions')</h6>

										<a href="https://www.youtube.com/watch?v=MQszEDuWFeQ" target="_blank" class="btn btn-primary btn-sm  " type="button">
											<span class="btn-label"><i class="fab fa-youtube"></i></span>
											@lang('How to set up it?')
										</a>
									</div>



									<div class="card-body">
										@lang('Pusher Channels provides realtime communication between servers, apps and devices.
										When something happens in your system, it can update web-pages, apps and devices.
										When an event happens on an app, the app can notify all other apps and your system
										<br><br>
										Get your free API keys')
										<a href="https://dashboard.pusher.com/accounts/sign_up"
											target="_blank">@lang('Create an account') <i class="fas fa-external-link-alt"></i></a>
										@lang(', then create a Channels app.
										Go to the "Keys" page for that app, and make a note of your app_id, key, secret and cluster.')
									</div>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="card mb-4 card-primary shadow">
									<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
										<h6 class="m-0 font-weight-bold text-primary">@lang('In App Notification Control')</h6>
									</div>
									<div class="card-body">
										<form action="{{ route('pusher.config') }}" method="post">
											@csrf
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="pusher_app_cluster">@lang('Pusher app cluster')</label>
														<input type="text" name="pusher_app_cluster"
																value="{{ old('pusher_app_cluster', env('PUSHER_APP_CLUSTER')) }}"
																class="form-control @error('pusher_app_cluster') is-invalid @enderror"
																placeholder="@lang('Enter your pusher app cluster')">
														<div class="invalid-feedback">
															@error('pusher_app_cluster') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="pusher_app_id">@lang('Pusher app ID')</label>
														<input type="text" name="pusher_app_id"
																value="{{ old('pusher_app_id',env('PUSHER_APP_ID')) }}"
																class="form-control @error('pusher_app_id') is-invalid @enderror"
																placeholder="@lang('Enter your pusher app id')">
														<div class="invalid-feedback">
															@error('pusher_app_id') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="pusher_app_key">@lang('Pusher app key')</label>
														<input type="text" name="pusher_app_key"
																value="{{ old('pusher_app_key',env('PUSHER_APP_KEY')) }}"
																class="form-control @error('pusher_app_key') is-invalid @enderror"
																placeholder="@lang('Enter your pusher app key')">
														<div class="invalid-feedback">
															@error('pusher_app_key') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="pusher_app_secret">@lang('Pusher app secret')</label>
														<input type="text" name="pusher_app_secret"
																value="{{ old('pusher_app_secret',env('PUSHER_APP_SECRET')) }}"
																class="form-control @error('pusher_app_secret') is-invalid @enderror"
																placeholder="@lang('Enter your pusher app secret')">
														<div class="invalid-feedback">
															@error('pusher_app_secret') @lang($message) @enderror
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>@lang('Push Notification')</label>
														<div class="selectgroup w-100">
															<label class="selectgroup-item">
																<input type="radio" name="push_notification" value="0"
																	   class="selectgroup-input" {{ old('push_notification', $basicControl->push_notification) == 0 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('OFF')</span>
															</label>
															<label class="selectgroup-item">
																<input type="radio" name="push_notification" value="1"
																	   class="selectgroup-input" {{ old('push_notification', $basicControl->push_notification) == 1 ? 'checked' : ''}}>
																<span class="selectgroup-button">@lang('ON')</span>
															</label>
														</div>
														@error('push_notification')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
														@enderror
													</div>
												</div>
											</div>
											<button type="submit" class="btn btn-primary btn-block btn-sm">@lang('Save Changes')</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>
</div>
@endsection
