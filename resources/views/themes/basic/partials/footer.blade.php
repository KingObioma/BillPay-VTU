<!-- Footer Section start -->
<section class="footer-section pb-50">
	<div class="container">
		<div class="row gy-4 gy-sm-5">
			<div class="col-lg-4 col-sm-6">
				<div class="footer-widget">
					<div class="widget-logo mb-30">
						<a href="{{route('home')}}"><img class="logo"
														 src="{{ getFile(config('basic.default_file_driver'),config('basic.footer_image')) }}"
														 alt="{{config('basic.site_title')}}"></a>
					</div>
					@if($contactUs)
						<p>{{optional($contactUs->description)->about_company}}
						</p>
					@endif
					@if(isset($contentDetails['social']))
						<div class="social-area mt-50">
							<ul class="d-flex">
								@foreach($contentDetails['social'] as $data)
									<li>
										<a href="{{optional(optional(optional($data->content)->contentMedia)->description)->social_link}}"><i
												class="{{optional(optional(optional($data->content)->contentMedia)->description)->social_icon}}"></i></a>
									</li>
								@endforeach
							</ul>
						</div>
					@endif
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="footer-widget">
					<h5 class="widget-title">@lang('Quick Links')</h5>
					<ul>
						<li><a class="widget-link" href="{{route('home')}}">@lang('Home')</a></li>
						<li><a class="widget-link" href="{{route('about')}}">@lang('About')</a></li>
						<li><a class="widget-link" href="{{route('blog')}}">@lang('Blog')</a></li>
						<li><a class="widget-link" href="{{route('contact')}}">@lang('Contact')</a></li>
					</ul>
				</div>
			</div>
			@if(isset($contentDetails['pages']))
				<div class="col-lg-3 col-sm-6 pt-sm-0 pt-3 ps-lg-5">
					<div class="footer-widget">
						<h5 class="widget-title">@lang('Company Policy')</h5>
						<ul>
							@foreach($contentDetails['pages'] as $data)
								<li><a class="widget-link"
									   href="{{route('getLink', [slug($data->description->title),$data->content_id])}}">@lang(optional($data->description)->title)</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			@endif
			@if($contactUs)
				<div class="col-lg-3 col-sm-6 pt-sm-0 pt-3">
					<div class="footer-widget">
						<h5 class="widget-title">@lang('Contact Us')</h5>
						<p class="contact-item"><i
								class="fa-regular fa-location-dot"></i> {{optional($contactUs->description)->location}}
						</p>
						<p class="contact-item"><i
								class="fa-regular fa-envelope"></i> {{optional($contactUs->description)->email}}</p>
						<p class="contact-item"><i
								class="fa-regular fa-phone"></i> {{optional($contactUs->description)->phone}}</p>
					</div>
				</div>
			@endif
		</div>
		<hr class="cmn-hr">
		<!-- Copyright-area-start -->
		<div class="copyright-area">
			<div class="row gy-4">
				<div class="col-sm-6">
					<p>@lang('Copyright') ©{{date('Y')}} <a class="highlight"
															href="javascript:void(0)">@lang($basic->site_title)</a> @lang('All Rights Reserved')
					</p>
				</div>
				@if(isset($languages))
					<div class="col-sm-6">
						<div class="language">
							@foreach($languages as $item)
								<a href="{{route('language',$item->short_name)}}"
								   class="language">@lang($item->name)</a>
							@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>
		<!-- Copyright-area-end -->
	</div>
</section>
<!-- Footer Section end -->
