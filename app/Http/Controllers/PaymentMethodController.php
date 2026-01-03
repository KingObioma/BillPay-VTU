<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
	use Upload;

	public function index()
	{
		$methods = Gateway::automatic()->orderBy('sort_by', 'ASC')->get();
		return view('admin.payment_methods.index', compact('methods'));
	}

	public function sortPaymentMethods(Request $request)
	{
		$sortItems = $request->sort;

		foreach ($sortItems as $key => $value) {
			Gateway::where('code', $value)->update(['sort_by' => $key + 1]);
		}
	}

	public function edit($id)
	{
		$method = Gateway::findOrFail($id);
		return view('admin.payment_methods.edit', compact('method'));
	}

	public function update(Request $request, $id)
	{
		$validationRules = [
			'currency' => 'required|string',
			'currency_symbol' => 'required|string',
			'minimum_deposit_amount' => 'required|numeric',
			'maximum_deposit_amount' => 'required|numeric',
			'percentage_charge' => 'required|numeric',
			'fixed_charge' => 'required|numeric',
			'convention_rate' => 'required|numeric',
			'environment' => 'sometimes|required|string|in:test,live',
			'status' => 'sometimes|required|min:0|in:0,1',
		];

		$gateway = Gateway::where('id', $id)->firstOr(function () {
			throw new \Exception('No payment method found.');
		});


		$parameters = [];
		foreach ($request->except('_token', '_method', 'image') as $k => $v) {
			foreach ($gateway->parameters as $key => $cus) {
				if ($k != $key) {
					continue;
				} else {
					$rules[$key] = 'required|max:191';
					$parameters[$key] = $v;
				}
			}
		}

		$purifiedData = Purify::clean($request->all());
		$purifiedData['image'] = $request->image;
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return back()->withErrors($validate)->withInput();
		}
		$purifiedData = (object)$purifiedData;

		if ($request->hasFile('image')) {
			try {
				$image = $this->fileUpload($request->image, config('location.gateway.path'), $gateway->driver, null, $gateway->image);
				if ($image) {
					$gatewayImage = $image['path'];
					$driver = $image['driver'];
				}
			} catch (\Exception $exp) {
				return back()->with('alert', 'Image could not be uploaded.');
			}
		}


		try {
			$response = $gateway->update([
				'currency' => $purifiedData->currency,
				'symbol' => $purifiedData->currency_symbol,
				'convention_rate' => $request->convention_rate,
				'min_amount' => $request->minimum_deposit_amount,
				'max_amount' => $request->maximum_deposit_amount,
				'percentage_charge' => $request->percentage_charge,
				'fixed_charge' => $request->fixed_charge,
				'parameters' => $parameters,
				'image' => $gatewayImage ?? $gateway->image,
				'driver' => $driver ?? $gateway->driver,
				'environment' => isset($purifiedData->environment) ?? null,
				'status' => $purifiedData->status
			]);

			if (!$response) {
				throw new \Exception('Unexpected error! Please try again.');
			}
			return back()->with('success', 'Gateway data has been updated.');
		} catch (\Exception $exception) {
			return back()->with('alert', $exception->getMessage());
		}
	}
}
