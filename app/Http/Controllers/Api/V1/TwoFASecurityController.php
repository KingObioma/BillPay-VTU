<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Traits\ApiValidation;
use App\Traits\Notify;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoFASecurityController extends Controller
{
	use ApiValidation, Notify;

	public function twoFASecurity()
	{
		$basic = (object)config('basic');
		try {
			$ga = new GoogleAuthenticator();
			$data['twoFactorEnable'] = auth()->user()->two_fa == 0 ? false : true;
			$data['secret'] = $ga->createSecret();
			$data['qrCodeUrl'] = $ga->getQRCodeGoogleUrl(auth()->user()->username . '@' . $basic->site_title, $data['secret']);
			$data['previousCode'] = auth()->user()->two_fa_code;

			$data['previousQR'] = $ga->getQRCodeGoogleUrl(auth()->user()->username . '@' . $basic->site_title, $data['previousCode']);
			$data['downloadApp'] = 'https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en';
			$data['downloadAppIOS'] = 'https://apps.apple.com/us/app/google-authenticator/id388497605';
			return response()->json($this->withSuccess($data));

		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}

	public function twoFASecurityEnable(Request $request)
	{
		$user = auth()->user();
		$validateUser = Validator::make($request->all(),
			[
				'key' => 'required',
				'code' => 'required',
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}

		try {
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
					'browser' => @$browser->browserName() . ', ' . @$browser->platformName(),
					'time' => date('d M, Y h:i:s A'),
				]);
				return response()->json($this->withSuccess('Google Authenticator Has Been Enabled.'));
			} else {
				return response()->json($this->withErrors('Wrong Verification Code.'));
			}
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}

	public function twoFASecurityDisable(Request $request)
	{
		$validateUser = Validator::make($request->all(),
			[
				'code' => 'required',
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}

		try {
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
					'browser' => @$browser->browserName() . ', ' . @$browser->platformName(),
					'time' => date('d M, Y h:i:s A'),
				]);
				return response()->json($this->withSuccess('Google Authenticator Has Been Disabled.'));
			} else {
				return response()->json($this->withErrors('Wrong Verification Code.'));
			}
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}
}
