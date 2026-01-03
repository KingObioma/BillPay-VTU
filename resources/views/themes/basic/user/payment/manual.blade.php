@extends($theme.'layouts.user')
@section('page_title')
	{{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection

@section('content')
	<div class="section dashboard">
		<div class="row">
			<div class="account-settings-profile-section">
				<div class="card">
					<div class="card-body pt-0">
						<div class="profile-form-section">
							<div class="row g-3">
								<div class="col-md-12">
									<h3 class="title text-center">{{trans('Please follow the instruction below')}}</h3>
									<p class="text-center mt-2 ">{{trans('You have requested to deposit')}} <b
											class="text--base">{{getAmount($deposit->amount,3)}}
											{{config('basic.base_currency')}}</b> , {{trans('Please pay')}}
										<b class="text--base">{{getAmount($deposit->payable_amount,3)}} {{$deposit->payment_method_currency}}</b> {{trans('for successful payment')}}
									</p>

									<p class="text-danger mt-2">
										<?php echo optional($deposit->gateway)->note; ?>
									</p>


									<form action="{{route('addFund.fromSubmit',$deposit->utr)}}" method="post"
										  enctype="multipart/form-data">
										@csrf
										@if(optional($deposit->gateway)->parameters)
											@foreach($deposit->gateway->parameters as $k => $v)
												@if($v->type == "text")
													<div class="col-md-12 mt-2">
														<div class="form-group">
															<label>{{trans($v->field_level)}} @if($v->validation == 'required')
																	<span class="text--danger">*</span>
																@endif </label>
															<input type="text" name="{{$k}}"
																   class="form-control"
																   @if($v->validation == "required") required @endif>
															@if ($errors->has($k))
																<span
																	class="text--danger">{{ trans($errors->first($k)) }}</span>
															@endif
														</div>
													</div>
												@elseif($v->type == "textarea")
													<div class="col-md-12 mt-2">
														<div class="form-group">
															<label>{{trans($v->field_level)}} @if($v->validation == 'required')
																	<span class="text--danger">*</span>
																@endif </label>
															<textarea name="{{$k}}" class="form-control"
																	  rows="3"
																	  @if($v->validation == "required") required @endif></textarea>
															@if ($errors->has($k))
																<span
																	class="text--danger">{{ trans($errors->first($k)) }}</span>
															@endif
														</div>
													</div>
												@elseif($v->type == "file")
													<div class="col-md-12 mt-2">
														<label>{{trans($v->field_level)}} @if($v->validation == 'required')
																<span class="text--danger">*</span>
															@endif </label>

														<div class="col-md-12 mt-4">
															<div class="image-area">
																<img
																	src="{{getFile('local','dummy')}}"
																	alt="..." class="h-100 img-profile-view">
															</div>
															<div class="btn-area">
																<div class="btn-area-inner d-flex">
																	<div class="cmn-file-input">
																		<label for="formFile"
																			   class="form-label cmn-btn">@lang('Upload New Photo')</label>
																		<input class="form-control file-upload-input"
																			   name="{{$k}}"
																			   type="file" id="formFile"
																			   @if($v->validation == "required") required @endif>
																	</div>
																	<button type="button"
																			class="cmn-btn3 reset">@lang('reset')</button>
																</div>
															</div>
															<p class="text-danger select-files-count"></p>
															@error($k)
															<div class="error text-danger"> @lang($message) </div>
															@enderror
														</div>

													</div>
												@endif
											@endforeach
										@endif
										<div class="col-md-12 ">
											<div class=" form-group">
												<button type="submit" class="cmn-btn mt-3">
													<span>@lang('Confirm Now')</span>
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		'use strict';
		$(document).on('change', '.file-upload-input', function () {
			let _this = $(this);
			let reader = new FileReader();
			reader.readAsDataURL(this.files[0]);
			reader.onload = function (e) {
				$('.img-profile-view').attr('src', e.target.result);
			}
			var fileCount = $(this)[0].files.length;
			$('.select-files-count').text(fileCount + ' file(s) selected');
		});
		$(document).on('click', '.reset', function () {
			let img = "{{asset(config('location.default'))}}"
			$('.img-profile-view').attr('src', img);
			$('.select-files-count').text("");
		});
	</script>
@endsection
