@extends($theme.'layouts.error')
@section('title','404')
@section('content')
	<section class="error-section">
		<div class="container">
			<div class="row g-5 align-items-center">
				<div class="col-sm-6">
					<div class="error-thum">
						<img src="{{ asset($themeTrue . 'images/error/error3.png')}}" alt="">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="error-content">
						<div class="error-title">@lang('404')</div>
						<div class="error-info">{{trans('Page not found')}}</div>
						<div class="btn-area">
							<a href="{{url('/')}}" class="cmn-btn">@lang('Back to Homepage')</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
