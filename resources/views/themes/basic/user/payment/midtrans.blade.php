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
									class="card-img-top gateway-img br-4" alt="..">
							</div>

							<div class="col-md-9">
								<h4>@lang('Please Pay') {{getAmount($deposit->payable_amount)}} {{$deposit->payment_method_currency}}</h4>
								<button type="button"
										class="btn cmn-btn"
										id="pay-button">@lang('Pay Now')
								</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

@endsection

@if($deposit->gateway->environment == 'live')
	@section('scripts')
		<script type="text/javascript"
				src="https://app.midtrans.com/snap/snap.js"
				data-client-key="{{ $data->client_key }}"></script>

		<script defer>
			var payButton = document.getElementById('pay-button');
			payButton.addEventListener('click', function () {
				window.snap.pay("{{ $data->token }}");
			});
		</script>
	@endsection
@else
	@section('scripts')
		<script type="text/javascript"
				src="https://app.sandbox.midtrans.com/snap/snap.js"
				data-client-key="{{ $data->client_key }}"></script>

		<script defer>
			var payButton = document.getElementById('pay-button');
			payButton.addEventListener('click', function () {
				window.snap.pay("{{ $data->token }}");
			});
		</script>
	@endsection
@endif

