<?php

namespace App\Traits;

use App\Events\AdminNotification;
use App\Events\UserNotification;
use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\FirebaseNotify;
use App\Models\NotifyTemplate;
use App\Models\SiteNotification;
use App\Models\SmsControl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Facades\App\Services\BasicCurl;

trait Notify
{
	public function sendMailSms($user, $templateKey, $params = [], $subject = null, $requestMessage = null)
	{
		$this->mail($user, $templateKey, $params, $subject, $requestMessage);
		$this->sms($user, $templateKey, $params, $requestMessage = null);
	}

	public function mail($user, $templateKey = null, $params = [], $subject = null, $requestMessage = null)
	{
		$basic = basicControl();

		if ($basic->email_notification != 1) {
			return false;
		}

		if ($user->email_key != null && in_array($templateKey, $user->email_key) == false) {
			return false;
		}

		$email_body = $basic->email_description;
		$templateObj = EmailTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('mail_status', 1)->first();
		if (!$templateObj) {
			$templateObj = EmailTemplate::where('template_key', $templateKey)->where('mail_status', 1)->first();
		}
		$message = str_replace("[[name]]", $user->username, $email_body);

		if (!$templateObj && $subject == null) {
			return false;
		} else {
			if ($templateObj) {
				$message = str_replace("[[message]]", $templateObj->template, $message);
				if (empty($message)) {
					$message = $email_body;
				}
				foreach ($params as $code => $value) {
					$message = str_replace('[[' . $code . ']]', $value, $message);
				}
			} else {
				$message = str_replace("[[message]]", $requestMessage, $message);
			}
		}

		$subject = ($subject == null) ? $templateObj->subject : $subject;
		$email_from = ($templateObj) ? $templateObj->email_from : $basic->sender_email;

		Mail::to($user)->queue(new SendMail($email_from, $subject, $message));
	}

	public function sms($user, $templateKey, $params = [], $requestMessage = null)
	{
		$basic = basicControl();
		if ($basic->sms_notification != 1 || !$user->profile) {
			return false;
		}

		if ($user->sms_key != null && in_array($templateKey, $user->sms_key) == false) {
			return false;
		}

		$smsControl = SmsControl::firstOrCreate(['id' => 1]);

		$templateObj = EmailTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('sms_status', 1)->first();
		if (!$templateObj) {
			$templateObj = EmailTemplate::where('template_key', $templateKey)->where('sms_status', 1)->first();
		}

		if (!$templateObj && $requestMessage == null) {
			return false;
		} else {
			if ($templateObj) {
				$template = $templateObj->sms_body;
				foreach ($params as $code => $value) {
					$template = str_replace('[[' . $code . ']]', $value, $template);
				}
			} else {
				$template = $requestMessage;
			}
		}


		$headerData = is_null($smsControl->headerData) ? [] : json_decode($smsControl->headerData, true);
		$paramData = is_null($smsControl->paramData) ? [] : json_decode($smsControl->paramData, true);
		$paramData = http_build_query($paramData);
		$actionUrl = $smsControl->actionUrl;
		$actionMethod = $smsControl->actionMethod;
		$formData = is_null($smsControl->formData) ? [] : json_decode($smsControl->formData, true);
		$formData = isset($headerData['Content-Type']) && $headerData['Content-Type'] == "application/x-www-form-urlencoded" ? http_build_query($formData) : (isset($headerData['Content-Type']) && $headerData['Content-Type'] == "application/json" ? json_encode($formData) : $formData);

		foreach ($headerData as $key => $data) {
			$headerData[] = "{$key}:$data";
		}

		if ($actionMethod == 'GET') {
			$actionUrl = $actionUrl . '?' . $paramData;
		}

		$formData = recursive_array_replace("[[receiver]]", $user->phone, recursive_array_replace("[[message]]", $template, $formData));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $actionUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $actionMethod,
			CURLOPT_POSTFIELDS => $formData,
			CURLOPT_HTTPHEADER => $headerData
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return $response;


	}


