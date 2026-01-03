<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\NotifyTemplate;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class NotifyController extends Controller
{
	public function index()
	{
		$notifyTemplates = NotifyTemplate::groupBy('template_key')->distinct()->orderBy('template_key')->get();
		return view('admin.notify.show', compact('notifyTemplates'));
	}

	public function edit($id)
	{
		$notifyTemplate = NotifyTemplate::findOrFail($id);
		$languages = Language::orderBy('short_name')->get();
		if ($notifyTemplate->notify_for == 0) {
			foreach ($languages as $lang) {
				$checkTemplate = NotifyTemplate::where('template_key', $notifyTemplate->template_key)->where('language_id', $lang->id)->count();

				if ($lang->short_name == 'en' && ($notifyTemplate->language_id == null)) {
					$notifyTemplate->language_id = $lang->id;
					$notifyTemplate->short_name = $lang->short_name;
					$notifyTemplate->save();
				}

				if (0 == $checkTemplate) {
					$template = new  NotifyTemplate();
					$template->language_id = $lang->id;
					$template->name = $notifyTemplate->name;
					$template->template_key = $notifyTemplate->template_key;
					$template->body = $notifyTemplate->body;
					$template->short_keys = $notifyTemplate->short_keys;
					$template->status = $notifyTemplate->status;
					$template->lang_code = $lang->short_name;
					$template->save();
				}
			}
		}

		$templates = NotifyTemplate::where('template_key', $notifyTemplate->template_key)->with('language')->get();
		return view('admin.notify.edit', compact('notifyTemplate', 'languages', 'templates'));
	}

	public function update(Request $request, $id)
	{
		$templateData = Purify::clean($request->all());

		$rules = [
			'body' => 'sometimes|required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}
		$template = NotifyTemplate::findOrFail($id);
		$template->status = $templateData['status'];
		$template->body = $templateData['body'];
		$template->save();

		return back()->with('success', 'Update Successfully');
	}
}
