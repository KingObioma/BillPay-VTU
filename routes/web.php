<?php

use App\Http\Controllers\Admin\AdminDepositController;
use App\Http\Controllers\Admin\AdminStorageController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\ManualGatewayController;
use App\Http\Controllers\Admin\BillMethodController;
use App\Http\Controllers\Admin\BillServiceController;
use App\Http\Controllers\Admin\BillPayController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PushNotifyController;
use App\Http\Controllers\Admin\AdminSocialiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\PayBillController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\khaltiPaymentController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BasicControlController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FaSecurityController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SiteNotificationController;
use App\Http\Controllers\SmsControlController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ManualRecaptchaController;
use App\Http\Controllers\FundController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

Route::get('/login', function () {
	abort(404);
});

Route::get('queue-work', function () {
	return Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
})->name('queue.work');

Route::get('clear', function () {
	return Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('schedule-run', function () {
	return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('schedule:run');

Route::get('removeStatus', function () {
	session()->forget('status');
})->name('removeStatus');


Route::get('payment/view/{utr}', 'Api\V1\PaymentController@paymentView')->name('paymentView');
Route::match(['get', 'post'], 'success', [PaymentController::class, 'success'])->name('success');
Route::match(['get', 'post'], 'failed', [PaymentController::class, 'failed'])->name('failed');
Route::match(['get', 'post'], 'payment/{code}/{trx?}/{type?}', [PaymentController::class, 'gatewayIpn'])->name('ipn');
Route::post('/khalti/payment/verify/{trx}', [khaltiPaymentController::class, 'verifyPayment'])->name('khalti.verifyPayment');
Route::post('/khalti/payment/store', [khaltiPaymentController::class, 'storePayment'])->name('khalti.storePayment');

Route::group(['prefix' => 'admin'], function () {
	/* Authentication Routes */
	Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.login');
	Route::post('login', [LoginController::class, 'login'])->name('admin.auth.login');

	/* Password Reset Routes */
	Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
	Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
	Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset')->middleware('guest');
	Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('admin.password.reset.update');
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'demo']], function () {

	/* Bill Method Routes */
	Route::get('bill-method-list', [BillMethodController::class, 'index'])->name('admin.bill.method.list');
	Route::get('bill-method-edit/{id}', [BillMethodController::class, 'edit'])->name('admin.bill.method.edit');
	Route::post('bill-method-edit/{id}', [BillMethodController::class, 'save'])->name('admin.bill.method.save');
	Route::post('bill-method-activated/{id}', [BillMethodController::class, 'activated'])->name('admin.bill.method.activated');
	Route::put('bill-service-convert/{id}', [BillMethodController::class, 'serviceRate'])->name('admin.bill.service.rate');

	/* Bill Operators Routes */
	Route::post('fetch/operators', [BillServiceController::class, 'fetchOperators'])->name('admin.bill.fetch.operators');
	Route::post('fetch/balance', [BillServiceController::class, 'fetchBalance'])->name('admin.fetch.balance');

	/* Bill Services Routes */
	Route::get('api/bill-service/{id}', [BillServiceController::class, 'fetchServices'])->name('admin.bill.fetch.service');
	Route::post('api/add-service', [BillServiceController::class, 'addServices'])->name('admin.bill.add.service');
	Route::post('api/add-service/bulk', [BillServiceController::class, 'addServicesBulk'])->name('admin.bill.add.bulk.service');

	Route::get('bill-service-list', [BillServiceController::class, 'serviceList'])->name('admin.bill.service.list');
	Route::post('bill-charge-limit/add', [BillServiceController::class, 'chargeLimitAdd'])->name('admin.bill.chargeLimit.add');
	Route::post('bill-charge-limit/{id}', [BillServiceController::class, 'chargeLimitEdit'])->name('admin.bill.chargeLimit.edit');
	Route::post('bill-service-change/{id}', [BillServiceController::class, 'statusChange'])->name('admin.bill.status.change');

	/* Bill Pay List */
	Route::get('bill-pay-list/{type?}', [BillPayController::class, 'billPayList'])->name('bill.pay.list');
	Route::get('bill-pay-list/view/{utr}', [BillPayController::class, 'billPayView'])->name('bill.pay.view');
	Route::post('bill-pay/confirm/{utr}', [BillPayController::class, 'billPayConfirm'])->name('bill.pay.confirm');
	Route::post('bill-pay/return/{utr}', [BillPayController::class, 'billPayReturn'])->name('bill.pay.return');

	Route::get('bill-list/{userId}', [BillPayController::class, 'billPayByUser'])->name('bill.pay.byUser');

	Route::post('/save-token', [AdminController::class, 'saveToken'])->name('admin.save.token');
	/* ===== ADMIN STORAGE ===== */
	Route::get('storage', [AdminStorageController::class, 'index'])->name('storage.index');
	Route::any('storage/edit/{id}', [AdminStorageController::class, 'edit'])->name('storage.edit');
	Route::post('storage/set-default/{id}', [AdminStorageController::class, 'setDefault'])->name('storage.setDefault');

	/* ===== ADMIN SOCIALITE ===== */
	Route::get('socialite', [AdminSocialiteController::class, 'index'])->name('socialite.index');
	Route::match(['get', 'post'], 'google-config', [AdminSocialiteController::class, 'googleConfig'])->name('google.control');
	Route::match(['get', 'post'], 'facebook-config', [AdminSocialiteController::class, 'facebookConfig'])->name('facebook.control');
	Route::match(['get', 'post'], 'github-config', [AdminSocialiteController::class, 'githubConfig'])->name('github.control');


	/* USER LIST */
	Route::get('user-list', [UserController::class, 'index'])->name('user-list');
	Route::get('inactive-user-list', [UserController::class, 'inactiveUserList'])->name('inactive.user.list');
	Route::get('user-profile/{id}', [UserController::class, 'userProfile'])->name('user-profile');
	Route::get('user-transaction/{id}', [UserController::class, 'userTransaction'])->name('user-transaction');
	Route::get('user-transaction/search/{id}', [UserController::class, 'userTransactionSearch'])->name('user-transactionSearch');
	Route::get('user-payment-log/{id}', [UserController::class, 'userPaymentLog'])->name('user-paymentLog');
	Route::get('user-payment-log/search/{id}', [UserController::class, 'userPaymentLogSearch'])->name('user-paymentLogSearch');
	Route::get('user-bill-pay/{id}', [UserController::class, 'userBillPay'])->name('user-billPay');
	Route::get('user-two-fa-status/{id}', [UserController::class, 'twoFaStatus'])->name('user-twoFaStatus');

	Route::get('user-search', [UserController::class, 'search'])->name('user.search');
	Route::get('inactive-user-search', [UserController::class, 'inactiveUserSearch'])->name('inactive.user.search');

	Route::match(['get', 'post'], 'user-edit/{user}', [UserController::class, 'edit'])->name('user.edit');
	Route::post('user-balance/update/{id}', [UserController::class, 'userBalanceUpdate'])->name('user.balance.update');
	Route::match(['get', 'post'], 'user-asLogin/{user}', [UserController::class, 'asLogin'])->name('user.asLogin');
	Route::match(['get', 'post'], 'send-mail-user/{user?}', [UserController::class, 'sendMailUser'])->name('send.mail.user');

	/* PROFILE SHOW UPDATE BY USER */
	Route::match(['get', 'post'], 'profile', [AdminProfileController::class, 'index'])->name('admin.profile');
	Route::match(['get', 'post'], 'change-password', [AdminController::class, 'changePassword'])->name('admin.change.password');

	Route::get('manage-kyc', [AdminKycController::class, 'create'])->name('kyc.create');
	Route::post('manage-kyc/update', [AdminKycController::class, 'update'])->name('kyc.update');
	Route::get('user/kyc/{status?}', [AdminKycController::class, 'list'])->name('kyc.list');
	Route::get('user/kyc/view/{id}', [AdminKycController::class, 'view'])->name('kyc.view');
	Route::post('user/kyc/action/{id}', [AdminKycController::class, 'action'])->name('kyc.action');
	Route::get('user/kyc/search', [AdminKycController::class, 'search'])->name('kyc.search');

	/* PAYMENT METHOD MANAGE BY ADMIN*/
	Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment.methods');
	Route::get('edit-payment-methods/{id}', [PaymentMethodController::class, 'edit'])->name('edit.payment.methods');
	Route::put('update-payment-methods/{id}', [PaymentMethodController::class, 'update'])->name('update.payment.methods');
	Route::post('sort-payment-methods', [PaymentMethodController::class, 'sortPaymentMethods'])->name('sort.payment.methods');

	Route::get('push-notification-show', [SiteNotificationController::class, 'showByAdmin'])->name('admin.push.notification.show');
	Route::get('push.notification.readAll', [SiteNotificationController::class, 'readAllByAdmin'])->name('admin.push.notification.readAll');
	Route::get('push-notification-readAt/{id}', [SiteNotificationController::class, 'readAt'])->name('admin.push.notification.readAt');


	/* ===== DEPOSIT VIEW MANAGE BY ADMIN ===== */
	Route::match(['get', 'post'], 'add-balance-user/{userId}', [AdminDepositController::class, 'addBalanceUser'])->name('admin.user.add.balance');


	// Manual Methods
	Route::get('payment-methods/manual', [ManualGatewayController::class, 'index'])->name('admin.deposit.manual.index');
	Route::get('payment-methods/manual/new', [ManualGatewayController::class, 'create'])->name('admin.deposit.manual.create');
	Route::post('payment-methods/manual/new', [ManualGatewayController::class, 'store'])->name('admin.deposit.manual.store');
	Route::get('payment-methods/manual/edit/{id}', [ManualGatewayController::class, 'edit'])->name('admin.deposit.manual.edit');
	Route::put('payment-methods/manual/update/{id}', [ManualGatewayController::class, 'update'])->name('admin.deposit.manual.update');

	Route::get('payment/pending', [PaymentLogController::class, 'pending'])->name('admin.payment.pending');
	Route::put('payment/action/{id}', [PaymentLogController::class, 'action'])->name('admin.payment.action');
	Route::get('payment/log', [PaymentLogController::class, 'index'])->name('admin.payment.log');
	Route::get('payment/search', [PaymentLogController::class, 'search'])->name('admin.payment.search');

	/* ===== BASIC CONTROL MANAGE BY ADMIN ===== */
	Route::get('settings/{settings?}', [BasicControlController::class, 'index'])->name('settings');
	Route::any('app/settings', [BasicControlController::class, 'appSetting'])->name('appSetting');

	Route::match(['get', 'post'], 'basic-control', [BasicControlController::class, 'basic_control'])->name('basic.control');
	Route::match(['get', 'post'], 'service-control', [BasicControlController::class, 'serviceControl'])->name('service.control');
	Route::match(['get', 'post'], 'pusher-config', [BasicControlController::class, 'pusherConfig'])->name('pusher.config');
	Route::match(['get', 'post'], 'firebase-config', [BasicControlController::class, 'firebaseConfig'])->name('firebase.config');
	Route::match(['get', 'post'], 'email-config', [BasicControlController::class, 'emailConfig'])->name('email.config');
	Route::match(['get', 'post'], 'sms-config', [SmsControlController::class, 'smsConfig'])->name('sms.config');

	Route::get('plugin-config', [BasicControlController::class, 'pluginConfig'])->name('plugin.config');
	Route::match(['get', 'post'], 'tawk-config', [BasicControlController::class, 'tawkConfig'])->name('tawk.control');
	Route::match(['get', 'post'], 'fb-messenger-config', [BasicControlController::class, 'fbMessengerConfig'])->name('fb.messenger.control');
	Route::match(['get', 'post'], 'google-recaptcha', [BasicControlController::class, 'googleRecaptchaConfig'])->name('google.recaptcha.control');
	Route::match(['get', 'post'], 'manual-recaptcha', [BasicControlController::class, 'manualRecaptchaConfig'])->name('manual.recaptcha.control');
	Route::match(['get', 'post'], 'google-analytics', [BasicControlController::class, 'googleAnalyticsConfig'])->name('google.analytics.control');

	Route::get('active-recaptcha', [BasicControlController::class, 'captchaControl'])->name('active.recaptcha');
	Route::get('active-manual-captcha', [BasicControlController::class, 'manualCaptcha'])->name('active.manual.recaptch');

	/* ===== ADMIN EMAIL-CONFIGURATION SETTINGS ===== */
	Route::get('email-templates', [EmailTemplateController::class, 'index'])->name('email.template.index');
	Route::match(['get', 'post'], 'default-template', [EmailTemplateController::class, 'defaultTemplate'])->name('email.template.default');
	Route::get('email-template/edit/{id}', [EmailTemplateController::class, 'edit'])->name('email.template.edit');
	Route::post('email-template/update/{id}', [EmailTemplateController::class, 'update'])->name('email.template.update');
	Route::post('testEmail', [EmailTemplateController::class, 'testEmail'])->name('testEmail');

	/* ===== ADMIN SMS-CONFIGURATION SETTINGS ===== */
	Route::get('sms-template', [SmsTemplateController::class, 'index'])->name('sms.template.index');
	Route::get('sms-template/edit/{id}', [SmsTemplateController::class, 'edit'])->name('sms.template.edit');
	Route::post('sms-template/update/{id}', [SmsTemplateController::class, 'update'])->name('sms.template.update');

	/* ===== ADMIN NOTIFICATION-CONFIGURATION SETTINGS ===== */
	Route::get('notify-template', [NotifyController::class, 'index'])->name('notify.template.index');
	Route::get('notify-template/edit/{id}', [NotifyController::class, 'edit'])->name('notify.template.edit');
	Route::post('notify-template/update/{id}', [NotifyController::class, 'update'])->name('notify.template.update');

	/* ===== ADMIN FIREBASE NOTIFICATION-CONFIGURATION SETTINGS ===== */
	Route::get('push/notify-template', [PushNotifyController::class, 'show'])->name('push.notify.template.index');
	Route::get('push/notify-template/edit/{id}', [PushNotifyController::class, 'edit'])->name('push.notify.template.edit');
	Route::post('push/notify-template/update/{id}', [PushNotifyController::class, 'update'])->name('push.notify.template.update');


	/* ===== ADMIN LANGUAGE SETTINGS ===== */
	Route::get('languages', [LanguageController::class, 'index'])->name('language.index');
	Route::get('language/create', [LanguageController::class, 'create'])->name('language.create');
	Route::post('language/create', [LanguageController::class, 'store'])->name('language.store');
	Route::get('language/{language}', [LanguageController::class, 'edit'])->name('language.edit');
	Route::put('language/{language}', [LanguageController::class, 'update'])->name('language.update');
	Route::delete('language-delete/{language}', [LanguageController::class, 'destroy'])->name('language.delete');

	Route::get('language-keyword/{language}', [LanguageController::class, 'keywordEdit'])->name('language.keyword.edit');
	Route::put('language-keyword/{language}', [LanguageController::class, 'keywordUpdate'])->name('language.keyword.update');
	Route::post('language-import-json', [LanguageController::class, 'importJson'])->name('language.import.json');
	Route::post('store-key/{language}', [LanguageController::class, 'storeKey'])->name('language.store.key');
	Route::put('update-key/{language}', [LanguageController::class, 'updateKey'])->name('language.update.key');
	Route::delete('delete-key/{language}', [LanguageController::class, 'deleteKey'])->name('language.delete.key');


	/* ===== ADMIN SUPPORT TICKET ===== */
	Route::get('tickets', [AdminTicketController::class, 'tickets'])->name('admin.ticket');
	Route::get('tickets-search', [AdminTicketController::class, 'ticketSearch'])->name('admin.ticket.search');
	Route::get('tickets-view/{id}', [AdminTicketController::class, 'ticketReply'])->name('admin.ticket.view');
	Route::put('ticket-reply/{id}', [AdminTicketController::class, 'ticketReplySend'])->name('admin.ticket.reply');
	Route::get('ticket-download/{ticket}', [AdminTicketController::class, 'ticketDownload'])->name('admin.ticket.download');
	Route::post('ticket-delete', [AdminTicketController::class, 'ticketDelete'])->name('admin.ticket.delete');


	/* ===== ADMIN TEMPLATE SETTINGS ===== */
	Route::get('template/{section}', [TemplateController::class, 'show'])->name('template.show');
	Route::put('template/{section}/{language}', [TemplateController::class, 'update'])->name('template.update');

	Route::get('contents/{content}', [ContentController::class, 'index'])->name('content.index');
	Route::get('content-create/{content}', [ContentController::class, 'create'])->name('content.create');
	Route::put('content-create/{content}/{language?}', [ContentController::class, 'store'])->name('content.store');
	Route::get('content-show/{content}', [ContentController::class, 'show'])->name('content.show');
	Route::put('content-update/{content}/{language?}', [ContentController::class, 'update'])->name('content.update');
	Route::delete('content-delete/{id}', [ContentController::class, 'destroy'])->name('content.delete');

	Route::match(['get', 'post'], 'logo-settings', [HomeController::class, 'logoUpdate'])->name('logo.update');
	Route::match(['get', 'post'], 'breadcrumb-settings', [HomeController::class, 'breadcrumbUpdate'])->name('breadcrumb.update');
	Route::match(['get', 'post'], 'seo-settings', [HomeController::class, 'seoUpdate'])->name('seo.update');

	/* ===== SUBSCRIBER VIEW MANAGE BY ADMIN ===== */
	Route::get('subscriber-list', [SubscribeController::class, 'index'])->name('subscribe.index');
	Route::get('subscriber-search', [SubscribeController::class, 'search'])->name('subscribe.search');
	Route::match(['get', 'post'], 'send-mail-subscriber/{subscribe?}', [SubscribeController::class, 'sendMailSubscribe'])->name('send.mail.subscribe');

	/* Transaction List*/
	Route::get('transaction-list', [AdminTransactionController::class, 'index'])->name('admin.transaction.index');
	Route::get('transaction-search', [AdminTransactionController::class, 'search'])->name('admin.transaction.search');
	Route::get('transaction-list/{userId}', [AdminTransactionController::class, 'showByUser'])->name('admin.user.transaction.show');
	Route::get('transaction-search/{userId}', [AdminTransactionController::class, 'searchByUser'])->name('admin.user.transaction.search');

	Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.home');
	Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');
});


Route::group(['prefix' => 'user', 'middleware' => ['auth', 'verifyUser']], function () {

	/* Add Fund Routes */
	Route::any('add-fund', [FundController::class, 'addFund'])->name('addFund');
	Route::get('add-fund/request', [FundController::class, 'addFundRequest'])->name('addFundRequest');

	/* PAY Bill BY USER */
	Route::get('pay-bill', [PayBillController::class, 'payBill'])->name('pay.bill');
	Route::get('pay-bill/{code}', [PayBillController::class, 'payBillSelect'])->name('pay.bill.select');
	Route::post('pay-bill/fetch-services', [PayBillController::class, 'fetchServices'])->name('fetch.services');
	Route::post('pay-bill/fetch-amount', [PayBillController::class, 'fetchAmount'])->name('fetch.amount');
	Route::post('pay-bill/fetch-service-field', [PayBillController::class, 'fetchServiceField'])->name('fetch.serviceField');

	Route::post('pay-bill/submit', [PayBillController::class, 'payBillSubmit'])->name('pay.bill.submit');
	Route::get('pay-bill/preview/{utr}', [PayBillController::class, 'payBillPreview'])->name('pay.bill.preview');
	Route::post('pay-bill/preview-confirm/{utr}', [PayBillController::class, 'payBillPreviewConfirm'])->name('pay.bill.previewConfirm');
	Route::post('gateway-charge/fetch', [PayBillController::class, 'gatewayChargeFetch'])->name('gatewayChargeFetch');

	/* PAY Bill BY USER */
	Route::get('bill-pay/list', [PayBillController::class, 'payBillList'])->name('pay.bill.list');
	Route::get('bill-pay/details/{id}', [PayBillController::class, 'payBillDetails'])->name('pay.bill.details');
	Route::get('bill-pay/request', [PayBillController::class, 'payBillRequest'])->name('pay.bill.request');

	Route::post('/save-token', [HomeController::class, 'saveToken'])->name('user.save.token');
	Route::get('/dashboard', [HomeController::class, 'index'])->name('user.dashboard');
	Route::get('get-transaction-chart', [HomeController::class, 'getTransactionChart'])->name('user.get.transaction.chart');

	/* Transaction List*/
	Route::get('transaction-list', [TransactionController::class, 'index'])->name('user.transaction');
	Route::get('transaction-search', [TransactionController::class, 'search'])->name('user.transaction.search');

	Route::get('push-notification-show', [SiteNotificationController::class, 'show'])->name('push.notification.show');
	Route::get('push.notification.readAll', [SiteNotificationController::class, 'readAll'])->name('push.notification.readAll');
	Route::get('push-notification-readAt/{id}', [SiteNotificationController::class, 'readAt'])->name('push.notification.readAt');

	/* PROFILE SHOW UPDATE BY USER */
	Route::match(['get', 'post'], 'profile', [UserProfileController::class, 'index'])->name('user.profile');
	Route::match(['get', 'post'], 'change-password', [UserProfileController::class, 'changePassword'])->name('user.change.password');

	/* PROFILE NOTIFICATION UPDATE BY USER */
	Route::match(['get', 'post'], 'notification', [UserProfileController::class, 'notification'])->name('user.notification');


	/* USER SUPPORT TICKET */
	Route::get('tickets', [SupportController::class, 'index'])->name('user.ticket.list');
	Route::get('ticket-create', [SupportController::class, 'create'])->name('user.ticket.create');
	Route::post('ticket-create', [SupportController::class, 'store'])->name('user.ticket.store');
	Route::get('ticket-view/{ticket}', [SupportController::class, 'view'])->name('user.ticket.view');
	Route::put('ticket-reply/{ticket}', [SupportController::class, 'reply'])->name('user.ticket.reply');
	Route::get('ticket-download/{ticket}', [SupportController::class, 'download'])->name('user.ticket.download');

	// TWO-FACTOR SECURITY
	Route::get('/twostep-security', [FaSecurityController::class, 'twoStepSecurity'])->name('user.twostep.security');
	Route::post('twoStep-enable', [FaSecurityController::class, 'twoStepEnable'])->name('user.twoStepEnable');
	Route::post('twoStep-disable', [FaSecurityController::class, 'twoStepDisable'])->name('user.twoStepDisable');

});

Route::group(['prefix' => 'user'], function () {
	Auth::routes();
	// Payment confirm page
	Route::get('deposit-check-amount', [DepositController::class, 'checkAmount'])->name('deposit.checkAmount');
	Route::get('payment-process/{utr}', [PaymentController::class, 'depositConfirm'])->name('payment.process');
	Route::match(['get', 'post'], 'confirm-deposit/{utr}', [DepositController::class, 'confirmDeposit'])->name('deposit.confirm');
	Route::post('addFundConfirm/{utr}', [PaymentController::class, 'fromSubmit'])->name('addFund.fromSubmit');

	Route::get('check', [VerificationController::class, 'check'])->name('user.check');
	Route::get('resend_code', [VerificationController::class, 'resendCode'])->name('user.resendCode');
	Route::post('mail-verify', [VerificationController::class, 'mailVerify'])->name('user.mailVerify');
	Route::post('sms-verify', [VerificationController::class, 'smsVerify'])->name('user.smsVerify');

	Route::get('kyc/fill-up', [HomeController::class, 'kycShow'])->name('user.kyc');
	Route::post('kyc/fill-up/store', [HomeController::class, 'kycStore'])->name('user.kycStore');

	Route::post('twoFA-Verify', [VerificationController::class, 'twoFAverify'])->name('user.twoFA-Verify');
	Route::get('auth/{socialite}', [SocialiteController::class, 'socialiteLogin'])->name('socialiteLogin');
	Route::get('auth/callback/{socialite}', [SocialiteController::class, 'socialiteCallback'])->name('socialiteCallback');
});


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/blog-details/{contentDetails}', [HomeController::class, 'blogDetails'])->name('blogDetails');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact/send', [HomeController::class, 'contactSend'])->name('contact.send');
Route::get('/captcha', [ManualRecaptchaController::class, 'reCaptCha'])->name('captcha');

Route::post('subscribe', [HomeController::class, 'subscribe'])->name('subscribe');
Route::get('{content_id}/{getLink}', [HomeController::class, 'getLink'])->name('getLink');
Route::get('/{template}', [HomeController::class, 'getTemplate'])->name('getTemplate');

Route::get('/language/switch/{code}', [HomeController::class, 'setLanguage'])->name('language');
