<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\IdentifyForm;
use App\Models\Kyc;
use App\Models\Language;
use App\Models\NotifyTemplate;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\UserProfile;
use App\Traits\ApiValidation;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stevebauman\Purify\Facades\Purify;

class ProfileController extends Controller
{
	use ApiValidation, Notify, Upload;

	public function profile()
	{
		try {
			$user = auth()->user();
			$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);

			$data['userImage'] = $user->profilePicture();
			$data['name'] = trans(ucfirst($user->name));
			$data['city'] = $userProfile->city;
			$data['username'] = $user->username;
			$data['email'] = $user->email;
			$data['phoneCode'] = $userProfile->phone_code;
			$data['phone'] = $userProfile->phone;
			$data['address'] = $userProfile->address;
			$data['userLanguageId'] = $user->language_id;

			$data['languages'] = Language::all();
			return response()->json($this->withSuccess($data));

		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}

	public function profileImageUpload(Request $request)
	{
		$user = Auth::user();
		$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);

		try {
			if ($request->file('profile_picture') && $request->file('profile_picture')->isValid()) {
				$extension = $request->profile_picture->extension();
				$profileName = strtolower($user->username . '.' . $extension);
				$image = $this->fileUpload($request->profile_picture, config('location.user.path'), $userProfile->driver, $profileName, $userProfile->profile_picture);
				if ($image) {
					$userProfile->profile_picture = $image['path'];
					$userProfile->driver = $image['driver'];
				}
			} else {
				return response()->json($this->withErrors('Please select a image'));
			}
			$userProfile->save();
			return response()->json($this->withSuccess('Updated Successfully.'));
		} catch (\Exception $exception) {
			return response()->json($this->withErrors($exception->getMessage()));
		}
	}

	public function profileInfoUpdate(Request $request)
	{
		try {

			$user = auth()->user();
			$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
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
				return response()->json($this->withErrors(collect($validator->errors())->collapse()[0]));
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

			$user->save();
			$userProfile->save();

			return response()->json($this->withSuccess('Profile Update Successfully'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function profilePassUpdate(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$validator = Validator::make($purifiedData, [
			'currentPassword' => 'required|min:5',
			'password' => 'required|min:8|confirmed',
		]);
		if ($validator->fails()) {
			return response()->json($this->withErrors(collect($validator->errors())->collapse()[0]));
		}

		$user = auth()->user();
		$purifiedData = (object)$purifiedData;
		try {
			if (!Hash::check($purifiedData->currentPassword, $user->password)) {
				return response()->json($this->withErrors('Current password did not match'));
			}
			$user->password = bcrypt($purifiedData->password);
			$user->save();
			return response()->json($this->withSuccess('Password updated successfully'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function profileIdentityVerification()
	{
		try {
			$data['kyc'] = Kyc::first();
			if (!$data['kyc']) {
				return response()->json($this->withErrors('No data available'));
			}

			$user = auth()->user();

			switch ($user) {
				case $user->kyc_verified == 1:
					$data['msg'] = 'Your kyc verification process in pending';
					break;
				case $user->kyc_verified == 2:
					$data['msg'] = 'Your kyc is verified';
					break;
				case $user->kyc_verified == 3:
					$data['msg'] = 'Your previous kyc is rejected';
					break;
				default:
					$data['msg'] = 'Verify your process instantly';
			}

			return response()->json($this->withSuccess($data));
		} catch (\Exception $exception) {
			return response()->json($exception->getMessage());
		}
	}

	public function profileIdentityVerificationSubmit(Request $request)
	{
		try {
			$kyc = Kyc::first();
			if (!$kyc) {
				return response()->json($this->withErrors('Data not found'));
			}
			$params = $kyc->input_form;
			$userKyc = new UserKyc();

			$rules = [];
			$inputField = [];

			$verifyImages = [];

			if ($params != null) {
				foreach ($params as $key => $cus) {
					$rules[$key] = [$cus->validation];
					if ($cus->type == 'file') {
						array_push($rules[$key], 'image');
						array_push($rules[$key], 'mimes:jpeg,jpg,png');
						array_push($rules[$key], 'max:10000');
						array_push($verifyImages, $key);
					}
					if ($cus->type == 'text') {
						array_push($rules[$key], 'max:191');
					}
					if ($cus->type == 'textarea') {
						array_push($rules[$key], 'max:300');
					}
					$inputField[] = $key;
				}
			}

			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return response()->json($this->withErrors(collect($validator->errors())->collapse()[0]));
			}
			$reqField = [];
			$path = config('location.kyc.path');
			$collection = collect($request);

			if ($params != null) {
				foreach ($collection as $k => $v) {
					foreach ($params as $inKey => $inVal) {
						if ($k != $inKey) {
							continue;
						} else {
							if ($inVal->type == 'file') {
								if ($request->hasFile($inKey)) {
									try {
										$reqField[$inKey] = [
											'field_name' => $inVal->field_level,
											'field_value' => $this->fileUpload($request[$inKey], $path),
											'type' => $inVal->type,
										];
									} catch (\Exception $exp) {
										return response()->json($this->withErrors('Could not upload your ' . $inKey));
									}
								}
							} else {
								$reqField[$inKey] = [
									'field_name' => $inVal->field_level,
									'field_value' => $v,
									'type' => $inVal->type,
								];
							}
						}
					}
				}
				$userKyc->kyc_info = $reqField;
			} else {
				$userKyc->kyc_info = null;
			}
			$user = Auth::user();
			DB::beginTransaction();
			$userKyc->user_id = $user->id;
			$userKyc->save();

			$user->kyc_verified = 1;
			$user->save();
			DB::commit();
			return response()->json($this->withSuccess('KYC Submitted'));
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function notification()
	{
		try {
			$email_templates = EmailTemplate::select(['template_key', 'sms_status', 'mail_status', 'name'])
				->where('mail_status', 1)->orWhere('sms_status', 1)->groupBy('template_key')
				->get()->map(function ($query) {
					$email_templates = new EmailTemplate();
					$email_templates->template_key = $query->template_key;
					$email_templates->name = $query->name;
					$email_templates->sms_status = $query->sms_status;
					$email_templates->mail_status = $query->mail_status;
					if (NotifyTemplate::where('template_key', $query->template_key)->exists()) {
						$email_templates->in_app_status = 1;
						$email_templates->push_status = 1;
					} else {
						$email_templates->in_app_status = 0;
						$email_templates->push_status = 0;
					}
					return $email_templates;
				});

			$temKeys = [];
			foreach ($email_templates as $email_template) {
				$temKeys[] = $email_template->template_key;
			}

			$notify_templates = NotifyTemplate::select(['template_key', 'status', 'firebase_notify_status', 'name'])
				->where('status', 1)->orWhere('firebase_notify_status', 1)->groupBy('template_key')->get()->map(function ($query) {
					return [
						'template_key' => $query->template_key,
						'name' => $query->name,
						'sms_status' => 0,
						'mail_status' => 0,
						'in_app_status' => $query->status,
						'push_status' => $query->firebase_notify_status,
					];
				});
			$notify_templates = $notify_templates->whereNotIn('template_key', $temKeys);

			$data['allTemplates'] = array_merge($email_templates->toArray(), $notify_templates->toArray());

			$data['userActiveEmail'] = auth()->user()->email_key ?? [];
			$data['userActiveSms'] = auth()->user()->sms_key ?? [];
			$data['userActivePush'] = auth()->user()->push_key ?? [];
			$data['userActiveInApp'] = auth()->user()->in_app_key ?? [];
			return response()->json($this->withSuccess($data));
		} catch (\Exception $exception) {
			return response()->json($this->withErrors($exception->getMessage()));
		}
	}

	public function notificationSubmit(Request $request)
	{
		$user = auth()->user();
		try {
			$user->email_key = $request->email_key;
			$user->sms_key = $request->sms_key;
			$user->push_key = $request->push_key;
			$user->in_app_key = $request->in_app_key;
			$user->save();
			return response()->json($this->withSuccess('Updated Successfully'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}
}
