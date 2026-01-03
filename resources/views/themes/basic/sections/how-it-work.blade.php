<!-- How it works section start -->
@if(isset($contentDetails['how-it-work'][0]) && $how_it_works = $contentDetails['how-it-work'][0])
	<section class="how-it-work">
		<div class="container">
			@if(isset($templates['how-it-work'][0]) && $how_it_work = $templates['how-it-work'][0])
				<div class="row">
					<div class="col-12">
						<div class="section-header text-center mb-50">
							<div class="section-subtitle">@lang(optional($how_it_work->description)->heading)</div>
							<h2 class="section-title mx-auto">@lang(wordSplice(optional($how_it_work->description)->title,2)['normal'])
								<span
									class="highlight">@lang(wordSplice(optional($how_it_work->description)->title,2)['highLights'])</span>
							</h2>
							<p class="cmn-para-text mx-auto">@lang(optional($how_it_work->description)->short_description)
							</p>
						</div>
					</div>
				</div>
			@endif
			@if(isset($contentDetails['how-it-work']) && $how_it_works = $contentDetails['how-it-work'])
				<div class="row g-4 g-xxl-5 align-items-center">
					@foreach($how_it_works as $key => $value)
						<div class="col-md-4 col-sm-6 cmn-box-item">
							<div class="cmn-box">
								<div class="icon-box">
									<i class="{{optional(optional($value->content)->contentMedia)->description->icon}}"></i>
									<div class="number">{{++$key}}</div>
								</div>
								<div class="text-box">
									<h5>@lang(optional($value->description)->title)</h5>
									<span>@lang(optional($value->description)->short_description)</span>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			@endif
		</div>
	</section>
@endif
<!-- How it works section start -->
