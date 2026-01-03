@extends('admin.layouts.master')
@section('page_title', __('Socialite Configuration'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Socialite Configuration')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('settings') }}">@lang('Settings')</a>
					</div>
					<div class="breadcrumb-item">@lang('Socialite Configuration')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="row justify-content-md-center">
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Socialite Configuration')</h6>
										</div>
										<div class="card-body py-5">
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-3"><img
																		src="{{ asset('assets/upload/google.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Socialite login your customers,they\'ll love you for it')</div>
																<div class="col-md-3"><a
																		href="{{ route('google.control') }}"
																		class="btn btn-sm btn-primary"
																		target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-3"><img
																		src="{{ asset('assets/upload/facebook.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Socialite login your customers,they\'ll love you for it')</div>
																<div class="col-md-3"><a
																		href="{{ route('facebook.control') }}"
																		class="btn btn-sm btn-primary"
																		target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row justify-content-md-center">
												<div class="col-lg-10">
													<div class="card mb-4 shadow">
														<div class="card-body">
															<div class="row justify-content-between align-items-center">
																<div class="col-md-3"><img
																		src="{{ asset('assets/upload/github.png') }}"
																		class="w-25"></div>
																<div
																	class="col-md-6">@lang('Socialite login your customers,they\'ll love you for it')</div>
																<div class="col-md-3"><a
																		href="{{ route('github.control') }}"
																		class="btn btn-sm btn-primary"
																		target="_blank">@lang('Configuration')</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
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

