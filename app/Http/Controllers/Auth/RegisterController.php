<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\Fund;
use App\Models\Language;
use App\Models\NotifyTemplate;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
	use RegistersUsers;

	public function showRegistrationForm(Request $request)
	{
		if (basicControl()->registration == 0) {
			return back()->with('error', 'You are not authorized');
		}

		$referral = $request->referral;
		$info = json_decode(json_encode(getIpInfo()), true);
		$country_code = null;
		if (!empty($info['code'])) {
			$country_code = $info['code'][0];
		}
		$countries = config('country');
		$template = Template::where('section_name', 'register')->first();
		return view(template() . 'auth.register', compact('countries', 'referral', 'country_code', 'template'));
	}

	protected $redirectTo = RouteServiceProvider::HOME;

	public function __construct()
	{
		$this->middleware('guest');
	}

	protected function validator(array $data)
	{
		$basicControl = basicControl();
		$validateData = [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
			'username' => ['required', 'string', 'max:50', 'unique:users,username'],
			'password' => ['required', 'string', 'min:6', 'confirmed'],
			'phone' => ['required', 'string', 'unique:user_profiles,phone'],
		];

		// Recaptcha
		if ($basicControl->reCaptcha_status_login && ($basicControl->google_reCaptcha_status)) {
			$validateData['g-recaptcha-response'] = 'sometimes|required|captcha';
		}

		// Manual Recaptcha
		if (($basicControl->manual_reCaptcha_status == 1) && ($basicControl->reCaptcha_status_registration == 1)) {
			$validateData['captcha'] = ['required',
				Rule::when((!empty(request()->captcha) && strcasecmp(session()->get('captcha'), $_POST['captcha']) != 0), ['confirmed']),
			];
		}

		return Validator::make($data, $validateData, [
			'name.required' => 'Full Name field is required',
			'g-recaptcha-response.required' => 'The reCAPTCHA field is required',
		]);
	}

	protected function create(array $data)
	{
		if (basicControl()->registration == 0) {
			return back()->with('error', 'You are not authorized');
		}

		$ref_by = null;
		if (isset($data['referral'])) {
			$ref_by = User::where('username', $data['referral'])->first();
		}
		if (!isset($ref_by)) {
			$ref_by = null;
		}
		$languageId = Language::select('id')->where('default_status', 1)->first() ?? null;
		$user = User::create([
			'name' => $data['name'],
			'ref_by' => $ref_by,
			'email' => $data['email'],
			'username' => $data['username'],
			'password' => Hash::make($data['password']),
			'language_id' => $languageId->id,
			'email_verification' => 1,
			'sms_verification' => 1,
			// 'email_verification' => (basicControl()->email_verification) ? 0 : 1,
			// 'sms_verification' => (basicControl()->sms_verification) ? 0 : 1,
		]);

		$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
		$userProfile->phone_code = '+' . $data['phone_code'];
		$userProfile->phone = $data['phone'];
		$userProfile->save();

		return $user;
	}


	protected function registered(Request $request, $user)
	{
		// $user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
		$user->two_fa_verify = 1;
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
	}
}