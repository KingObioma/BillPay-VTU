<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BillMethodController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});

Route::match(['get', 'post'], 'bill-payment/{code}', [BillMethodController::class, 'billIpn'])->name('webhook');


Route::get('app/config', 'Api\V1\HomeController@appConfig');
Route::get('/register/form', 'Api\V1\AuthController@registerUserForm');
Route::post('/register', 'Api\V1\AuthController@registerUser');
Route::post('/login', 'Api\V1\AuthController@loginUser');
Route::post('user/login', 'Api\V1\AuthController@loginUser');
Route::post('/recovery-pass/get-email', 'Api\V1\AuthController@getEmailForRecoverPass');
Route::post('/recovery-pass/get-code', 'Api\V1\AuthController@getCodeForRecoverPass');
Route::post('/update-pass', 'Api\V1\AuthController@updatePass');
Route::get('language/{id?}', 'Api\V1\HomeController@language');


Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('user-data', 'Api\V1\HomeController@userData');
	Route::middleware('userCheckApi')->group(function () {
		Route::get('pusher/config', 'Api\V1\HomeController@pusherConfig');
		Route::get('dashboard', 'Api\V1\HomeController@dashboard');

		Route::get('transaction', 'Api\V1\HomeController@transaction');
		Route::get('transaction/search', 'Api\V1\HomeController@transactionSearch');

		Route::get('support-ticket/list', 'Api\V1\SupportTicketController@ticketList');
		Route::post('support-ticket/create', 'Api\V1\SupportTicketController@ticketCreate');
		Route::get('support-ticket/view/{id}', 'Api\V1\SupportTicketController@ticketView');
		Route::get('support-ticket/download/{id}', 'Api\V1\SupportTicketController@ticketDownlaod')->name('api.ticket.download');
		Route::post('support-ticket/reply', 'Api\V1\SupportTicketController@ticketReply');

		Route::get('profile', 'Api\V1\ProfileController@profile');
		Route::post('profile/image/upload', 'Api\V1\ProfileController@profileImageUpload');
		Route::post('profile/information/update', 'Api\V1\ProfileController@profileInfoUpdate');
		Route::post('profile/password/update', 'Api\V1\ProfileController@profilePassUpdate');
		Route::get('profile/identity-verification', 'Api\V1\ProfileController@profileIdentityVerification');
		Route::post('profile/identity-verification/submit', 'Api\V1\ProfileController@profileIdentityVerificationSubmit');

		Route::get('notification', 'Api\V1\ProfileController@notification');
		Route::post('notification/submit', 'Api\V1\ProfileController@notificationSubmit');

		Route::get('2FA-security', 'Api\V1\TwoFASecurityController@twoFASecurity');
		Route::post('2FA-security/enable', 'Api\V1\TwoFASecurityController@twoFASecurityEnable');
		Route::post('2FA-security/disable', 'Api\V1\TwoFASecurityController@twoFASecurityDisable');

		Route::get('fund-request', 'Api\V1\BillController@fundRequest');

		Route::get('bill-request', 'Api\V1\BillController@billRequest');
		Route::get('bill-pay/list', 'Api\V1\BillController@billPayList');
		Route::get('bill-pay/details/{id}', 'Api\V1\BillController@billPayDetails');

		Route::get('bill-category', 'Api\V1\BillController@billCategory');
		Route::get('pay-bill/form/{code}', 'Api\V1\BillController@payBillForm');
		Route::post('pay-bill/form/submit', 'Api\V1\BillController@payBillFormSubmit');
		Route::get('bill-pay/preview/{utr}', 'Api\V1\BillController@billPayPreview');

		Route::post('wallet-payment', 'Api\V1\PaymentController@walletPayment');
		Route::post('manual/payment/submit', 'Api\V1\PaymentController@manualPaymentSubmit');
		Route::post('automation/payment', 'Api\V1\PaymentController@automationPayment');
		Route::post('card/payment', 'Api\V1\PaymentController@cardPayment');
		Route::post('payment/done', 'Api\V1\PaymentController@paymentDone');
	});

	Route::post('/twoFA-Verify', 'Api\V1\VerificationController@twoFAverify');
	Route::post('/mail-verify', 'Api\V1\VerificationController@mailVerify');
	Route::post('/sms-verify', 'Api\V1\VerificationController@smsVerify');
	Route::get('/resend-code', 'Api\V1\VerificationController@resendCode');

});
