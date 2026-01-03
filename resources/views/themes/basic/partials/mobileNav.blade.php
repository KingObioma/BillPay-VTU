<!-- Bottom Mobile Tab nav section start -->
<ul class="nav bottom-nav fixed-bottom d-lg-none">
	<li class="nav-item">
		<a class="nav-link" data-bs-toggle="offcanvas" role="button" aria-controls="offcanvasNavbar"
		   href="#offcanvasNavbar" aria-current="page"><i class="fa-light fa-list"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#"><i class="fa-light fa-planet-ringed"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('home')}}" href="{{route('home')}}"><i class="fa-light fa-house"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('contact')}}" href="{{route('contact')}}"><i
				class="fa-light fa-address-book"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('login')}}" href="{{route('login')}}"><i class="fa-light fa-user"></i></a>
	</li>
</ul>
<!-- Bottom Mobile Tab nav section end -->