	public function verifyToMail($user, $templateKey = null, $params = [], $subject = null, $requestMessage = null)
	{
		$basic = basicControl();

		if ($basic->email_verification != 1) {
			return false;
		}

		$email_body = $basic->email_description;
		$templateObj = EmailTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('mail_status', 1)->first();
		if (!$templateObj) {
			$templateObj = EmailTemplate::where('template_key', $templateKey)->where('mail_status', 1)->first();
		}
		$message = str_replace("[[name]]", $user->username, $email_body);

		if (!$templateObj && $subject == null) {
			return false;
		} else {
			if ($templateObj) {
				$message = str_replace("[[message]]", $templateObj->template, $message);
				if (empty($message)) {
					$message = $email_body;
				}
				foreach ($params as $code => $value) {
					$message = str_replace('[[' . $code . ']]', $value, $message);
				}
			} else {
				$message = str_replace("[[message]]", $requestMessage, $message);
			}
		}

		$subject = ($subject == null) ? $templateObj->subject : $subject;
		$email_from = ($templateObj) ? $templateObj->email_from : $basic->sender_email;

		Mail::to($user)->send(new SendMail($email_from, $subject, $message));
	}

	public function verifyToSms($user, $templateKey, $params = [], $requestMessage = null)
	{
		$basic = basicControl();
		if ($basic->sms_verification != 1 || !$user->profile) {
			return false;
		}

		$smsControl = SmsControl::firstOrCreate(['id' => 1]);

		$templateObj = EmailTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('sms_status', 1)->first();
		if (!$templateObj) {
			$templateObj = EmailTemplate::where('template_key', $templateKey)->where('sms_status', 1)->first();
		}

		if (!$templateObj && $requestMessage == null) {
			return false;
		} else {
			if ($templateObj) {
				$template = $templateObj->sms_body;
				foreach ($params as $code => $value) {
					$template = str_replace('[[' . $code . ']]', $value, $template);
				}
			} else {
				$template = $requestMessage;
			}
		}


		$headerData = is_null($smsControl->headerData) ? [] : json_decode($smsControl->headerData, true);
		$paramData = is_null($smsControl->paramData) ? [] : json_decode($smsControl->paramData, true);
		$paramData = http_build_query($paramData);
		$actionUrl = $smsControl->actionUrl;
		$actionMethod = $smsControl->actionMethod;
		$formData = is_null($smsControl->formData) ? [] : json_decode($smsControl->formData, true);
		$formData = isset($headerData['Content-Type']) && $headerData['Content-Type'] == "application/x-www-form-urlencoded" ? http_build_query($formData) : (isset($headerData['Content-Type']) && $headerData['Content-Type'] == "application/json" ? json_encode($formData) : $formData);

		foreach ($headerData as $key => $data) {
			$headerData[] = "{$key}:$data";
		}

		if ($actionMethod == 'GET') {
			$actionUrl = $actionUrl . '?' . $paramData;
		}

		$formData = recursive_array_replace("[[receiver]]", $user->phone, recursive_array_replace("[[message]]", $template, $formData));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $actionUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $actionMethod,
			CURLOPT_POSTFIELDS => $formData,
			CURLOPT_HTTPHEADER => $headerData
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return $response;


	}

	public function userPushNotification($user, $templateKey, $params = [], $action = [])
	{
		$basic = basicControl();
		if ($basic->push_notification != 1) {
			return false;
		}

		if ($user->in_app_key != null && in_array($templateKey, $user->in_app_key) == false) {
			return false;
		}

		$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('status', 1)->first();
		if (!$templateObj) {
			$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('status', 1)->first();
		}

		if ($templateObj) {
			$template = $templateObj->body;
			foreach ($params as $code => $value) {
				$template = str_replace('[[' . $code . ']]', $value, $template);
			}
			$action['text'] = $template;
		}

		$siteNotification = new SiteNotification();
		$siteNotification->description = $action;
		$user->siteNotificational()->save($siteNotification);
		event(new UserNotification($siteNotification, $user->id));
	}

