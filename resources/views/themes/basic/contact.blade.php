@extends($theme.'layouts.app')
@section('title',trans($title))

@section('content')
	<!-- Contact section start -->
	<section class="contact-section">
		<div class="container">
			<div class="row g-4">
				<div class="col-xl-5 col-lg-6">
					@if(isset($contact))
						<div class="contact-area">
							<div class="section-header mb-0">
								<h3>@lang($contact->title)</h3>
							</div>
							<p class="para_text">@lang($contact->short_description)</p>
							<div class="contact-item-list">
								<div class="item">
									<div class="icon-area">
										<i class="fa-light fa-phone"></i>
									</div>
									<div class="content-area">
										<h6 class="mb-0">@lang('Phone:')</h6>
										<p class="mb-0">{{$contact->phone}}</p>
									</div>
								</div>
								<div class="item">
									<div class="icon-area">
										<i class="fa-light fa-envelope"></i>
									</div>
									<div class="content-area">
										<h6 class="mb-0">@lang('Email:')</h6>
										<p class="mb-0">{{$contact->email}}</p>
									</div>
								</div>
								<div class="item">
									<div class="icon-area">
										<i class="fa-light fa-location-dot"></i>
									</div>
									<div class="content-area">
										<h6 class="mb-0">@lang('Address:')</h6>
										<p class="mb-0">{{$contact->location}}</p>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
				<div class="col-xl-7 col-lg-6">
					<div class="contact-message-area">
						<div class="contact-header">
							<h3 class="section-title">@lang('Drop Us a Line')</h3>
							<p>@lang(@$contact->about_company)
							</p>
						</div>
						<form action="{{route('contact.send')}}" method="post">
							@csrf
							<div class="row">
								<div class="mb-3 col-md-6">
									<input type="text" name="name" value="{{old('name')}}" id="firstName"
										   class="form-control" placeholder="@lang('Your Name')">
									@error('name')
									<div class="text-start text-danger">
										{{$message}}
									</div>
									@enderror
								</div>
								<div class="mb-3 col-md-6">
									<input type="email" name="email" value="{{old('email')}}" id="email"
										   class="form-control" placeholder="@lang('E-mail Address')">
									@error('email')
									<div class="text-start text-danger">
										{{$message}}
									</div>
									@enderror
								</div>
								<div class="mb-3 col-md-12">
									<input type="text" name="subject"
										   value="{{old('subject')}}" id="PhoneNumber" class="form-control"
										   placeholder="@lang('Your Subject')">
									@error('subject')
									<div class="text-start text-danger">
										{{$message}}
									</div>
									@enderror
								</div>
								<div class="mb-3 col-12">
                                    <textarea class="form-control" name="message" id="exampleFormControlTextarea1"
											  rows="8"
											  placeholder="@lang('Your Massage')">{{old('message')}}</textarea>
									@error('message')
									<div class="text-start text-danger">
										{{$message}}
									</div>
									@enderror
								</div>
							</div>
							<div class="btn-area d-flex justify-content-end">
								<button type="submit" class="cmn-btn mt-30">@lang('Send a massage')</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Contact section end -->
@endsection
