
@if(!request()->routeIs('home'))
	<!-- Banner section start -->
	<div class="banner-area">
		<div class="container">
			<div class="row ">
				<div class="col">
					<div class="breadcrumb-area">
						<h3>@yield('title')</h3>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{route('home')}}"><i
										class="fa-light fa-house"></i> @lang('Home')</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Banner section end -->
@endif
