@extends('admin.layouts.master')
@section('page_title', __(getTitle($settings)))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>{{ __(getTitle($settings)) }}</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>

					@if( collect(request()->segments())->last()  != 'settings')
						<div class="breadcrumb-item active">
							<a href="{{ route('settings') }}">@lang('Settings')</a>
						</div>
					@endif
					<div class="breadcrumb-item">{{ __(getTitle($settings)) }}</div>
				</div>
			</div>

			<div class="section-body mx-2 settings">
				<div class="row mt-sm-4">
					@foreach($settingsDetails as $key => $detail)
						@if(isset($detail['route']))
							<div class="col-lg-6">
								<div class="card card-large-icons shadow">
									<div class="card-icon bg-primary text-white">
										<i class="{{ $detail['icon'] ?? '' }}"></i>
									</div>
									<div class="card-body">
										<h4>{{ __(getTitle($key)) }}</h4>
										<p>{{ __($detail['short_description'] ?? '') }}</p>
										<a href="{{ getRoute($detail['route'], $detail['route_segment'] ?? null) }}"
										   class="card-cta">@lang('Change Setting')<i class="fas fa-chevron-right"></i></a>
									</div>
								</div>
							</div>
						@endif
					@endforeach
				</div>
			</div>

		</section>
	</div>
@endsection
