<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\EmailTemplate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
	public function defaultTemplate(Request $request)
	{
		$basicControl = basicControl();
		if ($request->isMethod('get')) {
			return view('admin.emailTemplate.default', compact('basicControl'));
		} elseif ($request->isMethod('post')) {
			$request->validate([
				'sender_email' => 'required|email',
				'sender_email_name' => 'required',
				'email_description' => 'required',
			]);


			$basicControl->sender_email = $request->sender_email;
			$basicControl->sender_email_name = $request->sender_email_name;
			$basicControl->email_description = $request->email_description;
			$basicControl->save();

			$envPath = base_path('.env');
			$env = file($envPath);
			$env = $this->set('MAIL_FROM_ADDRESS', $basicControl->sender_email, $env);
			$env = $this->set('MAIL_FROM_NAME', '"' . $basicControl->sender_email_name . '"', $env);
			$fp = fopen($envPath, 'w');
			fwrite($fp, implode($env));
			fclose($fp);


			$emailtemplates = EmailTemplate::get();
			foreach ($emailtemplates as $emailtemplate){
				$emailtemplate->email_from = $request->sender_email;
				$emailtemplate->save();
			}


			return back()->with('success', 'Email Updated Successfully');
		}
	}

	private function set($key, $value, $env)
	{
		foreach ($env as $env_key => $env_value) {
			$entry = explode("=", $env_value, 2);
			if ($entry[0] == $key) {
				$env[$env_key] = $key . "=" . $value . "\n";
			} else {
				$env[$env_key] = $env_value;
			}
		}
		return $env;
	}

	public function index()
	{
		$emailTemplates = EmailTemplate::groupBy('template_key')->distinct()->orderBy('template_key')->get();
		return view('admin.emailTemplate.show', compact('emailTemplates'));
	}

	public function edit($id)
	{
		$emailTemplate = EmailTemplate::findOrFail($id);
		$languages = Language::orderBy('short_name')->get();

		foreach ($languages as $lang) {
			$checkTemplate = EmailTemplate::where('template_key', $emailTemplate->template_key)->where('language_id', $lang->id)->count();

			if ($lang->short_name == 'en' && ($emailTemplate->language_id == null)) {
				$emailTemplate->language_id = $lang->id;
				$emailTemplate->lang_code = $lang->short_name;
				$emailTemplate->save();
			}
			if (0 == $checkTemplate) {
				$template = new  EmailTemplate();
				$template->language_id = $lang->id;
				$template->template_key = $emailTemplate->template_key;
				$template->email_from = $emailTemplate->email_from;
				$template->name = $emailTemplate->name;
				$template->subject = $emailTemplate->subject;
				$template->template = $emailTemplate->template;
				$template->sms_body = $emailTemplate->sms_body;
				$template->short_keys = $emailTemplate->short_keys;
				$template->mail_status = $emailTemplate->mail_status;
				$template->sms_status = $emailTemplate->sms_status;
				$template->lang_code = $lang->short_name;
				$template->save();
			}
		}

		$mailTemplates = EmailTemplate::where('template_key', $emailTemplate->template_key)->with('language')->get();

		return view('admin.emailTemplate.edit', compact('emailTemplate', 'languages', 'mailTemplates'));
	}

	public function update(Request $request, $id)
	{
		$templateData = Purify::clean($request->all());

		$rules = [
			'subject' => 'sometimes|required',
			'email_from' => 'sometimes|required',
			'mail_status' => 'nullable|integer|min:0|in:0,1',
		];
		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		$emailtemplate = EmailTemplate::findOrFail($id);
		$emailtemplate->mail_status = $templateData['mail_status'];
		$emailtemplate->subject = $templateData['subject'];
		$emailtemplate->email_from = $templateData['email_from'];
		$emailtemplate->template = $templateData['template'];
		$emailtemplate->save();
		return back()->with('success', 'Update Successfully');
	}

	public function testEmail(Request $request){
		$rules = [
			'email' => 'required|email',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		$basic = basicControl();

		if ($basic->email_notification != 1) {
			return back()->with('warning', 'Your email notification is disabled');
		}


		$email_from = $basic->sender_email;
		Mail::to($request->email)->send(new SendMail($email_from, "Test Email", "Your " . $_SERVER['SERVER_NAME'] . " email is working."));

		return back()->with('success', 'Email has been sent successfully.');
	}
}
