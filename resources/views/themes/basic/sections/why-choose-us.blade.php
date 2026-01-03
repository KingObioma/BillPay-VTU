<!-- Why choose us section start -->
@if(isset($contentDetails['why-choose-us'][0]) && $why_choose_uss = $contentDetails['why-choose-us'][0])
	<section class="why-choose-us">
		<div class="container-fluid">
			<div class="row g-5 align-items-center">
				<div class="col-lg-6 order-2 order-md-1">
					<div class="why-choose-us-content">
						@if(isset($templates['why-choose-us'][0]) && $why_choose_us = $templates['why-choose-us'][0])
							<div class="section-subtitle">@lang(optional($why_choose_us->description)->heading)</div>
							<h2 class="section-title">@lang(wordSplice(optional($why_choose_us->description)->title,2)['normal'])
								<span
									class="highlight">@lang(wordSplice(optional($why_choose_us->description)->title,2)['highLights'])</span>
							</h2>
						@endif
						@if(isset($contentDetails['why-choose-us']) && $why_choose_uss = $contentDetails['why-choose-us'])
							<ul class="choose-us-list">
								@foreach($why_choose_uss as $key => $value)
									<li class="item">
										<div class="content-area">
											<div class="item-title">
												<i class="fa-sharp fa-light fa-circle-check"></i>
												<h6>@lang(optional($value->description)->title)</h6>
											</div>
											<p>@lang(optional($value->description)->short_description)</p>
										</div>
									</li>
								@endforeach
							</ul>
						@endif
					</div>
				</div>
				<div class="col-lg-6 order-1 order-md-2 pe-md-0">
					<div class="why-choose-us-thum">
						<img
							src="{{getFile(optional($why_choose_us->media)->driver,@$why_choose_us->templateMedia()->image)}}"
							alt="...">
					</div>
				</div>
			</div>
		</div>
	</section>
@endif
<!-- Why choose us section end -->
