@extends('admin.layouts.master')
@section('page_title',__('Edit '). ' '.__(kebab2Title($content->name)))

@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/dashboard/css/bootstrap-iconpicker.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Edit') @lang(kebab2Title($content->name))</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Edit') @lang(kebab2Title($content->name))</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Edit') @lang(kebab2Title($content->name))</h6>
									<a href="{{route('content.index',$content->name)}}"
									   class="btn btn-sm btn-outline-primary"> <i
											class="fas fa-arrow-left"></i> @lang('Back')</a>
								</div>
								<div class="card-body">
									@if(array_key_exists('language',config("contents.$content->name")) && config("contents.$content.language") == 0)
										<form method="post" action="{{ route('content.update', [$content,0]) }}"
											  enctype="multipart/form-data">
											@csrf
											@method('put')
											<div class="row">
												@foreach(config("contents.$content->name.field_name") as $name => $type)
													@if($type == 'file')
														<div class="col-md-6">
															<div class="form-group mb-4">
																<label
																	class="col-form-label">@lang(ucwords(str_replace('_',' ',$name)))</label>
																<div id="image-preview" class="image-preview"
																	 style="background-image: url({{ getFile(config('location.content.path').(isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '')) }});">
																	<label for="image-upload"
																		   id="image-label">@lang('Choose File')</label>
																	<input type="file" name="{{ $name }}[0]"
																		   class="@error($name.'.0') is-invalid @enderror"
																		   id="image-upload"/>
																</div>
																<div class="invalid-feedback">
																	@error($name.'.0') @lang($message) @enderror
																</div>
															</div>
														</div>
													@elseif($type == 'url')
														<div class="col-md-12">
															<div class="form-group">
																<label
																	for="{{ $name }}"> @lang(ucwords(str_replace('_',' ',$name))) </label>
																<input type="{{ $type }}" name="{{ $name }}[0]"
																	   class="form-control @error($name.'.0') is-invalid @enderror"
																	   value="{{ old($name.'.0', isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}">
																<div class="invalid-feedback">
																	@error($name.'.0') @lang($message) @enderror
																</div>
																<div class="valid-feedback"></div>
															</div>
														</div>
													@elseif($type == 'icon')
														<div class="col-md-12">
															<div class="form-group">
																<label
																	for="{{ $name }}"> @lang(ucwords(str_replace('_',' ',$name))) </label>
																<div class="input-group input-group-sm">
																	<input type="text" name="{{ $name }}[{{ 0 }}]"
																		   class="form-control icon @error($name.'.0') is-invalid @enderror"
																		   value="{{ old($name.'.0', isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}">
																	<div class="input-group-append">
																		<button
																			class="btn btn-outline-primary iconPicker"
																			data-icon="{{ old($name.'.0', isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}"
																			role="iconpicker"></button>
																	</div>
																	<div
																		class="invalid-feedback">@error($name.'.0') @lang($message) @enderror</div>
																</div>
															</div>
														</div>
													@endif
												@endforeach
											</div>
											<button type="submit"
													class="btn btn-primary btn-sm btn-block">@lang('Save Change')</button>
										</form>
									@else
										<ul class="nav nav-tabs" id="myTab" role="tablist">
											@foreach($languages as $key => $language)
												<li class="nav-item">
													<a class="nav-link {{ $loop->first ? 'active' : '' }}"
													   data-toggle="tab" href="#lang-tab-{{ $key }}" role="tab"
													   aria-controls="lang-tab-{{ $key }}"
													   aria-selected="{{ $loop->first ? 'true' : 'false' }}">@lang($language->name)</a>
												</li>
											@endforeach
										</ul>
										<div class="tab-content mt-2" id="myTabContent">
											@foreach($languages as $key => $language)
												<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
													 id="lang-tab-{{ $key }}" role="tabpanel">
													<form method="post"
														  action="{{ route('content.update', [$content,$language->id]) }}"
														  enctype="multipart/form-data">
														@csrf
														@method('put')
														<div class="row">
															@foreach(config("contents.$content->name.field_name") as $name => $type)
																@if($type == 'text')
																	<div class="col-md-12">
																		<div class="form-group">
																			<label
																				for="{{ $name }}"> @lang(ucwords(str_replace('_',' ',$name))) </label>
																			<input type="{{ $type }}"
																				   name="{{ $name }}[{{ $language->id }}]"
																				   class="form-control @error($name.'.'.$language->id) is-invalid @enderror"
																				   value="{{ old($name.'.'.$language->id, isset($contentDetails[$language->id]) ? @$contentDetails[$language->id][0]->description->{$name} : '') }}">
																			<div class="invalid-feedback">
																				@error($name.'.'.$language->id) @lang($message) @enderror
																			</div>
																			<div class="valid-feedback"></div>
																		</div>
																	</div>
																@elseif($type == 'file' && $key == 0)
																	<div class="col-md-6">
																		<div class="form-group mb-4">
																			<label
																				class="col-form-label">@lang(ucwords(str_replace('_',' ',$name)))</label>
																			<div id="image-preview-{{ $name }}"
																				 class="image-preview"
																				 style="background-image: url({{ getFile(@$contentMedia->driver,(isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '')) }});">
																				<label for="image-upload"
																					   id="image-label">@lang('Choose File')</label>
																				<input type="file"
																					   name="{{ $name }}[{{ $language->id }}]"
																					   class="@error($name.'.'.$language->id) is-invalid @enderror"
																					   id="image-upload-{{ $name }}"/>
																			</div>
																			<div class="invalid-feedback">
																				@error($name.'.'.$language->id) @lang($message) @enderror
																			</div>
																		</div>
																	</div>
																@elseif($type == 'url' && $key == 0)
																	<div class="col-md-12">
																		<div class="form-group">
																			<label
																				for="{{ $name }}"> @lang(ucwords(str_replace('_',' ',$name))) </label>
																			<input type="{{ $type }}"
																				   name="{{ $name }}[{{ $language->id }}]"
																				   class="form-control @error($name.'.'.$language->id) is-invalid @enderror"
																				   value="{{ old($name.'.'.$language->id, isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}">
																			<div class="invalid-feedback">
																				@error($name.'.'.$language->id) @lang($message) @enderror
																			</div>
																			<div class="valid-feedback"></div>
																		</div>
																	</div>
																@elseif($type == 'textarea')
																	<div class="col-md-12">
																		<div class="form-group">
																			<label
																				for="{{ $name }}">@lang(ucwords(str_replace('_',' ',$name)))</label>
																			<textarea
																				class="form-control summernote @error($name.'.'.$language->id) is-invalid @enderror"
																				name="{{ $name }}[{{ $language->id }}]"
																				rows="5">{{ old($name.'.'.$language->id, isset($contentDetails[$language->id]) ? @$contentDetails[$language->id][0]->description->{$name} : '') }}</textarea>
																			<div class="invalid-feedback">
																				@error($name.'.'.$language->id) @lang($message) @enderror
																			</div>
																		</div>
																	</div>
																@elseif($type == 'icon' && $key == 0)
																	<div class="col-md-12">
																		<div class="form-group">
																			<label for="{{ $name }}"> @lang(ucwords(str_replace('_',' ',$name))) </label>
																			<div class="input-group input-group-sm">
																				<input type="text" name="{{ $name }}[{{ 0 }}]"
																					   class="form-control icon @error($name.'.0') is-invalid @enderror"
																					   value="{{ old($name.'.0', isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}">
																				<div class="input-group-append">
																					<button class="btn btn-outline-primary iconPicker"
																							data-icon="{{ old($name.'.0', isset($contentMedia->description->{$name}) ? @$contentMedia->description->{$name} : '') }}"
																							role="iconpicker"></button>
																				</div>
																				<div class="invalid-feedback">@error($name.'.0') @lang($message) @enderror</div>
																			</div>
																		</div>
																	</div>
																@endif
															@endforeach
														</div>
														<button type="submit"
																class="btn btn-primary btn-sm btn-block">@lang('Save Change')</button>
													</form>
												</div>
											@endforeach
										</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>
	</div>
