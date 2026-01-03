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
								<h5 class="my-3">@lang('Please Pay') {{getAmount($deposit->payable_amount,3)}} {{$deposit->payment_method_currency}}</h5>
								<button type="button" class="cmn-btn"
										id="btn-confirm">@lang('Pay Now')</button>
								<form
									action="{{ route('ipn', [optional($deposit->gateway)->code, $deposit->utr]) }}"
									method="POST">
									@csrf
									<script src="//js.paystack.co/v1/inline.js"
											data-key="{{ $data->key }}"
											data-email="{{ $data->email }}"
											data-amount="{{$data->amount}}"
											data-currency="{{$data->currency}}"
											data-ref="{{ $data->ref }}"
											data-custom-button="btn-confirm">
									</script>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

