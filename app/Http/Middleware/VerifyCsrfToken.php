<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [
		'success',
		'failed',
		'payment/*',
		'admin/sort-payment-methods',
		'*save-token*',
		'*api/add-service*',
		'*pay-bill/fetch-services*',
		'*gateway-charge/fetch*',
		'*fetch/operators*',
		'*fetch/balance*',
		'*pay-bill/fetch-amount*',
		'*active-recaptcha*',
		'*active-manual-captcha*',
	];
}
