<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class AdminStorageController extends Controller
{
	use Upload;

	public function index()
	{
		$data['storages'] = Storage::orderBy('name', 'asc')->get();
		return view('admin.storage.index', $data);
	}

	public function edit(Request $request, $id)
	{
		$storage = Storage::where('code', '!=', 'local')->findOrFail($id);
		if ($request->method() == 'GET') {
			return view('admin.storage.edit', compact('storage'));
		} elseif ($request->method() == 'POST') {
			$purifiedData = Purify::clean($request->all());
			$validator = Validator::make($purifiedData, [
				'name' => 'required|min:2|max:20|string',
			]);
			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$purifiedData = (object)$purifiedData;

			$parameters = [];
			foreach ($request->except('_token', '_method', 'image') as $k => $v) {
				foreach ($storage->parameters as $key => $cus) {
					if ($k != $key) {
						continue;
					} else {
						$rules[$key] = 'required|max:191';
						$parameters[$key] = $v;
					}
				}
			}

			if ($request->hasFile('logo')) {
				try {
					$image = $this->fileUpload($request->logo, config('location.driver.path'), $storage->driver, null, $storage->logo);
					if ($image) {
						$storage->logo = $image['path'];
						$storage->driver = $image['driver'];
					}
				} catch (\Exception $e) {
					return back()->with('alert', 'Image could not be uploaded');
				}
			}

			$storage->name = $purifiedData->name;
			$storage->parameters = $parameters;
			$storage->save();
			$this->envWrite($storage->code, $storage->parameters);
			return back()->with('success', 'Updated Successfully');
		}
	}

	public function setDefault(Request $request, $id)
	{
		$activeStorage = Storage::findOrFail($id);
		$activeStorage->status = 1;
		$activeStorage->save();

		$storages = Storage::where('id', '!=', $id)->get();
		foreach ($storages as $storage) {
			$storage->status = 0;
			$storage->save();
		}

		config(['basic.default_file_driver' => $activeStorage->code]);

		$fp = fopen(base_path() . '/config/basic.php', 'w');
		fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
		fclose($fp);

		return back()->with('success', 'Updated Successfully');
	}

	public function envWrite($storageType, $parameters)
	{
		$envPath = base_path('.env');
		$env = file($envPath);
		if ($storageType == 's3') {
			$env = $this->set('AWS_ACCESS_KEY_ID', $parameters->access_key_id, $env);
			$env = $this->set('AWS_SECRET_ACCESS_KEY', $parameters->secret_access_key, $env);
			$env = $this->set('AWS_DEFAULT_REGION', $parameters->default_region, $env);
			$env = $this->set('AWS_BUCKET', $parameters->bucket, $env);
		} elseif ($storageType == 'sftp') {
			$env = $this->set('SFTP_USERNAME', $parameters->sftp_username, $env);
			$env = $this->set('SFTP_PASSWORD', $parameters->sftp_password, $env);
		} elseif ($storageType == 'do') {
			$env = $this->set('DIGITALOCEAN_SPACES_KEY', $parameters->spaces_key, $env);
			$env = $this->set('DIGITALOCEAN_SPACES_SECRET', $parameters->spaces_secret, $env);
			$env = $this->set('DIGITALOCEAN_SPACES_ENDPOINT', $parameters->spaces_endpoint, $env);
			$env = $this->set('DIGITALOCEAN_SPACES_REGION', $parameters->spaces_region, $env);
			$env = $this->set('DIGITALOCEAN_SPACES_BUCKET', $parameters->spaces_bucket, $env);
		} elseif ($storageType == 'ftp') {
			$env = $this->set('FTP_HOST', $parameters->ftp_host, $env);
			$env = $this->set('FTP_USERNAME', $parameters->ftp_username, $env);
			$env = $this->set('FTP_PASSWORD', $parameters->ftp_password, $env);
		}

		$fp = fopen($envPath, 'w');
		fwrite($fp, implode($env));
		fclose($fp);
		return 0;
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
}
