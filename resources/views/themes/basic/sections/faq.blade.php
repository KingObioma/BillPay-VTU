<!-- Faq section start -->
@if(isset($contentDetails['faq']))
	<section class="faq-section">
		<div class="container-fluid">
			<div class="row g-5 align-items-center">
				@if(isset($templates['faq'][0]) && $faq = $templates['faq'][0])
					<div class="col-lg-6 ps-md-0">
						<div class="faq-thum">
							<img src="{{getFile(optional($faq->media)->driver,@$faq->templateMedia()->image)}}"
								 alt="...">
						</div>
					</div>
				@endif
				<div class="col-lg-6">
					<div class="faq-content">
						@if(isset($templates['faq'][0]) && $faq = $templates['faq'][0])
							<div class="section-subtitle">@lang(optional($faq->description)->heading)</div>
							<h2>@lang(wordSplice(optional($faq->description)->title,1)['normal']) <span
									class="highlight">@lang(wordSplice(optional($faq->description)->title,1)['highLights'])</span>
							</h2>
							<p class="cmn-para-text mx-auto">@lang(optional($faq->description)->short_description)
							</p>
						@endif
						<div class="accordion" id="accordionExample2">
							@foreach ( $contentDetails['faq']->take(6) as $key => $item)
								<div class="accordion-item">
									<h2 class="accordion-header" id="headin{{$key}}">
										<button class="accordion-button" type="button" data-bs-toggle="collapse"
												data-bs-target="#collapse{{$key}}" aria-expanded="true"
												aria-controls="collapse{{$key}}">
											@lang(optional($item->description)->title)
										</button>
									</h2>
									<div id="collapse{{$key}}"
										 class="accordion-collapse collapse {{$key == 0 ? 'show':''}}"
										 aria-labelledby="headin{{$key}}" data-bs-parent="#accordionExample2">
										<div class="accordion-body">
											<div class="table-responsive">
												<p>@lang(optional($item->description)->short_description)</p>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endif
<!-- Faq section end -->
