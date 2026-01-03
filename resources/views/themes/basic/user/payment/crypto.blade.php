@extends($theme.'layouts.user')
@section('page_title')
	{{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection
@section('content')
	<section class="section">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card card-primary shadow">
					<div class="card-header">@lang('Payment Preview')</div>
					<div class="card-body text-center">
						<h4 class="text-color"> @lang('PLEASE SEND EXACTLY') <span
								class="text-success"> {{ getAmount($data->amount) }}</span> {{ __($data->currency) }}
						</h4>
						<h5>@lang('TO') <span class="text-success"> {{ __($data->sendto) }}</span></h5>
						<img src="{{ $data->img }}">
						<h4 class="text-color bold">@lang('SCAN TO SEND')</h4>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

