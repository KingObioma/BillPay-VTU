@extends($theme.'layouts.app')
@section('title')
	@lang($title)
@endsection

@section('content')
	<section class="policy-section">
		<div class="container">
			<div class="row">
				<div class="policy wow fadeInUp">
					@lang(@$description)
					<div class="shape">
						<svg id="sw-js-blob-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">                    <defs>                         <linearGradient id="sw-gradient" x1="0" x2="1" y1="1" y2="0">                            <stop id="stop1" stop-color="rgba(125.317, 90.658, 219.781, 1)" offset="0%"></stop>                            <stop id="stop2" stop-color="rgba(243, 238, 255, 1)" offset="100%"></stop>                        </linearGradient>                    </defs>                <path fill="url(#sw-gradient)" d="M21,-24.1C28,-19.3,34.8,-13.3,35.7,-6.5C36.7,0.3,31.8,7.9,27.2,15.5C22.6,23.2,18.3,30.9,12,33.2C5.7,35.6,-2.6,32.7,-10.7,29.7C-18.8,26.6,-26.7,23.3,-30.1,17.4C-33.4,11.5,-32.3,3,-31.5,-6.3C-30.7,-15.5,-30.3,-25.5,-25.1,-30.7C-19.9,-35.9,-9.9,-36.3,-1.4,-34.6C7.1,-32.9,14.1,-29,21,-24.1Z" width="100%" height="100%" transform="translate(50 50)" stroke-width="0" style="transition: all 0.3s ease 0s;" stroke="url(#sw-gradient)"></path>  </svg>
					</div>
					<div class="shape2">
						<svg id="sw-js-blob-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">                    <defs>                         <linearGradient id="sw-gradient" x1="0" x2="1" y1="1" y2="0">                            <stop id="stop1" stop-color="rgba(125.317, 90.658, 219.781, 1)" offset="0%"></stop>                            <stop id="stop2" stop-color="rgba(243, 238, 255, 1)" offset="100%"></stop>                        </linearGradient>                    </defs>                <path fill="url(#sw-gradient)" d="M21,-24.1C28,-19.3,34.8,-13.3,35.7,-6.5C36.7,0.3,31.8,7.9,27.2,15.5C22.6,23.2,18.3,30.9,12,33.2C5.7,35.6,-2.6,32.7,-10.7,29.7C-18.8,26.6,-26.7,23.3,-30.1,17.4C-33.4,11.5,-32.3,3,-31.5,-6.3C-30.7,-15.5,-30.3,-25.5,-25.1,-30.7C-19.9,-35.9,-9.9,-36.3,-1.4,-34.6C7.1,-32.9,14.1,-29,21,-24.1Z" width="100%" height="100%" transform="translate(50 50)" stroke-width="0" style="transition: all 0.3s ease 0s;" stroke="url(#sw-gradient)"></path>  </svg>
					</div>
				</div>
			</div>

		</div>


	</section>
@endsection
