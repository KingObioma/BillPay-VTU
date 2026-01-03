<!-- Nav section start -->
<nav class="navbar fixed-top navbar-expand-lg">
	<div class="container">
		<a class="navbar-brand logo" href="{{route('home')}}"><img
				src="{{ getFile(config('basic.default_file_driver'),config('basic.logo_image')) }}"
				alt="{{config('basic.site_title')}}"></a>
		<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
				aria-controls="offcanvasNavbar">
			<i class="fa-light fa-list"></i>
		</button>
		<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbar">
			<div class="offcanvas-header">
				<a class="navbar-brand" href="{{route('home')}}"><img class="logo"
																	  src="{{ getFile(config('basic.default_file_driver'),config('basic.logo_image')) }}"
																	  alt="{{config('basic.site_title')}}"></a>
				<button type="button" class="cmn-btn-close btn-close" data-bs-dismiss="offcanvas"
						aria-label="Close"><i class="fa-light fa-arrow-right"></i></button>
			</div>
			<div class="offcanvas-body align-items-center justify-content-between">
				<ul class="navbar-nav m-auto">
					<li class="nav-item">
						<a class="nav-link {{menuActive('home')}}" aria-current="page"
						   href="{{route('home')}}">@lang('home')</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{menuActive('about')}}" href="{{route('about')}}">@lang('About')</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{menuActive('features')}}"
						   href="{{route('features')}}">@lang('features')</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{menuActive('blog')}}" href="{{route('blog')}}">@lang('blog')</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{menuActive('faq')}}" href="{{route('faq')}}">@lang('FAQ')</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{menuActive('contact')}}" href="{{route('contact')}}">@lang('Contact')</a>
					</li>
				</ul>
				<form>
					<ul class="navbar-nav nav-right d-flex align-items-center">
						@guest
							<li class="nav-item">
								<a class="nav-link" href="{{route('login')}}"><i
										class="login-icon fa-regular fa-right-to-bracket"></i>@lang('Login')</a>
							</li>
						@endguest
						@auth
							<li class="nav-item">
								<a class="get-start-btn" href="{{route('user.dashboard')}}">@lang('Dashboard')</a>
							</li>
						@endauth
						<li>
							<a id="toggle-btn" class="nav-link d-flex toggle-btn">
								<i class="fa-regular fa-moon" id="moon"></i>
								<i class="fa-regular fa-sun-bright" id="sun"></i>
							</a>
						</li>
					</ul>
				</form>

			</div>
		</div>
	</div>
</nav>
<!-- Nav section end -->
