<!-- Bottom Mobile Tab nav section start -->
<ul class="nav bottom-nav fixed-bottom d-lg-none">
	<li class="nav-item">
		<a onclick="toggleSideMenu()" class="nav-link toggle-sidebar" aria-current="page"><i
				class="fa-light fa-list"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('user.transaction')}}" href="{{route('user.transaction')}}"><i
				class="fa-regular fa-exchange-alt"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('user.dashboard')}}" href="{{route('user.dashboard')}}"><i
				class="fa-regular fa-house"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('pay.bill')}}" href="{{route('pay.bill')}}"><i
				class="fa-regular fa-paper-plane"></i></a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{menuActive('user.profile')}}" href="{{route('user.profile')}}"><i
				class="fa-light fa-user"></i></a>
	</li>
</ul>
<!-- Bottom Mobile Tab nav section end -->
