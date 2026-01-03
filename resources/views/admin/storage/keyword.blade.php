@extends('admin.layouts.master')
@section('page_title',__('Update ') . $language->name. __(' Keywords'))
@push('extra_styles')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Update ') {{ $language->name }} @lang('Keywords')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item"><a href="{{ route('language.index') }}">@lang('Languages')</a></div>
					<div class="breadcrumb-item">@lang('Update ') {{ $language->name }} @lang('Keywords')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div class="card-header align-items-center d-flex justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Edit ') {{ __($language->name) }} @lang('Keywords')</h6>
									<div class="d-flex justify-content-end align-items-start mt-2">
										<button type="button" data-toggle="modal" data-target="#addModal"
												class="btn btn-sm btn-outline-primary mr-2"><i
													class="fas fa-plus"></i> @lang('Add New Key') </button>
										<span>
											<div class="input-group">
												<select class="form-control select-language">
													<option value="">@lang('Import Keywords')</option>
													@foreach($languages as $data)
														@if($data->id != $language->id)
															<option value="{{ $data->id }}">{{ __($data->name) }}</option>
														@endif
													@endforeach
												</select>
												<div class="input-group-append">
													<button type="button" class="btn btn-sm btn-outline-primary import-language"><i
																class="fas fa-file-import"></i> @lang('Import Now')</button>
												</div>
											</div>
										<small class="text-danger">@lang("If you import keywords from another language, Your present `$language->name` all keywords will remove.")</small>
									</span>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="zero_config" class="table table-striped table-hover align-items-center table-bordered">
											<thead class="thead-light">
											<tr>
												<th col="scope">@lang('Key')</th>
												<th col="scope">{{ __($language->name) }}</th>
												<th col="scope">@lang('Action')</th>
											</tr>
											</thead>
											<tbody>
											@foreach($json as $k => $langValue)
												<tr>
													<td data-label="@lang('key')">{{ __($k) }}</td>
													<td data-label="@lang('Value')">{{ __($langValue) }}</td>
													<td data-label="@lang('Action')">
														<button type="button"
																data-target="#editModal"
																data-toggle="modal"
																data-title="{{ $k }}"
																data-key="{{ $k }}"
																data-value="{{ $langValue }}"
																class="editModal btn btn-outline-primary btn-sm "
																data-original-title="@lang('Edit')">
															<i class="fas fa-pencil-alt"></i>
														</button>
														<button type="button"
																data-key="{{ $k }}"
																data-value="{{ $langValue }}"
																data-toggle="modal" data-target="#deleteModal"
																class="btn btn-outline-danger btn-sm deleteKey"
																data-original-title="@lang('Remove')">
															<i class="fas fa-times-circle"></i>
														</button>
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>
	</div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">@lang('Edit')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">@lang('×')</span></button>
                </div>

                <form action="{{route('language.update.key',$language)}}" method="post">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="form-group ">
                            <label for="inputName" class="control-label form-title"></label>
                            <input type="text" class="form-control form-control-sm" name="value"
                                   placeholder="@lang('Enter language value')"
                                   value="">
                        </div>
                        <input type="hidden" name="key">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-sm btn-primary">@lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="myModalLabel"><i
                                class="fas fa-trash-alt"></i> @lang('Confirmation !')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body text-center">
                    <strong>@lang('Are you sure, you want to Delete?')</strong>
                </div>
                <form action="{{route('language.delete.key',$language)}}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="key">
                    <input type="hidden" name="value">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-sm btn-danger ">@lang('Delete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"> @lang('Add New')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <form action="{{route('language.store.key',$language)}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="key" class="control-label">@lang('Key')</label>
                            <input type="text" class="form-control " id="key" name="key"
                                   placeholder="@lang('Enter language key')"
                                   value="{{ old('key') }}">
                        </div>
                        <div class="form-group">
                            <label for="value" class="control-label">@lang('Value')</label>
                            <input type="text" class="form-control form-control-sm" id="value" name="value"
                                   placeholder="@lang('Enter language value')" value="{{ old('value') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-sm btn-primary"> @lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('extra_scripts')
    <script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
@endpush
@section('scripts')
    <script>
        'use strict'
        $(document).ready(function () {
            $('#zero_config').DataTable({
                "aaSorting": [],
                "ordering": false
            });

            $(document).on('click', '.deleteKey', function () {
                let modal = $('#deleteModal');
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
            });

            $(document).on('click', '.editModal', function () {
                let modal = $('#editModal');
                modal.find('.form-title').text($(this).data('title'));
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
            });

            $(document).on('click', '.import-language', function () {
                let id = $('.select-language').val();

                if (id === '') {
                    Notiflix.Notify.Failure("{{trans('Please Select a language to Import')}}");
                    return 0;
                } else {
                    $.ajax({
                        type: "post",
                        url: "{{route('language.import.json')}}",
                        data: {
                            id: id,
                            myLangid: "{{$language->id}}",
                            _token: "{{csrf_token()}}"
                        },
                        success: function (data) {

                            if (data === 'success') {
                                Notiflix.Notify.Success("{{trans('Import Data Successfully')}}");

                                window.location.href = "{{url()->current()}}"
                            }
                        }
                        ,
                        error: function (res) {

                        }
                    });
                }
            });
        });
    </script>
@endsection
