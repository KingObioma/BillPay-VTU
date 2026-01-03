<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Providers\RouteServiceProvider;
use App\Traits\Notify;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class VerificationController extends Controller
{
	use VerifiesEmails, Notify;

	protected $redirectTo = RouteServiceProvider::HOME;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
	}

	public function checkValidCode($user, $code, $add_min = 10000)
	{
		if (!$code) return false;
		if (!$user->sent_at) return false;
		if (Carbon::parse($user->sent_at)->addMinutes($add_min) < Carbon::now()) return false;
		if ($user->verify_code !== $code) return false;
		return true;
	}

	public function check()
	{
		return redirect()->route('user.dashboard');
		$user = $this->user;
		if (!$user->status) {
			Auth::logout();
		} elseif (!$user->email_verification) {
			$template = Template::where('section_name', 'email-verification')->first();
			if (!$this->checkValidCode($user, $user->verify_code)) {
				$user->verify_code = code(6);
				$user->sent_at = Carbon::now();
				$user->save();
				$this->verifyToMail($user, 'VERIFICATION_CODE', [
					'code' => $user->verify_code
				]);
			}
			return view(template() . 'auth.verification.email', compact('user', 'template'));
		} elseif (!$user->sms_verification) {
			$template = Template::where('section_name', 'sms-verification')->first();
			if (!$this->checkValidCode($user, $user->verify_code)) {
				$user->verify_code = code(6);
				$user->sent_at = Carbon::now();
				$user->save();

				$this->verifyToSms($user, 'VERIFICATION_CODE', [
					'code' => $user->verify_code
				]);
			}
			return view(template() . 'auth.verification.sms', compact('user', 'template'));
		} elseif (!$user->two_fa_verify) {
			$template = Template::where('section_name', '2fa-verification')->first();
			$page_title = '2FA Code';
			return view(template() . 'auth.verification.2stepSecurity', compact('user', 'page_title', 'template'));
		}
		return redirect()->route('user.dashboard');
	}

	public function resendCode()
	{
		$type = request()->type;
		$user = $this->user;
		if ($this->checkValidCode($user, $user->verify_code, 2)) {
			$target_time = Carbon::parse($user->sent_at)->addMinutes(2)->timestamp;
			$delay = $target_time - time();
			throw ValidationException::withMessages(['resend' => 'Please Try after ' . $delay . ' Seconds']);
		}
		if (!$this->checkValidCode($user, $user->verify_code)) {
			$user->verify_code = code(6);
			$user->sent_at = Carbon::now();
			$user->save();
		} else {
			$user->verify_code = $user->verify_code;
			$user->sent_at = Carbon::now();
			$user->save();
		}

		if ($type === 'email') {
			$this->verifyToMail($user, 'VERIFICATION_CODE', [
				'code' => $user->verify_code
			]);

			return back()->with('success', 'Email verification code has been sent');
		} elseif ($type === 'phone') {
			$this->verifyToSms($user, 'VERIFICATION_CODE', [
				'code' => $user->verify_code
			]);
			return back()->with('success', 'SMS verification code has been sent');
		} else {
			throw ValidationException::withMessages(['error' => 'Sending Failed']);
		}
	}

	public function mailVerify(Request $request)
	{
		$rules = [
			'code' => 'required',
		];
		$msg = [
			'code.required' => 'Email verification code is required',
		];
		$validate = $this->validate($request, $rules, $msg);
		$user = $this->user;

		if ($this->checkValidCode($user, $request->code)) {
			$user->email_verification = 1;
			$user->verify_code = null;
			$user->sent_at = null;
			$user->save();
			return redirect()->intended(route('user.dashboard'));
		}
		throw ValidationException::withMessages(['error' => 'Verification code didn\'t match!']);
	}

	public function smsVerify(Request $request)
	{
		$rules = [
			'code' => 'required',
		];
		$msg = [
			'code.required' => 'Sms verification code is required',
		];
		$validate = $this->validate($request, $rules, $msg);
		$user = Auth::user();

		if ($this->checkValidCode($user, $request->code)) {
			$user->sms_verification = 1;
			$user->verify_code = null;
			$user->sent_at = null;
			$user->save();

			return redirect()->intended(route('user.dashboard'));
		}
		throw ValidationException::withMessages(['error' => 'Verification code didn\'t match!']);
	}

	public function twoFAverify(Request $request)
	{
		$this->validate($request, [
			'code' => 'required',
		], [
			'code.required' => '2 Step verification code is required',
		]);
		$ga = new GoogleAuthenticator();
		$user = Auth::user();
		$getCode = $ga->getCode($user->two_fa_code);
		if ($getCode == trim($request->code)) {
			$user->two_fa_verify = 1;
			$user->save();
			return redirect()->intended(route('user.dashboard'));
		}
		throw ValidationException::withMessages(['error' => 'Wrong Verification Code']);
	}
}
