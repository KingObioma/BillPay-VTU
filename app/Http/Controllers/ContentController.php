<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentDetails;
use App\Models\ContentMedia;
use App\Models\Language;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class ContentController extends Controller
{
	use Upload;

	public function index($content)
	{
		$contents = Content::with('contentDetails', 'contentMedia')->where('name', $content)->get();
		return view('admin.content.index', compact('content', 'contents'));
	}

	public function create($content)
	{
		if (!array_key_exists($content, config('contents'))) {
			abort(404);
		}
		$languages = Language::all();
		return view('admin.content.create', compact('languages', 'content'));
	}

	public function store(Request $request, $content, $language)
	{
		if (!array_key_exists($content, config('contents'))) {
			abort(500);
		}

		$purifiedData = Purify::clean($request->except('image', 'thumbnail', '_token', '_method'));

		if ($request->has('image')) {
			$purifiedData['image'] = $request->image;
		}
		if ($request->has('writer_image')) {
			$purifiedData['writer_image'] = $request->writer_image;
		}
		if ($request->has('thumbnail')) {
			$purifiedData['thumbnail'] = $request->thumbnail;
		}

		$validate = Validator::make($purifiedData, config("contents.$content.validation"), config('contents.message'));

		if ($validate->fails()) {
			return back()->withInput()->withErrors($validate);
		}

		// save regular field
		$field_name = array_diff_key(config("contents.$content.field_name"), config("contents.content_media"));

		foreach ($field_name as $name => $type) {
			$description[$name] = $purifiedData[$name][$language];
		}

		// save content for the first time
		$contentModel = new Content();
		$contentModel->name = $content;
		$contentModel->save();

		// save content details on any language
		if ($language != 0) {
			$contentDetails = new ContentDetails();
			$contentDetails->content_id = $contentModel->id;
			$contentDetails->language_id = $language;
			$contentDetails->description = $description ?? null;
			$contentDetails->save();
		}

		// save content media
		if ($request->hasAny(array_keys(config('contents.content_media')))) {
			$contentMedia = new ContentMedia();

			if ($request->has('image')) {
				$image = $this->fileUpload($purifiedData['image'][$language], config('location.content.path'));
				if ($image) {
					$contentMediaDescription['image'] = $image['path'];
					$contentMedia->driver = $image['driver'];
				}
			}
			if ($request->has('writer_image')) {
				$writer_image = $this->fileUpload($purifiedData['writer_image'][$language], config('location.content.path'));
				if ($writer_image) {
					$contentMediaDescription['writer_image'] = $writer_image['path'];
					$contentMedia->driver = $writer_image['driver'];
				}
			}
			if ($request->has('thumbnail')) {
				$image = $this->fileUpload($purifiedData['thumbnail'][$language], config('location.content.path'));
				if ($image) {
					$contentMediaDescription['thumbnail'] = $image['path'];
					$contentMedia->driver = $image['driver'];
				}
			}
			if ($request->has('youtube_link')) {
				$contentMediaDescription['youtube_link'] = linkToEmbed($purifiedData['youtube_link'][$language]);
			}

			if ($request->has('social_icon')) {
				$contentMediaDescription['social_icon'] = $purifiedData['social_icon'][$language];
			}

			if ($request->has('icon')) {
				$contentMediaDescription['icon'] = $purifiedData['icon'][$language];
			}

			if ($request->has('social_link')) {
				$contentMediaDescription['social_link'] = $purifiedData['social_link'][$language];
			}
			if ($request->has('button_link')) {
				$contentMediaDescription['button_link'] = $purifiedData['button_link'][$language];
			}

			$contentMedia->content_id = $contentModel->id;
			$contentMedia->description = $contentMediaDescription ?? null;
			$contentMedia->save();
		}

		return redirect(route('content.index', $content))->with('success', 'Content Details Successfully Saved');
	}

	public function show(Content $content)
	{
		if (!array_key_exists($content->name, config('contents'))) {
			abort(404);
		}

		$languages = Language::all();
		$contentDetails = ContentDetails::where('content_id', $content->id)->get()->groupBy('language_id');
		$contentMedia = ContentMedia::where('content_id', $content->id)->first();

		return view('admin.content.show', compact('content', 'languages', 'contentDetails', 'contentMedia'));
	}

	public function update(Request $request, Content $content, $language)
	{
		if (!array_key_exists($content->name, config('contents'))) {
			abort(404);
		}

		$purifiedData = Purify::clean($request->except('image', 'thumbnail', '_token', '_method'));

		if ($request->has('image')) {
			$purifiedData['image'] = $request->image;
		}
		if ($request->has('writer_image')) {
			$purifiedData['writer_image'] = $request->writer_image;
		}
		if ($request->has('thumbnail')) {
			$purifiedData['thumbnail'] = $request->thumbnail;
		}

		$validate = Validator::make($purifiedData, config("contents.$content->name.validation"), config('contents.message'));
		if ($validate->fails()) {
			return back()->withInput()->withErrors($validate);
		}

		// save regular field
		$field_name = array_diff_key(config("contents.$content->name.field_name"), config("contents.content_media"));

		foreach ($field_name as $name => $type) {
			$description[$name] = $purifiedData[$name][$language];
		}
		if ($language != 0) {
			$contentDetails = ContentDetails::where(['content_id' => $content->id, 'language_id' => $language])->firstOrCreate();
			$contentDetails->content_id = $content->id;
			$contentDetails->language_id = $language;
			$contentDetails->description = $description ?? null;
			$contentDetails->save();
		}

		// save contents media
		if ($request->hasAny(array_keys(config('contents.content_media')))) {
			$contentMedia = ContentMedia::where(['content_id' => $content->id])->firstOrCreate();

			$old_image = $contentMedia->description->image ?? null;
			if ($request->has('image')) {
				$image = $this->fileUpload($purifiedData['image'][$language], config('location.content.path'), $contentMedia->driver, null, $old_image);
				if ($image) {
					$contentMediaDescription['image'] = $image['path'];
					$contentMedia->driver = $image['driver'];
				}
			} elseif (isset($old_image)) {
				$contentMediaDescription['image'] = $old_image;
			}

			$old_writer_image = $contentMedia->description->writer_image ?? null;
			if ($request->has('writer_image')) {
				$writer_image = $this->fileUpload($purifiedData['writer_image'][$language], config('location.content.path'), $contentMedia->driver, null, $old_writer_image);
				if ($writer_image) {
					$contentMediaDescription['writer_image'] = $writer_image['path'];
					$contentMedia->driver = $writer_image['driver'];
				}
			} elseif (isset($old_writer_image)) {
				$contentMediaDescription['writer_image'] = $old_writer_image;
			}

			$old_thumbnail = $contentMedia->description->thumbnail ?? null;
			if ($request->has('thumbnail')) {
				$image = $this->fileUpload($purifiedData['thumbnail'][$language], config('location.content.path'), $contentMedia->driver, null, $old_thumbnail);
				if ($image) {
					$contentMediaDescription['thumbnail'] = $image['path'];
					$contentMedia->driver = $image['driver'];
				}
			} elseif (isset($old_thumbnail)) {
				$contentMediaDescription['thumbnail'] = $old_thumbnail;
			}

			$old_youtube_link = $contentMedia->description->youtube_link ?? null;
			if ($request->has('youtube_link')) {
				$contentMediaDescription['youtube_link'] = linkToEmbed($purifiedData['youtube_link'][$language]);
			} elseif (isset($old_youtube_link)) {
				$contentMediaDescription['youtube_link'] = $old_youtube_link;
			}

			$old_social_icon = $contentMedia->description->social_icon ?? null;
			if ($request->has('social_icon')) {
				$contentMediaDescription['social_icon'] = $purifiedData['social_icon'][0];
			} elseif (isset($old_social_icon)) {
				$contentMediaDescription['social_icon'] = $old_social_icon;
			}

			$old_icon = $contentMedia->description->icon ?? null;
			if ($request->has('icon')) {
				$contentMediaDescription['icon'] = $purifiedData['icon'][0];
			} elseif (isset($old_icon)) {
				$contentMediaDescription['icon'] = $old_icon;
			}

			$old_social_link = $contentMedia->description->social_link ?? null;
			if ($request->has('social_link')) {
				$contentMediaDescription['social_link'] = $purifiedData['social_link'][$language];
			} elseif (isset($old_youtube_link)) {
				$contentMediaDescription['social_link'] = $old_social_link;
			}

			$old_button_link = $contentMedia->description->button_link ?? null;
			if ($request->has('button_link')) {
				$contentMediaDescription['button_link'] = $purifiedData['button_link'][$language];
			} elseif (isset($old_youtube_link)) {
				$contentMediaDescription['button_link'] = $old_button_link;
			}

			$contentMedia->content_id = $content->id;
			$contentMedia->description = $contentMediaDescription ?? null;
			$contentMedia->save();
		}

		return back()->with('success', 'Content Details Successfully Saved');
	}

	function destroy($id)
	{
		$content = Content::findOrFail($id);
		if (isset($content->contentMedia->description->image)) {
			$this->removeFile(config('location.content.path') . $content->contentMedia->description->image);
		};
		if (isset($content->contentMedia->description->thumbnail)) {
			$this->removeFile(config('location.content.path') . $content->contentMedia->description->thumbnail);
		};

		$content->contentMedia()->delete();
		$content->contentDetails()->delete();
		$content->delete();
		return back()->with('success', 'Content has been deleted');
	}
}
