<!-- Header section start -->
<header id="header" class="header fixed-top d-flex align-items-center">
	<div class="d-flex align-items-center justify-content-between">
		<a href="{{route('home')}}" class="logo d-flex align-items-center">
			<img src="{{ getFile(config('basic.default_file_driver'),config('basic.logo_image')) }}"
				 alt="{{config('basic.site_title')}}">

		</a>
		<button onclick="toggleSideMenu()" class="toggle-sidebar-btn d-none d-lg-block"><i
				class="fa-light fa-list"></i></button>
	</div><!-- End Logo -->

	<div class="search-bar">
		<form class="search-form d-flex align-items-center" method="POST" action="#">
			<input type="text" class="form-control global-search" name="query" placeholder="@lang('Search')"
				   title="@lang('Enter search keyword')">
			<button class="search-btn" type="button" title="@lang('Search')"><i
					class="fa-regular fa-magnifying-glass"></i></button>
			<div class="search-backdrop d-none"></div>
			<div class="search-result d-none">
				<div class="search-header">
					@lang('Result')
				</div>
				<div class="content"></div>
			</div>
		</form>
	</div><!-- End Search Bar -->


	<nav class="header-nav ms-auto">
		<ul class="d-flex align-items-center">

			<li class="nav-item pe-3">
				<a id="toggle-btn" class="nav-link d-flex toggle-btn">
					<i class="fa-light fa-moon" id="moon"></i>
					<i class="fa-light fa-sun-bright" id="sun"></i>
				</a>
			</li><!-- End Search Icon-->
			<li class="nav-item d-none d-lg-block d-xl-none">
				<a class="nav-link nav-icon search-bar-toggle" href="#">
					<i class="fa-regular fa-magnifying-glass"></i>
				</a>
			</li><!-- End Search Icon-->

			@include($theme.'partials.pushNotify')

			<li class="nav-item dropdown pe-3">
				<a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
					<img src="{{auth()->user()->profilePicture()}}" alt="@lang(auth()->user()->name)"
						 class="rounded-circle">
					<span class="d-none d-lg-block dropdown-toggle ps-2">@lang(ucfirst(auth()->user()->name))</span>
				</a><!-- End Profile Iamge Icon -->

				<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
					<li class="dropdown-header d-flex justify-content-center text-start">
						<div class="profile-thum">
							<img src="{{auth()->user()->profilePicture()}}" alt="@lang(auth()->user()->name)">
						</div>
						<div class="profile-content">
							<h6>@lang(ucfirst(auth()->user()->name))</h6>
							<span>{{auth()->user()->email}}</span>
						</div>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>

					<li>
						<a class="dropdown-item d-flex align-items-center" href="{{route('user.profile')}}">
							<i class="fa-sharp fa-light fa-gear"></i>
							<span>@lang('Account Settings')</span>
						</a>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>

					<li>
						<a class="dropdown-item d-flex align-items-center" href="{{route('user.twostep.security')}}">
							<i class="fal fa-key"></i>
							<span>@lang('2 FA Security')</span>
						</a>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>

					<li>
						<a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
						   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="fa-regular fa-right-from-bracket"></i>
							<span>@lang('Sign Out')</span>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
								@csrf
							</form>
						</a>
					</li>

				</ul><!-- End Profile Dropdown Items -->
			</li><!-- End Profile Nav -->

		</ul>
	</nav><!-- End Icons Navigation -->

</header>



