<meta name="description" content="{{ basicControl()->meta_description}}">
<meta name="keywords" content="{{ basicControl()->meta_keywords }}">
<link rel="shortcut icon" href="{{getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}"
	  type="image/x-icon">
{{--<!-- Apple Stuff -->--}}
<link rel="apple-touch-icon" href="{{getFile(config('basic.default_file_driver'),config('basic.logo_image')) }}">
<title>@lang($basic->site_title) | @yield('title')</title>
<link rel="icon" type="image/png" sizes="16x16"
	  href="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image'))}}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="@lang($basic->site_title) | @yield('title')">
{{--<!-- Google / Search Engine Tags -->--}}
<meta itemprop="name" content="@lang($basic->site_title) | @yield('title')">
<meta itemprop="description" content="{{ basicControl()->meta_description}}">
<meta itemprop="image" content="{{ getFile(config('basic.default_file_driver'),config('basic.logo_meta')) }}">
{{--<!-- Facebook Meta Tags -->--}}
<meta property="og:type" content="website">
<meta property="og:title" content="{{ basicControl()->social_title }}">
<meta property="og:description" content="{{  basicControl()->social_description }}">
<meta property="og:image" content="{{ getFile(config('basic.default_file_driver'),config('basic.logo_meta')) }}"/>
<meta property="og:image:type"
	  content="image/{{ pathinfo(getFile(config('basic.default_file_driver'),config('basic.logo_meta')))['extension'] }}"/>
<meta property="og:url" content="{{ url()->current() }}">
{{--<!-- Twitter Meta Tags -->--}}
<meta name="twitter:card" content="summary_large_image">
