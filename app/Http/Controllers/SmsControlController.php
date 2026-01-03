<?php

namespace App\Http\Controllers;

use App\Models\SmsControl;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class SmsControlController extends Controller
{
	public function smsConfig(Request $request)
	{
		$basicControl = basicControl();
		if ($request->isMethod('GET')) {
			$smsControl = SmsControl::firstOrCreate(['id' => 1]);
			return view('admin.control_panel.smsConfig', compact('smsControl', 'basicControl'));
		} elseif ($request->isMethod('POST')) {

			$purifiedData = Purify::clean($request->all());

			$validator = Validator::make($purifiedData, [
				'actionMethod' => 'required|min:3|max:4',
				'actionUrl' => 'required|url',
				'headerDataKeys.*' => 'nullable|string|min:2|required_with:headerDataValues.*',
				'headerDataValues.*' => 'nullable|string|min:2|required_with:headerDataKeys.*',
				'paramKeys.*' => 'nullable|string|min:2|required_with:paramValues.*',
				'paramValues.*' => 'nullable|string|min:2|required_with:paramKeys.*',
				'formDataKeys.*' => 'nullable|string|min:2|required_with:formDataValues.*',
				'formDataValues.*' => 'nullable|string|min:2|required_with:formDataKeys.*',
				'sms_notification' => 'nullable|integer|min:0|in:0,1',
				'sms_verification' => 'nullable|integer|min:0|in:0,1',
			], [
				'min' => 'Field value must be at least :min characters.',
				'string' => 'Field value must be :string.',
				'required_with' => 'Field value empty not allowed',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$purifiedData = (object)$purifiedData;

			$headerData = array_combine($purifiedData->headerDataKeys, $purifiedData->headerDataValues);
			$paramData = array_combine($purifiedData->paramKeys, $purifiedData->paramValues);
			$formData = array_combine($purifiedData->formDataKeys, $purifiedData->formDataValues);

			$headerData = (empty(array_filter($headerData))) ? null : json_encode(array_filter($headerData));
			$paramData = (empty(array_filter($paramData))) ? null : json_encode(array_filter($paramData));
			$formData = (empty(array_filter($formData))) ? null : json_encode(array_filter($formData));

			$actionMethod = $purifiedData->actionMethod;
			$actionUrl = $purifiedData->actionUrl;

			$smsControl = SmsControl::firstOrCreate(['id' => 1]);
			$smsControl->actionUrl = $actionUrl;
			$smsControl->actionMethod = $actionMethod;
			$smsControl->formData = $formData;
			$smsControl->paramData = $paramData;
			$smsControl->headerData = $headerData;
			$smsControl->save();

			$basicControl->sms_notification = $purifiedData->sms_notification;
			$basicControl->sms_verification = $purifiedData->sms_verification;
			$basicControl->save();

			return back()->with('success', 'SMS Configuration Saved');
		}
	}
}