@endsection

@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush

@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$('.iconPicker').iconpicker({
				align: 'center', // Only in div tag
				arrowClass: 'btn-danger',
				arrowPrevIconClass: 'fas fa-angle-left',
				arrowNextIconClass: 'fas fa-angle-right',
				cols: 10,
				footer: true,
				header: true,
				icon: 'fas fa-bomb',
				iconset: 'fontawesome5',
				labelHeader: '{0} of {1} pages',
				labelFooter: '{0} - {1} of {2} icons',
				placement: 'bottom', // Only in button tag
				rows: 5,
				search: true,
				searchText: 'Search icon',
				selectedClass: 'btn-success',
				unselectedClass: ''
			}).on('change', function (e) {
				$(this).parent().siblings('.icon').val(`${e.icon}`);
			});

			$(document).ready(function () {
				$('input[type="file"]').each(function (key, selector) {
					const attrId = $(selector).attr('id');
					const attrLabelId = $(selector).siblings('label').attr('id');
					const attrPreviewId = $(selector).parent().attr('id');
					$.uploadPreview({
						input_field: `#${attrId}`,
						preview_box: `#${attrPreviewId}`,
						label_field: `#${attrLabelId}`,
						label_default: "Choose File",
						label_selected: "Change File",
						no_label: false
					});
				});

			});
		});
	</script>
@endsection
