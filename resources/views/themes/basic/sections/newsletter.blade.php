<!-- Newsletter secdtion start -->
@if(isset($templates['newsletter'][0]) && $newsletter = $templates['newsletter'][0])
	<section class="newsletter-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-6 col-md-8">
					<div class="content-area">
						<div
							class="subscribe-small-text">@lang(wordSplice(optional($newsletter->description)->title,1)['normal'])</div>
						<h1 class="subscribe-normal-text">@lang(wordSplice(optional($newsletter->description)->title,1)['highLights'])</h1>
					</div>
					<form action="{{ route('subscribe') }}" method="post" class="newsletter-form subscribe-form">
						@csrf
						<input type="email" class="form-control" id="inputEmail4"
							   placeholder="@lang('Enter your mail')">
						<button type="submit" class="subscribe-btn">@lang('Subscribe')</button>
					</form>
					@error('email')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
			</div>
		</div>
	</section>
@endif
<!-- Newsletter secdtion end -->
