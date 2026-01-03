<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Template;
use App\Models\TemplateMedia;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class TemplateController extends Controller
{
	use Upload;


	public function show($section)
	{
		if (!array_key_exists($section, config('templates'))) {
			abort(404);
		}
		$languages = Language::all();
		$templates = Template::where('section_name', $section)->get()->groupBy('language_id');
		$templateMedia = TemplateMedia::where('section_name', $section)->first();

		return view('admin.template.show', compact('languages', 'section', 'templates', 'templateMedia'));
	}

	public function update(Request $request, $section, $language)
	{
		if (!array_key_exists($section, config('templates'))) {
			abort(404);
		}

		$purifiedData = Purify::clean($request->except('image', 'thumbnail', 'video', '_token', '_method'));

		if ($request->has('video')) {
			$purifiedData['video'] = $request->video;
		}

		if ($request->has('image')) {
			$purifiedData['image'] = $request->image;
		}

		if ($request->has('top_image')) {
			$purifiedData['top_image'] = $request->top_image;
		}

		if ($request->has('bottom_image')) {
			$purifiedData['bottom_image'] = $request->bottom_image;
		}

		if ($request->has('thumbnail')) {
			$purifiedData['thumbnail'] = $request->thumbnail;
		}

		$validate = Validator::make($purifiedData, config("templates.$section.validation"), config('templates.message'));
		if ($validate->fails()) {
			return back()->withInput()->withErrors($validate);
		}

		// save regular field
		$field_name = array_diff_key(config("templates.$section.field_name"), config("templates.template_media"));
		foreach ($field_name as $name => $type) {
			$description[$name] = $purifiedData[$name][$language];
		}
		$template = Template::where(['section_name' => $section, 'language_id' => $language])->firstOrCreate();
		$template->language_id = $language;
		$template->section_name = $section;
		$template->description = $description ?? null;
		$template->save();

		// save template media
		if ($request->hasAny(array_keys(config('templates.template_media')))) {
			$templateMedia = TemplateMedia::where(['section_name' => $section])->firstOrCreate();

			$old_image = $templateMedia->description->image ?? null;

			if ($request->has('image')) {
				$image = $this->fileUpload($purifiedData['image'][$language], config('location.template.path'), $templateMedia->driver, null, $old_image);
				if ($image) {
					$templateMediaDescription['image'] = $image['path'];
					$templateMedia->driver = $image['driver'];
				}
			} elseif (isset($old_image)) {
				$templateMediaDescription['image'] = $old_image;
			}

			$old_bottom_image = $templateMedia->description->bottom_image ?? null;
			if ($request->has('bottom_image')) {
				$bottom_image = $this->fileUpload($purifiedData['bottom_image'][$language], config('location.template.path'), $templateMedia->driver, null, $old_bottom_image);
				if ($bottom_image) {
					$templateMediaDescription['bottom_image'] = $bottom_image['path'];
					$templateMedia->driver = $bottom_image['driver'];
				}
			} elseif (isset($old_bottom_image)) {
				$templateMediaDescription['bottom_image'] = $old_bottom_image;
			}

			$old_top_image = $templateMedia->description->top_image ?? null;
			if ($request->has('top_image')) {
				$top_image = $this->fileUpload($purifiedData['top_image'][$language], config('location.template.path'), $templateMedia->driver, null, $old_top_image);
				if ($top_image) {
					$templateMediaDescription['top_image'] = $top_image['path'];
					$templateMedia->driver = $top_image['driver'];
				}
			} elseif (isset($old_top_image)) {
				$templateMediaDescription['top_image'] = $old_top_image;
			}

			$old_thumbnail = $templateMedia->description->thumbnail ?? null;
			if ($request->has('thumbnail')) {
				$image = $this->fileUpload($purifiedData['thumbnail'][$language], config('location.template.path'), $templateMedia->driver, null, $old_thumbnail);
				if ($image) {
					$templateMediaDescription['thumbnail'] = $image['path'];
					$templateMedia->driver = $image['driver'];
				}
			} elseif (isset($old_thumbnail)) {
				$templateMediaDescription['thumbnail'] = $old_thumbnail;
			}

			$old_video = $templateMedia->description->video ?? null;
			if ($request->has('video')) {
				$image = $this->fileUpload($purifiedData['video'][$language], config('location.template.path'), $templateMedia->driver, null, $old_video);
				if ($image) {
					$templateMediaDescription['video'] = $image['path'];
					$templateMedia->driver = $image['driver'];
				}
			} elseif (isset($old_image)) {
				$templateMediaDescription['video'] = $old_video;
			}

			$old_youtube_link = $templateMedia->description->youtube_link ?? null;
			if ($request->has('video_link')) {
				$templateMediaDescription['video_link'] = linkToEmbed($purifiedData['video_link'][$language]);
			} elseif (isset($old_youtube_link)) {
				$templateMediaDescription['video_link'] = $old_youtube_link;
			}

			$old_button_link = $templateMedia->description->button_link ?? null;
			if ($request->has('button_link')) {
				$templateMediaDescription['button_link'] = ($purifiedData['button_link'][$language]);
			} elseif (isset($old_button_link)) {
				$templateMediaDescription['button_link'] = $old_button_link;
			}

			$templateMedia->section_name = $section;
			$templateMedia->description = $templateMediaDescription ?? null;
			$templateMedia->save();
		}

		return back()->with('success', 'Template Successfully Saved');
	}
}
