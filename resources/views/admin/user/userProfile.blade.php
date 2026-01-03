@extends('admin.layouts.master')
@section('page_title', __('User profile'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('User profile')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('User profile')</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<!-- Profile Cover -->
					<div class="profile-wrapper text-center">
						<div class="cover">
							<img class="cover-img img-fluid rounded"
								 src="{{asset('assets/upload/default2.jpg')}}"
								 alt="Image Description">
						</div>
						<div class="profile">
							<!-- Avatar -->
							<div class="avatar avatar-xxl avatar-circle profile-cover-avatar mb-4">
								<img src="{{$user->profilePicture()}}" alt="..." class="img-fluid">
								<span class="avatar-status avatar-status-success"></span>
							</div>
							<!-- End Avatar -->

							<h1 class="page-header-title">{{$user->name}} <i
									class="fas fa-patch-check-fill fs-2 text-primary" data-bs-toggle="tooltip"
									data-bs-placement="top" title="Top endorsed"></i></h1>

							<!-- List -->
							<ul class="list-inline list-px-2">
								<li class="list-inline-item">
									<i class="bi-building me-1"></i>
									<span></span>
								</li>

								<li class="list-inline-item">
									<i class="bi-geo-alt me-1"></i>
									<a href="#"> </a>
									<a href="#"></a>
								</li>

								<li class="list-inline-item">
									<i class="bi-calendar-week me-1"></i>
									<span><i
											class="fas fa-calendar-alt"></i> @lang('Joined') {{dateTime($user->created_at,'d M, Y')}}</span>
								</li>
							</ul>
							<!-- End List -->
						</div>
					</div>

					<div class="row">
						<div class="container-fluid user-profile" id="container-wrapper">
							<div class="row mb-3 gap-4">
								<div class="col-lg-9">

									<ul class="nav nav-tabs" role="tablist">
										<li class="nav-item">
											<a class="nav-link {{menuActive('user-profile')}}"
											   href="{{route('user-profile',$user->id)}}">@lang('Profile')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{menuActive(['user-transaction','user-transactionSearch'])}}"
											   href="{{route('user-transaction',$user->id)}}">@lang('Transaction')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{menuActive(['user-paymentLog','user-paymentLogSearch'])}}"
											   href="{{route('user-paymentLog',$user->id)}}">@lang('Payment History')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link {{menuActive('user-billPay')}}"
											   href="{{route('user-billPay',$user->id)}}">@lang('BillPay History')</a>
										</li>
									</ul>
								</div>
								<div class="col-lg-3 text-lg-right">

									<a href="{{route('user.edit',$user)}}"
									   class="btn btn-sm btn-outline-primary m-1 text-right ml-auto py-1 px-2"><i
											class="fas fa-user"></i> @lang('Edit profile')</a>
									<div class="dropdown d-inline">
										<button class="btn btn-sm btn-outline-primary p-1 px-3 dropdown-toggle"
												type="button" id="dropdownMenuButton2" data-toggle="dropdown"
												aria-haspopup="true" aria-expanded="false">
											<i class="fas fa-ellipsis-v"></i>
										</button>
										<div class="dropdown-menu shadow dropdown-menu-right">
											<a href="{{route('send.mail.user',$user)}}"
											   class="dropdown-item">
												<i class="far fa-envelope text-primary mr-1"></i> @lang('Send Mail')
											</a>

											<a href="javascript:void(0)" class=" dropdown-item" data-toggle="modal"
											   data-target="#balance"><i class="text-warning fas fa-plus-circle"></i>
												@lang('Add/Subtract Balance') </a>


											<a href="{{ route('user.asLogin',$user) }}"
											   class="dropdown-item">
												<i class="fa fa-sign-in-alt text-success mr-1"></i> @lang('Login As User')
											</a>
										</div>
									</div>
								</div>
							</div>
							@yield('extra_content')
						</div>
					</div>
					<!-- End Row -->
				</div>
				<!-- End Col -->
			</div>
		</section>
	</div>
	<div class="modal fade" id="balance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form method="post" action="{{ route('user.balance.update',$user->id) }}"
					  enctype="multipart/form-data">
					@csrf
					<!-- Modal Header -->
					<div class="modal-header modal-colored-header bg-primary">
						<h4 class="modal-title text-white">@lang('Add / Subtract Balance')</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<!-- Modal body -->
					<div class="modal-body">
						<div class="form-group ">
							<label>@lang('Amount')</label>
							<div class="input-group">
								<input class="form-control" type="text" name="balance" id="balance">
								<div class="input-group-prepend">
									<span class="form-control">{{config('basic.base_currency')}}</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="selectgroup w-100">
								<label class="selectgroup-item">
									<input type="radio" name="add_status" value="1"
										   class="selectgroup-input" checked>
									<span class="selectgroup-button">@lang('Add Balance')</span>
								</label>
								<label class="selectgroup-item">
									<input type="radio" name="add_status" value="0"
										   class="selectgroup-input">
									<span class="selectgroup-button">@lang('Substruct Balance')</span>
								</label>
							</div>
						</div>
					</div>
					<!-- Modal footer -->
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('Close')</span>
						</button>
						<button type="submit" class=" btn btn-primary balanceSave"><span>@lang('Submit')</span>
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
@endsection
