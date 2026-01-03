<!-- Feature section start -->
@if(isset($contentDetails['feature'][0]) && $featureContents = $contentDetails['feature'][0])
	<section class="feature-section">
		<div class="container">
			@if(isset($templates['feature'][0]) && $feature = $templates['feature'][0])
				<div class="row">
					<div class="section-header text-center">
						<div class="section-subtitle">@lang(optional($feature->description)->heading)</div>
						<h2 class="section-title text-center mx-auto">@lang(wordSplice(optional($feature->description)->title,1)['normal'])
							<span
								class="highlight">@lang(wordSplice(optional($feature->description)->title,1)['highLights'])</span>
						</h2>
						<p class="cmn-para-text mx-auto">@lang(optional($feature->description)->short_description)</p>
					</div>
				</div>
			@endif
			<div class="row g-4">
				@if(isset($contentDetails['feature']) && $featureContents = $contentDetails['feature'])
					@foreach($featureContents as $key => $featureContent)
						<div class="col-lg-4 col-sm-6 cmn-box-item">
							<div class="cmn-box">
								<div class="icon-box">
									<i class="{{optional(optional($featureContent->content)->contentMedia)->description->icon}}"></i>
								</div>
								<div class="text-box">
									<h5 class="title">@lang(optional($featureContent->description)->title)</h5>
									<span>@lang(optional($featureContent->description)->short_description)</span>
								</div>
							</div>
						</div>
					@endforeach
				@endif
			</div>
		</div>
	</section>
@endif
<!-- Feature section end -->
