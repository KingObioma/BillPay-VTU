<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\NotifyTemplate;
use App\Models\UserProfile;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
	use Upload;

	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function changePassword(Request $request)
	{
		if ($request->isMethod('get')) {
			return view($this->theme . 'user.profile.change');
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());
			$validator = Validator::make($purifiedData, [
				'currentPassword' => 'required|min:5',
				'password' => 'required|min:8|confirmed',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$user = Auth::user();
			$purifiedData = (object)$purifiedData;

			if (!Hash::check($purifiedData->currentPassword, $user->password)) {
				return back()->withInput()->withErrors(['currentPassword' => 'current password did not match']);
			}

			$user->password = bcrypt($purifiedData->password);
			$user->save();
			return back()->with('success', 'Password changed successfully');
		}
	}

	public function index(Request $request)
	{
		$user = Auth::user();
		$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
		$countries = config('country');
		$country_code = $userProfile->phone_code;
		if ($request->isMethod('get')) {
			$languages = Language::select('id', 'name')->where('is_active', true)->orderBy('name', 'ASC')->get();
			return view($this->theme . 'user.profile.show', compact('country_code', 'user', 'userProfile', 'countries', 'languages'));
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());

			$validator = Validator::make($purifiedData, [
				'name' => 'required|min:3|max:100|string',
				'city' => 'required|min:3|max:32|string',
				'username' => 'sometimes|required|min:5|max:50|unique:users,username,' . $user->id,
				'email' => 'sometimes|required|min:5|max:50|unique:users,email,' . $user->id,
				'language' => 'required|integer|not_in:0|exists:languages,id',
				'address' => 'nullable|max:250',
			]);

			if ($validator->fails()) {
				$validator->errors()->add('profile', '1');
				return back()->withErrors($validator)->withInput();
			}
			$purifiedData = (object)$purifiedData;
			if ($purifiedData->email != $user->email) {
				$user->email_verification = 0;
			}
			if ($purifiedData->phone != $userProfile->phone) {
				$user->sms_verification = 0;
			}

			$user->name = $purifiedData->name;
			$user->username = $purifiedData->username;
			$user->email = $purifiedData->email;
			$userProfile->city = $purifiedData->city;
			$userProfile->address = $purifiedData->address;
			$userProfile->phone = $purifiedData->phone;
			$userProfile->phone_code = $purifiedData->phone_code ?? $userProfile->phone_code;
			$user->language_id = $purifiedData->language;


			if ($request->file('profile_picture') && $request->file('profile_picture')->isValid()) {
				$extension = $request->profile_picture->extension();
				$profileName = strtolower($user->username . '.' . $extension);
				$image = $this->fileUpload($request->profile_picture, config('location.user.path'), $userProfile->driver, $profileName, $userProfile->profile_picture);
				if ($image) {
					$userProfile->profile_picture = $image['path'];
					$userProfile->driver = $image['driver'];
				}
			}

			$user->save();
			$userProfile->save();

			return back()->with('success', 'Profile Update Successfully');
		}
	}

	public function notification(Request $request)
	{
		if ($request->method() == 'GET') {
			$email_templates = EmailTemplate::select(['template_key', 'sms_status', 'mail_status', 'name'])
				->where('mail_status', 1)->orWhere('sms_status', 1)->groupBy('template_key')
				->get()->map(function ($query) {

					$email_templates = new EmailTemplate();
					$email_templates->template_key = $query->template_key;
					$email_templates->sms_status = $query->sms_status;
					$email_templates->mail_status = $query->mail_status;
					$email_templates->name = $query->name;
					if (NotifyTemplate::where('template_key', $query->template_key)->exists()) {
						$email_templates->global = true;
					}
					return $email_templates;
				});

			$temKeys = [];
			foreach ($email_templates as $email_template) {
				$temKeys[] = $email_template->template_key;
			}

			$notify_templates = NotifyTemplate::select(['template_key', 'status', 'firebase_notify_status', 'name'])
				->where('status', 1)->orWhere('firebase_notify_status', 1)->groupBy('template_key')->get();
			$notify_templates = $notify_templates->whereNotIn('template_key', $temKeys);

			$allTemplates = array_merge($email_templates->toArray(), $notify_templates->toArray());
			return view($this->theme . 'user.notification.show', compact('allTemplates'));
		} elseif ($request->method() == 'POST') {
			$user = $this->user;
			$user->email_key = $request->email_key;
			$user->sms_key = $request->sms_key;
			$user->push_key = $request->push_key;
			$user->in_app_key = $request->in_app_key;
			$user->save();
			return back()->with('success', 'Updated Successfully');
		}
	}
}
