<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\NotifyTemplate;
use App\Models\UserProfile;
use App\Traits\ApiValidation;
use App\Traits\Notify;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
	use ApiValidation, Notify;

	public function registerUserForm()
	{
		try {
			if (basicControl()->registration == 0) {
				return response()->json($this->withErrors("You are not authorized"));
			}

			$info = json_decode(json_encode(getIpInfo()), true);
			$country_code = null;
			if (!empty($info['code'])) {
				$data['country_code'] = @$info['code'][0];
			}
			$data['countries'] = config('country');
			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}

	public function registerUser(Request $request)
	{
		if (basicControl()->registration == 0) {
			return response()->json($this->withErrors("You are not authorized"));
		}

		$basic = (object)config('basic');
		try {
			$validateUser = Validator::make($request->all(),
				[
					'name' => 'required|string|max:255',
					'email' => 'required|string|email|max:255|unique:users,email',
					'username' => 'required|string|max:50|min:3|unique:users,username',
					'password' => 'required|string|min:6|confirmed',
					'phone' => 'required|string|min:6|unique:user_profiles,phone'
				]);

			if ($validateUser->fails()) {
				return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
			}

			if ($request->sponsor != null) {
				$sponsorId = User::where('username', $request->sponsor)->first();
			} else {
				$sponsorId = null;
			}
			$languageId = Language::select('id')->where('default_status', 1)->first() ?? null;

			$user = User::create([
				'name' => $request->name,
				'referral_id' => ($sponsorId != null) ? $sponsorId->id : null,
				'email' => $request->email,
				'username' => $request->username,
				'password' => Hash::make($request->password),
				'language_id' => $languageId ? $languageId->id : null,
				'email_verification' => (basicControl()->email_verification) ? 0 : 1,
				'sms_verification' => (basicControl()->sms_verification) ? 0 : 1,
			]);

			$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
			$userProfile->phone_code = '+' . $request->phone_code;
			$userProfile->phone = $request->phone;
			$userProfile->save();

			$user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
			$user->save();

			$email_templates = EmailTemplate::where('mail_status', 1)->orWhere('sms_status', 1)->groupBy('template_key')
				->pluck('template_key');

			$notify_templates = NotifyTemplate::where('status', 1)->orWhere('firebase_notify_status', 1)
				->groupBy('template_key')->pluck('template_key');

			$user->email_key = $email_templates;
			$user->sms_key = $email_templates;
			$user->push_key = $notify_templates;
			$user->in_app_key = $notify_templates;
			$user->save();


			return response()->json([
				'status' => 'success',
				'message' => 'User Created Successfully',
				'token' => $user->createToken("API TOKEN")->plainTextToken
			]);


		} catch (\Throwable $th) {
			return response()->json($this->withErrors($th->getMessage()));
		}
	}

	public function loginUser(Request $request)
	{
		try {
			$validateUser = Validator::make($request->all(),
				[
					'username' => 'required|string',
					'password' => 'required|string'
				]);

			if ($validateUser->fails()) {
				return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
			}

			if (!Auth::attempt($request->only(['username', 'password']))) {
				return response()->json($this->withErrors('Username & Password does not match with our record.'));
			}

			$user = User::where('username', $request->username)->first();
			if (!$user) {
				return response()->json($this->withErrors('record not found'));
			}
			$user->last_login = Carbon::now();
			$user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
			$user->save();

			if ($user->status == 0) {
				return response()->json($this->withErrors('You are banned from this application.Please contact with the administration'));
			}
			return response()->json([
				'status' => 'success',
				'message' => 'User Logged In Successfully',
				'token' => $user->createToken("API TOKEN")->plainTextToken
			]);

		} catch (\Throwable $th) {
			return response()->json($this->withErrors($th->getMessage()));
		}
	}

	public function getEmailForRecoverPass(Request $request)
	{
		$validateUser = Validator::make($request->all(),
			[
				'email' => 'required|email',
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}

		try {
			$user = User::where('email', $request->email)->first();
			if (!$user) {
				return response()->json($this->withErrors('Email does not exit on record'));
			}

			$code = rand(10000, 99999);
			$data['email'] = $request->email;
			$data['message'] = 'OTP has been send';
			$user->verify_code = $code;
			$user->save();

			$basic = basicControl();
			$message = 'Your Password Recovery Code is ' . $code;
			$email_from = $basic->sender_email;
			@Mail::to($request->email)->send(new SendMail($email_from, "Recovery Code", $message));

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function getCodeForRecoverPass(Request $request)
	{
		$validateUser = Validator::make($request->all(),
			[
				'code' => 'required',
				'email' => 'required|email',
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}

		try {
			$user = User::where('email', $request->email)->first();
			if (!$user) {
				return response()->json($this->withErrors('Email does not exit on record'));
			}

			if ($user->verify_code == $request->code && $user->updated_at > Carbon::now()->subMinutes(5)) {
				$user->verify_code = null;
				$user->save();
				return response()->json($this->withSuccess('Code Matching'));
			}

			return response()->json($this->withErrors('Invalid Code'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function updatePass(Request $request)
	{
		if (config('basic.strong_password') == 0) {
			$rules['password'] = ['required', 'min:6', 'confirmed'];
		} else {
			$rules['password'] = ["required", 'confirmed',
				Password::min(6)->mixedCase()
					->letters()
					->numbers()
					->symbols()
					->uncompromised()];
		}
		$rules['email'] = ['required', 'email'];

		$validateUser = Validator::make($request->all(), $rules);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}

		$user = User::where('email', $request->email)->first();
		if (!$user) {
			return response()->json($this->withErrors('Email does not exist on record'));
		}
		$user->password = Hash::make($request->password);
		$user->save();
		return response()->json($this->withSuccess('Password Updated'));
	}

}
