@extends($theme.'layouts.user')
@section('page_title',__('Pay Bill'))

@section('content')
	<div class="card">
		<div class="row">
			<div class="col-xl-8 mx-auto">
				<div class="paybill-category-container mt-50 mb-50">
					@if(count($activeServices) > 0)
						@foreach($activeServices as $key => $service)
							<a href="{{route('pay.bill.select',$key)}}" class="category-box">
								<div class="icon-box">
									<img
										src="{{$service['image']}}" class="w-45"
										alt="{{$service['name']}}"/>
								</div>
								<p class="title">{{$service['name']}}</p>
							</a>
						@endforeach
					@else
						@include($theme.'user.empty')
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
