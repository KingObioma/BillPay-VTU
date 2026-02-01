@extends($theme.'layouts.app')
@section('title', trans($title))

@section('content')

	<!-- Blog Section start -->
	@if(isset($blogContents))
		<section class="blog-section">
			<div class="container">
				@if(isset($templates['blog'][0]) && $blog = $templates['blog'][0])
					<div class="row">
						<div class="col-12">
							<div class="section-header text-center mb-50">
								<div class="section-subtitle">@lang(optional($blog->description)->heading)</div>
								<h2 class="section-title mx-auto">@lang(wordSplice(optional($blog->description)->title,1)['normal'])
									<span
										class="highlight">@lang(wordSplice(optional($blog->description)->title,1)['highLights'])</span>
								</h2>
								<h2 class="section-title mx-auto">@lang(wordSplice(optional($blog->description)->title,1)['normal'])
									
								</h2>
								<p class="cmn-para-text mx-auto">@lang(optional($blog->description)->short_description)</p>
							</div>
						</div>
					</div>
				@endif
				<div class="row g-4">
					@foreach($blogContents as $blogContent)
						<div class="col-lg-4 col-md-6">
							<div class="blog-box">
								<div class="img-box">
									<a href="{{ route('blogDetails', $blogContent->content_id) }}">
										<img
											src="{{getFile(optional(optional($blogContent->content)->contentMedia)->driver,optional(optional(optional($blogContent->content)->contentMedia)->description)->image)}}"
											alt="...">
									</a>

								</div>
								<div class="content-box">
									<div class="date">
										<p class="mb-0">{{dateTime($blogContent->created_at,'M')}}</p>
										<h4 class="mb-0">{{dateTime($blogContent->created_at,'d')}}</h4>
									</div>
									<div class="blog-title">
										<h5>
											<a href="{{ route('blogDetails', $blogContent->content_id) }}">{{optional($blogContent->description)->title}}
											</a></h5>
									</div>
									<div class="para-text">
										<p>@lang(Str::limit(optional($blogContent->description)->description,200))</p>
									</div>
									<a href="{{ route('blogDetails', $blogContent->content_id) }}"
									   class="blog-btn">@lang('Read more')</a>
								</div>

							</div>
						</div>
					@endforeach
				</div>
				<div class="pagination-section">
					{{ $blogContents->appends($_GET)->links($theme.'partials.pagination') }}
				</div>
			</div>
		</section>
	@endif
	<!-- Blog Section end -->

@endsection
