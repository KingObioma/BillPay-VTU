<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}" rel="icon">
    <title> @yield('page_title') | @lang('Admin') </title>
    @include('admin.layouts.styles')
</head>

<body>
	<div id="app">
		<div class="main-wrapper main-wrapper-1">

			@include('admin.layouts.topbar')
			@include('admin.layouts.sidebar')
			@section('content')
			@show
			@include('admin.layouts.footer')

		</div>
	</div>

	@include('admin.layouts.scripts')
	@include('admin.layouts.flash-message')
	@yield('scripts')
</body>
</html>
