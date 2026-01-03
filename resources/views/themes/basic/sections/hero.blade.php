<!-- Hero section start -->
@if(isset($templates['hero'][0]) && $hero = $templates['hero'][0])
	<div class="hero-section">
		<div class="container">
			<div class="row g-5 justify-content-between align-items-center">
				<div class="col-lg-6 order-2 order-lg-1">
					<div class="hero-content">
						<h1 class="hero-title">@lang(wordSplice($hero['description']->title,1)['normal']) <span
								class="highlight">@lang(wordSplice($hero['description']->title,1)['highLights'])</span>
						</h1>
						<p class="hero-description">@lang(optional($hero->description)->short_description)</p>
						<div class="btn-area">
							<a href="{{@$hero->templateMedia()->button_link}}"
							   class="cmn-btn">@lang(optional($hero->description)->button_name)</a>
							<a data-fancybox
							   href="{{@$hero->templateMedia()->video_link}}"
							   class="how-it-work-btn">@lang('How it works')?</a>
						</div>
					</div>
				</div>
				<div class="col-xxl-5 col-lg-6 mx-auto order-1 order-lg-2">
					<div class="paybill-category-container">
						<div class="row g-4">
							@foreach($activeServices as $key => $service)
								<div class="col-md-6">
									<a href="{{route('pay.bill.select',$key)}}" class="category-box">
										<div class="icon-area">
											<img src="{{$service['image']}}" alt="{{$service['name']}}">
										</div>
										<p class="title">{{$service['name']}}</p>
									</a>
								</div>

							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif
<!-- Hero section end -->
