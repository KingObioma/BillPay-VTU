<!DOCTYPE html>
<html lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif />
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}" rel="icon">
	<title> @yield('page_title') | {{ basicControl()->site_title }} </title>

	@include($theme.'partials.user.styles')
</head>

<body>

<div id="app" onload="preloder_function()" class="">

	<!-- Preloader section start -->
	<div id="preloader">
		<div class="phone">
			<span class="loader"></span>
			<span class="text">@lang('Loading')...</span>
		</div>
	</div>
	<!-- Preloader section end -->

	@include($theme.'partials.user.topbar')
	@include($theme.'partials.user.mobileNav')
	@include($theme.'partials.user.sidebar')
	<main id="main" class="main">
		<div class="pagetitle">
			<h3 class="mb-1">@yield('page_title')</h3>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{route('home')}}">@lang('Home')</a></li>
					<li class="breadcrumb-item active">@yield('page_title')</li>
				</ol>
			</nav>
		</div>
		@section('content')
		@show
	</main>
	@include($theme.'partials.user.footer')
</div>

@include($theme.'partials.user.scripts')
@include($theme.'partials.user.flash-message')

@yield('scripts')

@include('plugins')
</body>
</html>
<script>
	'use strict'
	 var preloader = document.getElementById("preloader");
	 preloader.style.display = "none";
</script>
