<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class SmsTemplateController extends Controller
{
	public function index()
	{
		$smsTemplates = SmsTemplate::groupBy('template_key')->distinct()->orderBy('template_key')->get();
		return view('admin.smsTemplate.show', compact('smsTemplates'));
	}

	public function edit($id)
	{
		$smsTemplate = SmsTemplate::findOrFail($id);
		$languages = Language::orderBy('short_name')->get();

		foreach ($languages as $lang) {
			$checkTemplate = EmailTemplate::where('template_key', $smsTemplate->template_key)->where('language_id', $lang->id)->count();

			if ($lang->short_name == 'en' && ($smsTemplate->language_id == null)) {
				$smsTemplate->language_id = $lang->id;
				$smsTemplate->lang_code = $lang->short_name;
				$smsTemplate->save();
			}

			if (0 == $checkTemplate) {
				$template = new  EmailTemplate();
				$template->language_id = $lang->id;
				$template->template_key = $smsTemplate->template_key;
				$template->name = $smsTemplate->name;
				$template->subject = $smsTemplate->subject;
				$template->template = $smsTemplate->template;
				$template->sms_body = $smsTemplate->sms_body;
				$template->short_keys = $smsTemplate->short_keys;
				$template->mail_status = $smsTemplate->mail_status;
				$template->sms_status = $smsTemplate->sms_status;
				$template->lang_code = $lang->short_name;
				$template->save();
			}
		}

		$smsTemplates = EmailTemplate::where('template_key', $smsTemplate->template_key)->with('language')->get();

		return view('admin.smsTemplate.edit', compact('smsTemplate', 'smsTemplates'));
	}

	public function update(Request $request, $id)
	{
		$req = Purify::clean($request->all());
		$smsTemplate = SmsTemplate::findOrFail($id);
		$smsTemplate->sms_status = $req['sms_status'];
		$smsTemplate->sms_body = $req['sms_body'];
		$smsTemplate->save();
		return back()->with('success', 'Successfully Updated');
	}
}
