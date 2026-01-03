<!-- App section start -->
<section class="app-section">
	<div class="container">
		<div class="row g-4 g-lg-5 align-items-center">
			@if(isset($templates['app-section'][0]) && $app = $templates['app-section'][0])
				<div class="col-lg-6">
					<div class="app-content">
						<h3 class="mb-0">@lang(optional($app->description)->title)
						</h3>
					</div>
				</div>
			@endif
			@if(isset($contentDetails['app-section']) && $appSectionContents = $contentDetails['app-section'])
				<div class="col-lg-6 d-flex justify-content-center justify-lg-content-end">
					<div class="app-btn-area">
						@foreach($appSectionContents as $key => $appSectionContent)
							<a href="{{optional(optional($appSectionContent->content)->contentMedia)->description->button_link}}"
							   class="app-btn">
								<div class="icon-area">
									<i class="{{optional(optional($appSectionContent->content)->contentMedia)->description->icon}}"></i>
								</div>
								<div class="content-area">
									<p class="mb-0">@lang('Download on')</p>
									<h5 class="mb-0">@lang(optional($appSectionContent->description)->title)</h5>
								</div>
							</a>
						@endforeach
					</div>
				</div>
			@endif
		</div>
	</div>
</section>
<!-- App section end -->
