@extends('admin.layouts.master')
@section('page_title', __('KYC Form'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('KYC Form')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('KYC Form')</div>
				</div>
			</div>
			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row">
						<div class="col-lg-12">
							<form action="{{route('kyc.update')}}" method="post">
								@csrf
								<div class="card mb-4 card-primary shadow-sm">
									<div
										class="card-header  d-flex flex-row align-items-center justify-content-between">
										<h6 class="m-0 font-weight-bold text-primary">@lang('KYC Form')</h6>
										<div class="row">
											<label class="custom-switch mt-2" for="is_active">
												<input type="checkbox" name="is_active" id="is_active"
													   class="custom-switch-input" value="1"
													   @if(!empty($kyc) && $kyc->status == '1') checked @endif>
												<span class="custom-switch-indicator"></span>
												<span
													class="custom-switch-description">@lang('KYC')</span>
											</label>
											<button type="button"
											   class="btn btn-dark btn-sm btn-rounded ml-4"
											   id="generate"><i class="fa fa-plus-circle"></i>
												{{ trans('Add Field') }}</button>
										</div>
									</div>
									<div class="card-body">
										<div class="row addedField">
											@if($kyc)
												@foreach ($kyc->input_form as $k => $v)
													<div class="col-md-12">
														<div class="form-group">
															<div class="input-group">

																<input name="field_name[]" class="form-control"
																	   type="text" value="{{$v->field_level}}"
																	   required
																	   placeholder="{{trans('Field Name')}}">

																<select name="type[]" class="form-control  ">
																	<option value="text"
																			@if($v->type == 'text') selected @endif>{{trans('Input Text')}}</option>
																	<option value="textarea"
																			@if($v->type == 'textarea') selected @endif>{{trans('Textarea')}}</option>
																	<option value="file"
																			@if($v->type == 'file') selected @endif>{{trans('File upload')}}</option>
																</select>

																<select name="validation[]" class="form-control  ">
																	<option value="required"
																			@if($v->validation == 'required') selected @endif>{{trans('Required')}}</option>
																	<option value="nullable"
																			@if($v->validation == 'nullable') selected @endif>{{trans('Optional')}}</option>
																</select>
																<span class="input-group-btn">
																	<button class="btn btn-danger  delete_desc"
																			type="button">
																		<i class="fa fa-times"></i>
																	</button>
																</span>
															</div>
														</div>
													</div>
												@endforeach
											@endif
										</div>
										<button type="submit" class="btn btn-primary">@lang('Save Changes')</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
@section('scripts')
	<script>
		'use script'
		$(document).on('click', '#generate', function () {
			var form = `<div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="field_name[]" class="form-control " type="text" value="" required placeholder="{{trans('Field Name')}}">

                                        <select name="type[]"  class="form-control  ">
                                            <option value="text">{{trans('Input Text')}}</option>
                                            <option value="textarea">{{trans('Textarea')}}</option>
                                            <option value="file">{{trans('File upload')}}</option>
                                        </select>

                                        <select name="validation[]"  class="form-control  ">
                                            <option value="required">{{trans('Required')}}</option>
                                            <option value="nullable">{{trans('Optional')}}</option>
                                        </select>

                                        <span class="input-group-btn">
                                            <button class="btn btn-danger delete_desc" type="button">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> `;

			$('.addedField').append(form)
		});

		$(document).on('click', '.delete_desc', function () {
			$(this).closest('.input-group').parent().remove();
		});

	</script>
@endsection
