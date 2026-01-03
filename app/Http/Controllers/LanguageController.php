<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class LanguageController extends Controller
{
	use Upload;

	public function index()
	{
		$languages = Language::all();
		return view('admin.language.index', compact('languages'));
	}

	public function create()
	{
		return view('admin.language.create');
	}

	public function store(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'name' => 'required|min:2|max:100',
			'short_name' => 'required|size:2|unique:languages',
			'flag' => 'nullable|image|mimes:jpg,jpeg,png|max:5000',
			'is_active' => 'nullable|integer|min:0|in:0,1',
			'rtl' => 'nullable|integer|min:0|in:0,1',
		];

		$purifiedData['flag'] = $request->flag;
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return back()->withErrors($validate)->withInput();
		}

		$purifiedData = (object)$purifiedData;
		$language = new Language();
		$language->name = $purifiedData->name;
		$language->short_name = strtolower($purifiedData->short_name);
		$language->is_active = $purifiedData->is_active;
		$language->rtl = $purifiedData->rtl;

		if ($request->file('flag') && $request->file('flag')->isValid()) {
			$image = $this->fileUpload($request->flag, config('location.language.path'));
			if ($image) {
				$language->flag = $image['path'];
				$language->driver = $image['driver'];
			}
		}
		$language->save();

		$data = file_get_contents(resource_path('lang/') . 'en.json');
		$json_file = strtolower($language->short_name) . '.json';
		$path = resource_path('lang/') . $json_file;
		File::put($path, $data);

		return redirect(route('language.index'))->with('success', 'Language Successfully Saved');
	}

	public function edit(language $language)
	{
		return view('admin.language.edit', compact('language'));
	}

	public function update(Request $request, language $language)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'name' => 'required|min:2|max:100',
			'short_name' => 'required|size:2|unique:languages,flag,' . $language->id,
			'flag' => 'nullable|image|mimes:jpg,jpeg,png|max:5000',
			'is_active' => 'nullable|integer|min:0|in:0,1',
			'rtl' => 'nullable|integer|min:0|in:0,1',
		];
		$purifiedData['flag'] = $request->flag;
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return back()->withErrors($validate)->withInput();
		}

		$purifiedData = (object)$purifiedData;
		$language->name = $purifiedData->name;
		$language->short_name = strtolower($purifiedData->short_name);
		$language->is_active = $purifiedData->is_active;
		$language->rtl = $purifiedData->rtl;

		if ($request->file('flag') && $request->file('flag')->isValid()) {
			$image = $this->fileUpload($request->flag, config('location.language.path'), $language->driver, null, $language->flag);
			if ($image) {
				$language->flag = $image['path'];
				$language->driver = $image['driver'];
			}
		}
		$language->save();
		return redirect(route('language.index'))->with('success', 'Language Successfully Saved');
	}

	function destroy(language $language)
	{
		try {
			throw_if($language->default_status, 'You can not delete default language');

			$language->contentDetails()->delete();
			$language->templates()->delete();

			$path = config('location.language.path') . $language->flag;
			if (file_exists($path) && is_file($path)) {
				@unlink($path);
			}
			$langPath = resource_path("lang/{$language->short_name}.json");
			if (file_exists($langPath)) {
				@unlink($langPath);
			}
			$language->delete();
		} catch (\Exception $e) {

			DB::statement('ALTER TABLE `content_details` DROP FOREIGN KEY `content_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
			DB::statement('ALTER TABLE `email_templates` DROP FOREIGN KEY `email_templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
			DB::statement('ALTER TABLE `notify_templates` DROP FOREIGN KEY `notify_templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
			DB::statement('ALTER TABLE `templates` DROP FOREIGN KEY `templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
			DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

			return back()->with('error', $e->getMessage());
		}
		return back()->with('success', 'language has been deleted');
	}

	public function keywordEdit(language $language)
	{
		$json = file_get_contents(resource_path('lang/') . $language->short_name . '.json');
		$languages = Language::all();

		if (empty($json)) {
			return back()->with('error', 'File Not Found.');
		}
		$json = json_decode($json);

		return view('admin.language.keyword', compact('json', 'language', 'languages'));
	}

	public function keywordUpdate(Request $request, language $language)
	{
		$content = json_encode($request->keys);
		if ($content === 'null') {
			return back()->with('error', 'At Least One Field Should Be Fill-up');
		}
		file_put_contents(resource_path('lang/') . $language->short_name . '.json', $content);
		return back()->with('success', 'Update Successfully');
	}

	public function importJson(Request $request)
	{
		$mylang = Language::findOrFail($request->myLangid);
		$lang = Language::findOrFail($request->id);
		$json = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');

		$jsonArray = json_decode($json, true);
		file_put_contents(resource_path('lang/') . $mylang->short_name . '.json', json_encode($jsonArray));

		return 'success';
	}

	public function storeKey(Request $request, language $language)
	{
		$rules = [
			'key' => 'required',
			'value' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		$items = file_get_contents(resource_path('lang/') . $language->short_name . '.json');
		$requestKey = trim($request->key);
		if (array_key_exists($requestKey, json_decode($items, true))) {
			return back()->with('error', "`$requestKey` Already Exist");
		} else {
			$newArr[$requestKey] = trim($request->value);
			$itemsss = json_decode($items, true);
			$result = array_merge($itemsss, $newArr);
			file_put_contents(resource_path('lang/') . $language->short_name . '.json', json_encode($result));
			return back()->with('success', "`" . trim($requestKey) . "` has been added");
		}
	}

	public function deleteKey(Request $request, language $language)
	{
		$rules = [
			'key' => 'required',
			'value' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		$requestKey = $request->key;

		$data = file_get_contents(resource_path('lang/') . $language->short_name . '.json');
		$jsonArray = json_decode($data, true);
		unset($jsonArray[$requestKey]);
		file_put_contents(resource_path('lang/') . $language->short_name . '.json', json_encode($jsonArray));
		return back()->with('success', "`" . trim($request->key) . "` has been removed");
	}

	public function updateKey(Request $request, language $language)
	{
		$rules = [
			'key' => 'required',
			'value' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}
		$requestKey = trim($request->key);
		$requestValue = $request->value;

		$data = file_get_contents(resource_path('lang/') . $language->short_name . '.json');
		$jsonArray = json_decode($data, true);
		$jsonArray[$requestKey] = $requestValue;
		file_put_contents(resource_path('lang/') . $language->short_name . '.json', json_encode($jsonArray));

		return back()->with('success', "Update successfully");
	}
}
