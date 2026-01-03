<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\NotifyTemplate;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;


class SocialiteController extends Controller
{
	use Upload;

	public function socialiteLogin($socialite)
	{
		if (config('basic.' . $socialite . '_status')) {
			return Socialite::driver($socialite)->redirect();
		}
		return redirect()->route('login');
	}

	public function socialiteCallback($socialite)
	{
		try {
			$user = Socialite::driver($socialite)->user();
			$columName = $socialite . '_id';
			$searchUser = User::where($columName, $user->id)->first();

			if ($searchUser) {
				Auth::login($searchUser);
				return redirect('user/dashboard');

			} else {
				$languageId = Language::select('id')->where('default_status', 1)->first()->id ?? null;

				$newUser = User::create([
					'name' => $user->name,
					'email' => $user->email,
					'username' => $user->email,
					'password' => Hash::make($user->name),
					$columName => $user->id,
					'language_id' => $languageId,
					'email_verification' => (basicControl()->email_verification) ? 0 : 1,
					'sms_verification' => (basicControl()->sms_verification) ? 0 : 1,
				]);

				$this->extraWorkWithRegister($newUser);
				Auth::login($newUser);
				return redirect('user/dashboard');
			}

		} catch (\Exception $e) {
			return redirect()->route('login');
		}
	}

	public function extraWorkWithRegister($newUser): void
	{
		$newUser->two_fa_verify = ($newUser->two_fa == 1) ? 0 : 1;
		$newUser->save();

		$email_templates = EmailTemplate::where('mail_status', 1)->orWhere('sms_status', 1)->groupBy('template_key')
			->pluck('template_key');

		$notify_templates = NotifyTemplate::where('status', 1)->orWhere('firebase_notify_status', 1)
			->groupBy('template_key')->pluck('template_key');

		$newUser->email_key = $email_templates;
		$newUser->sms_key = $email_templates;
		$newUser->push_key = $notify_templates;
		$newUser->in_app_key = $notify_templates;
		$newUser->save();
	}
}
