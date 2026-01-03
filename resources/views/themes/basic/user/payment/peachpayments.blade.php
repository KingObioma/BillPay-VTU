@extends($theme.'layouts.user')
@section('page_title')
	{{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection
@section('content')
	<section class="section">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card card-primary shadow">
					<div class="card-body">
						<div class="row justify-content-center">
							<div class="col-md-3">
								<img
									src="{{getFile(optional($deposit->gateway)->driver,optional($deposit->gateway)->image)}}"
									class="card-img-top gateway-img">
							</div>
							<div class="col-md-6">
								<form action="{{$data->url}}" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId={{$data->checkoutId}}"></script>
@endsection
