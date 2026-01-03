<link rel="shortcut icon" href="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}"
	  type="image/x-icon">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/all.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/fontawesome.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/owl.theme.default.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/swiper-bundle.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset($themeTrue . 'css/intlTelInput.min.css')}}"/>

<link rel="stylesheet" href="{{ asset($themeTrue . 'css/dashboard.css')}}">

@stack('extra_styles')
