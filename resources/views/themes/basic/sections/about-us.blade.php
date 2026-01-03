<!-- About section start -->
@if(isset($templates['about-us'][0]) && $aboutUs = $templates['about-us'][0])
	<section class="about-section">
		<div class="container-fluid">
			<div class="row g-5 align-items-center">
				<div class="col-lg-6 ps-md-0">
					<div class="about-thumb">
						<img src="{{getFile(optional($aboutUs->media)->driver,@$aboutUs->templateMedia()->image)}}"
							 alt="...">
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-content">
						<div class="section-subtitle">@lang(optional($aboutUs->description)->heading)</div>
						<h2 class="section-title">@lang(wordSplice(optional($aboutUs->description)->title,2)['normal'])
							<span
								class="highlight">@lang(wordSplice(optional($aboutUs->description)->title,2)['highLights'])</span>
						</h2>
						<p class="cmn-para-text">@lang(optional($aboutUs->description)->short_description)</p>
						<div class="btn-area">
							<a href="{{@$aboutUs->templateMedia()->button_link}}"
							   class="cmn-btn">@lang(optional($aboutUs->description)->button_name)</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endif
<!-- About section end -->

