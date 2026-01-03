@extends('admin.layouts.master')
@section('page_title',__('Push Notification Control'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Push Notification Configuration')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Push Notification Control')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.push-notification'), 'suffix' => ''])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="row justify-content-md-center">
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Instructions')</h6>

											<a href="https://www.youtube.com/watch?v=F_l69SMj6XU" target="_blank"
											   class="btn btn-primary btn-sm  " type="button">
												<span class="btn-label"><i class="fab fa-youtube"></i></span>
												@lang('How to set up it?')
											</a>
										</div>


										<div class="card-body">
											@lang('Push notification provides realtime communication between servers, apps and devices.
                                            When something happens in your system, it can update web-pages, apps and devices.
                                            When an event happens on an app, the app can notify all other apps and your system
											<br><br>
											Get your free API keys')
											<a href="https://console.firebase.google.com/"
											   target="_blank">@lang('Create an account') <i
													class="fas fa-external-link-alt"></i></a>
											@lang(', then create a Firebase Project, then create a web app in created Project
                                                   Go to web app configuration details to get Vapid key, Api key, Auth domain, Project id, Storage bucket, Messaging sender id, App id, Measurement id.')
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('In App Notification Control')</h6>
										</div>
										<div class="card-body">
											<form action="{{ route('firebase.config') }}" method="post">
												@csrf
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="server_key">@lang('Server Key')</label>
															<input type="text" name="server_key"
																   value="{{ old('server_key', $control->server_key) }}"
																   class="form-control @error('server_key') is-invalid @enderror"
																   placeholder="@lang('Enter your server key')">
															<div class="invalid-feedback">
																@error('server_key') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="vapid_key">@lang('Vapid Key')</label>
															<input type="text" name="vapid_key"
																   value="{{ old('vapid_key',$control->vapid_key) }}"
																   class="form-control @error('vapid_key') is-invalid @enderror"
																   placeholder="@lang('Enter your vapid key')">
															<div class="invalid-feedback">
																@error('vapid_key') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="api_key">@lang('Api Key')</label>
															<input type="text" name="api_key"
																   value="{{ old('api_key',$control->api_key) }}"
																   class="form-control @error('api_key') is-invalid @enderror"
																   placeholder="@lang('Enter your api key')">
															<div class="invalid-feedback">
																@error('api_key') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="auth_domain">@lang('Auth Domain')</label>
															<input type="text" name="auth_domain"
																   value="{{ old('auth_domain',$control->auth_domain) }}"
																   class="form-control @error('auth_domain') is-invalid @enderror"
																   placeholder="@lang('Enter your app secret')">
															<div class="invalid-feedback">
																@error('auth_domain') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="project_id">@lang('Project Id')</label>
															<input type="text" name="project_id"
																   value="{{ old('project_id',$control->project_id) }}"
																   class="form-control @error('project_id') is-invalid @enderror"
																   placeholder="@lang('Enter your project id')">
															<div class="invalid-feedback">
																@error('project_id') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="storage_bucket">@lang('Storage Bucket')</label>
															<input type="text" name="storage_bucket"
																   value="{{ old('storage_bucket',$control->storage_bucket) }}"
																   class="form-control @error('storage_bucket') is-invalid @enderror"
																   placeholder="@lang('Enter your storage bucket')">
															<div class="invalid-feedback">
																@error('storage_bucket') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="messaging_sender_id">@lang('Messaging Sender Id')</label>
															<input type="text" name="messaging_sender_id"
																   value="{{ old('messaging_sender_id',$control->messaging_sender_id) }}"
																   class="form-control @error('messaging_sender_id') is-invalid @enderror"
																   placeholder="@lang('Enter your messaging sender id')">
															<div class="invalid-feedback">
																@error('messaging_sender_id') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="app_id">@lang('App Id')</label>
															<input type="text" name="app_id"
																   value="{{ old('app_id',$control->app_id) }}"
																   class="form-control @error('app_id') is-invalid @enderror"
																   placeholder="@lang('Enter your app id')">
															<div class="invalid-feedback">
																@error('app_id') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label
																for="measurement_id">@lang('Measurement Id')</label>
															<input type="text" name="measurement_id"
																   value="{{ old('measurement_id',$control->measurement_id) }}"
																   class="form-control @error('measurement_id') is-invalid @enderror"
																   placeholder="@lang('Enter your measurement id')">
															<div class="invalid-feedback">
																@error('measurement_id') @lang($message) @enderror
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>@lang('User Foreground')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="user_foreground"
																		   value="0"
																		   class="selectgroup-input" {{ old('user_foreground', $control->user_foreground) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="user_foreground"
																		   value="1"
																		   class="selectgroup-input" {{ old('user_foreground', $control->user_foreground) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('user_foreground')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>@lang('User Background')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="user_background"
																		   value="0"
																		   class="selectgroup-input" {{ old('user_background', $control->user_background) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="user_background"
																		   value="1"
																		   class="selectgroup-input" {{ old('user_background', $control->user_background) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('user_background')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>@lang('Admin Foreground')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="admin_foreground"
																		   value="0"
																		   class="selectgroup-input" {{ old('admin_foreground', $control->admin_foreground) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="admin_foreground"
																		   value="1"
																		   class="selectgroup-input" {{ old('admin_foreground', $control->admin_foreground) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('admin_foreground')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>@lang('Admin Background')</label>
															<div class="selectgroup w-100">
																<label class="selectgroup-item">
																	<input type="radio" name="admin_background"
																		   value="0"
																		   class="selectgroup-input" {{ old('admin_background', $control->admin_background) == 0 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('OFF')</span>
																</label>
																<label class="selectgroup-item">
																	<input type="radio" name="admin_background"
																		   value="1"
																		   class="selectgroup-input" {{ old('admin_background', $control->admin_background) == 1 ? 'checked' : ''}}>
																	<span class="selectgroup-button">@lang('ON')</span>
																</label>
															</div>
															@error('admin_background')
															<span class="text-danger" role="alert">
																<strong>{{ __($message) }}</strong>
															</span>
															@enderror
														</div>
													</div>
												</div>
												<button type="submit"
														class="btn btn-primary btn-block btn-sm">@lang('Save Changes')</button>
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
