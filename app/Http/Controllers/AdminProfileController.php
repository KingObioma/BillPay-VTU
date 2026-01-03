<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class AdminProfileController extends Controller
{
	use Upload;

	public function index(Request $request)
	{
		$admin = Auth::guard('admin')->user();
		$adminProfile = AdminProfile::firstOrCreate(['admin_id' => $admin->id]);

		$data['admin'] = $admin;
		$data['adminProfile'] = $adminProfile;

		if ($request->isMethod('get')) {
			return view('admin.profile.show', $data);
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());
			$purifiedData['profile_picture'] = $request->profile_picture;
			$validator = Validator::make($purifiedData, [
				'name' => 'required|min:3|max:100|string',
				'username' => 'required|min:3|max:100|alpha_dash',
				'email' => 'required|email|max:100',
				'city' => 'required|min:3|max:32|string',
				'state' => 'required|min:3|max:32|string',
				'phone' => 'required|max:32',
				'address' => 'nullable|max:250',
				'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:5000'
			]);
			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$purifiedData = (object)$purifiedData;

			$admin->name = $purifiedData->name;
			$admin->email = $purifiedData->email;
			$admin->username = $purifiedData->username;
			$adminProfile->city = $purifiedData->city;
			$adminProfile->state = $purifiedData->state;
			$adminProfile->phone = $purifiedData->phone;
			$adminProfile->address = $purifiedData->address;

			if ($request->file('profile_picture') && $request->file('profile_picture')->isValid()) {
				$extension = $request->profile_picture->extension();
				$profileName = strtolower($admin->username . '.' . $extension);
				$image = $this->fileUpload($request->profile_picture, config('location.admin.path'), $adminProfile->driver, $profileName, $adminProfile->profile_picture);
				if ($image) {
					$adminProfile->profile_picture = $image['path'];
					$adminProfile->driver = $image['driver'];
				}
			}

			$admin->save();
			$adminProfile->save();

			return back()->with('success', 'Profile Update Successfully');
		}
	}
}
