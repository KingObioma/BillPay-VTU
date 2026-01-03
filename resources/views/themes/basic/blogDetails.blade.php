@extends($theme.'layouts.app')
@section('title',trans('Blog Details'))

@section('content')
	<!-- Blog details section start -->
	<section class="blog-details-section">
		<div class="container">
			<div class="row g-5">
				<div class="col-xxl-8 col-lg-7 order-2 order-lg-1">
					<div class="blog-details">
						<div class="thum-inner">
							<div class="blog-image">
								<img src="{{ $singleItem['image'] }}" alt="{{ $singleItem['title'] }}">

							</div>
						</div>
						<div class="blog-author">
							<div class="author-img">
								<img src="{{ $singleItem['writer_image'] }}" alt="{{ $singleItem['writer_name'] }}">
							</div>
							<div class="author-info">
								<a href="#">
									<h5>{{ $singleItem['writer_name'] }}</h5>
								</a>
								<span>{{ $singleItem['writer_designation'] }}</span>
							</div>
						</div>
						<div class="blog-header">
							<h3 class="">@lang($singleItem['title'])
							</h3>
						</div>
						<div class="blog-para">
							<p>
								@lang($singleItem['description'])
							</p>
						</div>
					</div>
				</div>
				<div class="col-xxl-4 col-lg-5 order-1 order-lg-2">
					<div class="blog-sidebar">
						<form action="{{ route('blogDetails', $getData->id) }}" method="get">
							<div class="blog-widget-area">
								<div class="search-box">
									<input type="text" class="form-control" name="title" value="{{@request()->title}}"
										   placeholder="@lang('Search here...')">
									<button type="submit" class="search-btn"><i class="far fa-search"></i></button>
								</div>
							</div>
						</form>
						@if (isset($popularContentDetails['blog']))
							<div class="blog-widget-area">
								<div class="widget-title">
									<h4>@lang('Recent Post')</h4>
								</div>
								@foreach ($popularContentDetails['blog']->sortDesc() as $data)
									<a href="{{ route('blogDetails', $data->content_id) }}" class="blog-widget-item">
										<div class="blog-widget-image">
											<img
												src="{{ getFile(optional(optional($data->content)->contentMedia)->driver,optional(optional(optional($data->content)->contentMedia)->description)->image) }}"
												alt="...">
										</div>
										<div class="blog-widget-content">
											<div class="blog-title">@lang($data->description->title)</div>
											<div class="blog-date">
												<i class="fa-regular fa-calendar-days"></i> @lang(dateTime($data->created_at,'d M, Y'))
											</div>
										</div>
									</a>
								@endforeach
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Blog details Section End -->
@endsection
