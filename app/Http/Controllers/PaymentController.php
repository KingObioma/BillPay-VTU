<?php

namespace App\Http\Controllers;

use App\Events\AdminNotification;
use App\Events\UserNotification;
use App\Mail\MasterTemplate;
use App\Models\Admin;
use App\Models\Deposit;
use App\Models\EmailTemplate;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\SiteNotification;
use App\Models\User;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Facades\App\Services\BasicService;

class PaymentController extends Controller
{
	use Upload, Notify;

	public function depositConfirm($utr)
	{
		try {
			$deposit = Deposit::with('receiver', 'depositable')->where(['utr' => $utr, 'status' => 0])->first();

			if (!$deposit) throw new \Exception('Invalid Payment Request.');

			$gateway = Gateway::findOrFail($deposit->payment_method_id);
			if (!$gateway) throw new \Exception('Invalid Payment Gateway.');

			if (999 < $gateway->id) {
				return view(template() . 'user.payment.manual', compact('deposit'));
			}

			$getwayObj = 'App\\Services\\Gateway\\' . $gateway->code . '\\Payment';

			$data = $getwayObj::prepareData($deposit, $gateway);

			$data = json_decode($data);

		} catch (\Exception $exception) {
			return back()->with('alert', $exception->getMessage());
		}

		if (isset($data->error)) {
			return back()->with('alert', $data->message);
		}

		if (isset($data->redirect)) {
			return redirect($data->redirect_url);
		}

		$page_title = 'Payment Confirm';
		$basic = basicControl();
		return view(template() . $data->view, compact('data', 'page_title', 'deposit', 'basic'));
	}

	public function fromSubmit(Request $request, $utr)
	{
		$basic = (object)config('basic');

		$data = Deposit::where('utr', $utr)->orderBy('id', 'DESC')->with(['gateway', 'receiver'])->first();
		if (is_null($data)) {
			return back()->with('alert', 'Invalid Request');
		}
		if ($data->status != 0) {
			return back()->with('alert', 'Invalid Request');
		}
		$gateway = $data->gateway;
		$params = optional($data->gateway)->parameters;


		$rules = [];
		$inputField = [];

		$verifyImages = [];

		if ($params != null) {
			foreach ($params as $key => $cus) {
				$rules[$key] = [$cus->validation];
				if ($cus->type == 'file') {
					array_push($rules[$key], 'image');
					array_push($rules[$key], 'mimes:jpeg,jpg,png');
					array_push($rules[$key], 'max:2048');
					array_push($verifyImages, $key);
				}
				if ($cus->type == 'text') {
					array_push($rules[$key], 'max:191');
				}
				if ($cus->type == 'textarea') {
					array_push($rules[$key], 'max:300');
				}
				$inputField[] = $key;
			}
		}

		$this->validate($request, $rules);


		$path = config('location.deposit.path') . date('Y') . '/' . date('m') . '/' . date('d');
		$collection = collect($request);

		$reqField = [];
		if ($params != null) {
			foreach ($collection as $k => $v) {
				foreach ($params as $inKey => $inVal) {
					if ($k != $inKey) {
						continue;
					} else {
						if ($inVal->type == 'file') {
							if ($request->hasFile($inKey)) {
								try {
									$reqField[$inKey] = [
										'field_name' => $this->fileUpload($request[$inKey], $path),
										'type' => $inVal->type,
									];
								} catch (\Exception $exp) {
									session()->flash('alert', 'Could not upload your ' . $inKey);
									return back()->withInput();
								}
							}
						} else {
							$reqField[$inKey] = $v;
							$reqField[$inKey] = [
								'field_name' => $v,
								'type' => $inVal->type,
							];
						}
					}
				}
			}
			$data->detail = $reqField;
		} else {
			$data->detail = null;
		}

		$data->created_at = Carbon::now();
		$data->status = 2; // pending
		$data->update();


		$msg = [
			'username' => $data->receiver->username,
			'amount' => getAmount($data->amount, config('basic.fraction_number')),
			'currency' => config('basic.base_currency'),
			'gateway' => $gateway->name
		];
		$action = [
			"link" => "#",
			"icon" => "fa fa-money-bill-alt text-white"
		];
		$this->adminPushNotification('PAYMENT_REQUEST', $msg, $action);

		session()->flash('success', 'You request has been taken.');
		if ($data->depositable_type == Fund::class) {
			return redirect()->route('addFundRequest');
		} else {
			return redirect()->route('pay.bill.request');
		}
	}

	public function gatewayIpn(Request $request, $code, $trx = null, $type = null)
	{
		if (isset($request->m_orderid)) {
			$trx = $request->m_orderid;
		}
		if (isset($request->MERCHANT_ORDER_ID)) {
			$trx = $request->MERCHANT_ORDER_ID;
		}
		if (isset($request->payment_ref)) {
			$payment_ref = $request->payment_ref;
		}
		if ($code == 'coinbasecommerce') {
			$gateway = Gateway::where('code', $code)->first();
			$postdata = file_get_contents("php://input");
			$res = json_decode($postdata);
			if (isset($res->event)) {
				$deposit = Deposit::with('receiver')->where('utr', $res->event->data->metadata->trx)->orderBy('id', 'DESC')->first();
				$sentSign = $request->header('X-Cc-Webhook-Signature');
				$sig = hash_hmac('sha256', $postdata, $gateway->parameters->secret);

				if ($sentSign == $sig) {
					if ($res->event->type == 'charge:confirmed' && $deposit->status == 0) {
						BasicService::prepareOrderUpgradation($deposit);
					}
				}
			}
			session()->flash('success', 'You request has been processing.');
			return redirect()->route('success');
		}

		try {
			$gateway = Gateway::where('code', $code)->first();
			if (!$gateway) throw new \Exception('Invalid Payment Gateway.');

			if (isset($trx)) {
				$deposit = Deposit::with('receiver')->where('utr', $trx)->first();
				if (!$deposit) throw new \Exception('Invalid Payment Request.');
			}
			if (isset($payment_ref)) {
				$order = Deposit::where('btc_wallet', $payment_ref)->orderBy('id', 'desc')->with(['gateway', 'receiver'])->first();
				if (!$order) throw new \Exception('Invalid Payment Request.');
			}
			$getwayObj = 'App\\Services\\Gateway\\' . $code . '\\Payment';
			$data = $getwayObj::ipn($request, $gateway, @$deposit, @$trx, @$type);

		} catch (\Exception $exception) {
			return back()->with('alert', $exception->getMessage());
		}
		if (isset($data['redirect'])) {
			return redirect($data['redirect'])->with($data['status'], $data['msg']);
		}
	}

	public function success()
	{
		if (isset($request->status) && $request->status == 'DECLINED') {
			session()->flash('alert', $request->message);
			return redirect()->route('failed');
		}

		return view('success');
	}

	public function failed()
	{
		return view('failed');
	}
}
