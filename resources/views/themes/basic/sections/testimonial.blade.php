<!-- Testimonial section start -->
@if(isset($templates['testimonial'][0]) && $testimonial = $templates['testimonial'][0])
	<section class="testimonial-section">
		<div class="container">
			<div class="row">
				<div class="section-header mb-50 text-center">
					<div class="section-subtitle">@lang(optional($testimonial->description)->heading)</div>
					<h2 class="">@lang(wordSplice(optional($testimonial->description)->title,1)['normal']) <span
							class="highlight">@lang(wordSplice(optional($testimonial->description)->title,1)['highLights'])</span>
					</h2>
					<p class="cmn-para-text m-auto">@lang(optional($testimonial->description)->short_description) </p>
				</div>
			</div>
			@if(isset($contentDetails['testimonial']) && $testimonials = $contentDetails['testimonial'])
				<div class="row">
					<div class="owl-carousel owl-theme testimonial-carousel">
						@foreach($testimonials->sortBy('desc') as $key => $testimonial)
							<div class="item">
								<div class="testimonial-box">
									<div class="testimonial-header">
										<div class="testimonial-title-area">
											<div class="testimonial-thumbs">
												<img
													src="{{getFile(optional(optional($testimonial->content)->contentMedia)->driver,optional(optional(optional($testimonial->content)->contentMedia)->description)->image)}}"
													alt="...">
											</div>
											<div class="testimonial-title">
												<h5>@lang(optional($testimonial->description)->name)</h5>
												<h6>@lang(optional($testimonial->description)->address)</h6>
											</div>
										</div>
										<div class="qoute-icon">
											<i class="fa-sharp fa-regular fa-quote-left"></i>
										</div>
									</div>
									<div class="quote-area">
										<p>@lang(optional($testimonial->description)->short_description)</p>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			@endif
		</div>
	</section>
@endif
<!-- Testimonial section end -->
