<?php

namespace App\Http\Controllers;

use App\Helpers\GoogleAuthenticator;
use App\Traits\Notify;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;

class FaSecurityController extends Controller
{
	use Notify;

	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function twoStepSecurity()
	{
		$user = auth()->user();
		$basic = (object)config('basic');
		$ga = new GoogleAuthenticator();
		$secret = $ga->createSecret();
		$qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $basic->site_title, $secret);
		$previousCode = $user->two_fa_code;

		$previousQR = $ga->getQRCodeGoogleUrl($user->username . '@' . $basic->site_title, $previousCode);

		return view($this->theme . 'user.twoFA.index', compact('secret', 'qrCodeUrl', 'previousCode', 'previousQR'));
	}

	public function twoStepEnable(Request $request)
	{
		$user = auth()->user();
		$this->validate($request, [
			'key' => 'required',
			'code' => 'required',
		]);
		$ga = new GoogleAuthenticator();
		$secret = $request->key;
		$oneCode = $ga->getCode($secret);

		$userCode = $request->code;

		if ($oneCode == $userCode) {
			$user['two_fa'] = 1;
			$user['two_fa_verify'] = 1;
			$user['two_fa_code'] = $request->key;
			$user->save();
			$browser = new Browser();
			$this->mail($user, 'TWO_STEP_ENABLED', [
				'action' => 'Enabled',
				'code' => $user->two_fa_code,
				'ip' => request()->ip(),
				'browser' => $browser->browserName() . ', ' . $browser->platformName(),
				'time' => date('d M, Y h:i:s A'),
			]);
			return back()->with('success', 'Google Authenticator Has Been Enabled.');
		} else {
			return back()->with('alert', 'Wrong Verification Code.');
		}
	}


	public function twoStepDisable(Request $request)
	{
		$this->validate($request, [
			'code' => 'required',
		]);
		$user = auth()->user();
		$ga = new GoogleAuthenticator();

		$secret = $user->two_fa_code;
		$oneCode = $ga->getCode($secret);
		$userCode = $request->code;

		if ($oneCode == $userCode) {
			$user['two_fa'] = 0;
			$user['two_fa_verify'] = 1;
			$user['two_fa_code'] = null;
			$user->save();
			$browser = new Browser();
			$this->mail($user, 'TWO_STEP_DISABLED', [
				'action' => 'Disabled',
				'ip' => request()->ip(),
				'browser' => $browser->browserName() . ', ' . $browser->platformName(),
				'time' => date('d M, Y h:i:s A'),
			]);

			return back()->with('success', 'Google Authenticator Has Been Disabled.');
		} else {
			return back()->with('alert', 'Wrong Verification Code.');
		}
	}
}
