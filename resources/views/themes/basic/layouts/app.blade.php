<!DOCTYPE html>
<html lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif />
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{config('basic.site_title')}}</title>

	@include('partials.seo')

	<link rel="shortcut icon" href="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}"
		  type="image/x-icon">

	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/all.min.css')}}"/>
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/fontawesome.min.css')}}"/>
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/bootstrap.min.css')}}"/>

	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/owl.carousel.min.css')}}"/>
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/owl.theme.default.min.css')}}"/>

	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/slick.css')}}">
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/slick-theme.css')}}">
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/select2.min.css')}}">
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/nouislider.min.css')}}">
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/fancybox.css')}}">

	<!-- Style_Css_link -->
	<link rel="stylesheet" href="{{ asset($themeTrue . 'css/style.css')}}"/>
	@stack('css-lib')
	@stack('style')

</head>
<body onload="preloder_function()" class="">

<!-- Preloader section start -->
<div id="preloader">
	<div class="phone">
		<span class="loader"></span>
		<span class="text">@lang('Loading')...</span>
	</div>
</div>
<!-- Preloader section end -->

@include($theme.'partials.nav')
@include($theme.'partials.mobileNav')
@include($theme.'partials.banner')

@yield('content')

@include($theme.'partials.footer')

<script src="{{ asset($themeTrue . 'js/bootstrap.bundle.min.js')}}"></script>

<script src="{{ asset($themeTrue . 'js/jquery-3.6.1.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/owl.carousel.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/swiper-bundle.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/slick.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/select2.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/nouislider.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/fancybox.umd.js')}}"></script>

@stack('extra-js')
<script src="{{ asset($themeTrue . 'js/main.js')}}"></script>

<script src="{{asset('assets/global/js/notiflix-aio-2.7.0.min.js')}}"></script>
<script src="{{asset('assets/global/js/pusher.min.js')}}"></script>
<script src="{{asset('assets/global/js/vue.min.js')}}"></script>
<script src="{{asset('assets/global/js/axios.min.js')}}"></script>
@yield('scripts')

@stack('script')
@include($theme.'partials.notification')

@include('plugins')
</body>
</html>