	public function userFirebasePushNotification($user, $templateKey, $params = [], $action = null)
	{
		try {
			if ($user->push_key != null && in_array($templateKey, $user->push_key) == false) {
				return false;
			}

			$notify = FirebaseNotify::first();
			if (!$notify) {
				return false;
			}

			if ($notify->user_foreground == 0 && $notify->user_background == 0) {
				return false;
			}

			$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('language_id', $user->language_id)->where('firebase_notify_status', 1)->where('status', 1)->first();
			if (!$templateObj) {
				$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('firebase_notify_status', 1)->first();
			}
			$template = '';
			if ($templateObj) {
				$template = $templateObj->body;
				foreach ($params as $code => $value) {
					$template = str_replace('[[' . $code . ']]', $value, $template);
				}
			}

			$data = [
				"to" => $user->fcm_token,
				"notification" => [
					"title" => $templateObj->name . ' from ' . config('basic.site_title'),
					"body" => $template,
					"icon" => getFile(config('basic.default_file_driver'), config('basic.favicon_image')),
				],
				"data" => [
					"foreground" => (int)$notify->user_foreground,
					"background" => (int)$notify->user_background,
					"click_action" => $action
				],
				"content_available" => true,
				"mutable_content" => true
			];

			$response = Http::withHeaders([
				'Authorization' => 'key=' . $notify->server_key
			])
				->acceptJson()
				->post('https://fcm.googleapis.com/fcm/send', $data);
		} catch (\Exception $e) {
			return 0;
		}
	}

	public function adminPushNotification($templateKey, $params = [], $action = [])
	{
		$basic = basicControl();
		if ($basic->push_notification != 1) {
			return false;
		}

		$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('status', 1)->first();
		if (!$templateObj) {
			return false;
		}

		if ($templateObj) {
			$template = $templateObj->body;
			foreach ($params as $code => $value) {
				$template = str_replace('[[' . $code . ']]', $value, $template);
			}
			$action['text'] = $template;
		}

		$admins = Admin::all();
		foreach ($admins as $admin) {
			$siteNotification = new SiteNotification();
			$siteNotification->description = $action;
			$admin->siteNotificational()->save($siteNotification);
			event(new AdminNotification($siteNotification, $admin->id));
		}
	}

	public function adminFirebasePushNotification($templateKey, $params = [], $action = null)
	{
		try {
			$notify = FirebaseNotify::first();
			if (!$notify) {
				return false;
			}

			$templateObj = NotifyTemplate::where('template_key', $templateKey)->where('firebase_notify_status', 1)->first();
			if (!$templateObj) {
				return false;
			}

			$template = '';
			if ($templateObj) {
				$template = $templateObj->body;
				foreach ($params as $code => $value) {
					$template = str_replace('[[' . $code . ']]', $value, $template);
				}
			}

			$admins = Admin::all();
			foreach ($admins as $admin) {
				$data = [
					"to" => $admin->fcm_token,
					"notification" => [
						"title" => $templateObj->name,
						"body" => $template,
						"icon" => getFile(config('basic.default_file_driver'), config('basic.favicon_image')),
						"data" => [
							"foreground" => (int)$notify->admin_foreground,
							"background" => (int)$notify->admin_background,
							"click_action" => $action
						],
						"content_available" => true,
						"mutable_content" => true
					]
				];

				$response = Http::withHeaders([
					'Authorization' => 'key=' . $notify->server_key
				])
					->acceptJson()
					->post('https://fcm.googleapis.com/fcm/send', $data);
			}
		} catch (\Exception $e) {
			return 0;
		}
	}

	public function adminMail($templateKey = null, $params = [], $subject = null, $requestMessage = null)
	{
		$basic = basicControl();

		if ($basic->email_notification != 1) {
			return false;
		}

		$email_body = $basic->email_description;
		$templateObj = EmailTemplate::where('template_key', $templateKey)->where('mail_status', 1)->first();
		if (!$templateObj) {
			$templateObj = EmailTemplate::where('template_key', $templateKey)->where('mail_status', 1)->first();
		}

		$message = $email_body;
		if ($templateObj) {
			$message = str_replace("[[message]]", $templateObj->template, $message);
			if (empty($message)) {
				$message = $email_body;
			}
			foreach ($params as $code => $value) {
				$message = str_replace('[[' . $code . ']]', $value, $message);
			}
		} else {
			$message = str_replace("[[message]]", $requestMessage, $message);
		}

		$subject = ($subject == null) ? $templateObj->subject : $subject;
		$email_from = ($templateObj) ? $templateObj->email_from : $basic->sender_email;

		$admins = Admin::all();
		foreach ($admins as $admin) {
			$message = str_replace("[[name]]", $admin->username, $message);
			Mail::to($admin)->queue(new SendMail($email_from, $subject, $message));
		}
	}
}
