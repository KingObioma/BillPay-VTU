<?php

namespace App\Http\Middleware;

use App\Traits\ApiValidation;
use App\Traits\Notify;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyUserApi
{
	use ApiValidation, Notify;

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		$user = Auth::user();
		if ((Auth::user()->sms_verification == 1) && (Auth::user()->email_verification == 1) && (Auth::user()->status == 1) && (Auth::user()->two_fa_verify == 1)) {
			return $next($request);
		} else {
			if (Auth::user()->email_verification == 0) {
				$user->verify_code = code(6);
				$user->sent_at = Carbon::now();
				$user->save();
				$this->verifyToMail($user, 'VERIFICATION_CODE', [
					'code' => $user->verify_code
				]);
				return response()->json($this->withErrors('Email Verification Required'));
			} elseif (Auth::user()->sms_verification == 0) {
				$user->verify_code = code(6);
				$user->sent_at = Carbon::now();
				$user->save();

				$this->verifyToSms($user, 'VERIFICATION_CODE', [
					'code' => $user->verify_code
				]);

				return response()->json($this->withErrors('Mobile Verification Required'));
			} elseif (Auth::user()->status == 0) {
				return response()->json($this->withErrors('Your account has been suspend'));
			} elseif (Auth::user()->two_fa_verify == 0) {
				return response()->json($this->withErrors('Two FA Verification Required'));
			}
		}

	}
}
