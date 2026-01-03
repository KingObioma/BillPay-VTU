@extends($theme.'layouts.user')
@section('page_title')
	{{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection
@section('section')
	<section class="section">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card">
					<div class="card-body text-center">
						<form
							action="{{ route('ipn', [optional($deposit->gateway)->code ?? 'mercadopago', $deposit->utr]) }}"
							method="POST">
							<script src="https://www.mercadopago.com.co/integrations/v1/web-payment-checkout.js"
									data-preference-id="{{ $data->preference }}">
							</script>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
