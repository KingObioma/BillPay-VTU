<?php

namespace App\Http\Controllers;

use App\Models\BillMethod;
use App\Models\BillPay;
use App\Models\BillService;
use App\Models\Deposit;
use App\Models\FirebaseNotify;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BasicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
	public function index()
	{
		$basicControl = basicControl();
		$today = today();
		$dayCount = date('t', strtotime($today));

		$users = User::selectRaw('COUNT(id) AS totalUser')
			->selectRaw('COUNT((CASE WHEN created_at >= CURDATE()  THEN id END)) AS todayJoin')
			->selectRaw('COUNT((CASE WHEN status = 1  THEN id END)) AS activeUser')
			->selectRaw('COUNT((CASE WHEN email_verified_at IS NOT NULL  THEN id END)) AS verifiedUser')
			->get()->makeHidden(['mobile', 'profile'])->toArray();

		$data['userRecord'] = collect($users)->collapse();

		$activeMethod = BillMethod::where('is_active', 1)->first();
		$services = BillService::selectRaw('SUM(CASE WHEN bill_method_id = ' . $activeMethod->id . ' THEN 1 ELSE 0 END) AS totalServices')
			->selectRaw('COUNT(CASE WHEN status = 1 AND bill_method_id = ' . $activeMethod->id . ' THEN id END) AS activeServices')
			->selectRaw('COUNT(CASE WHEN status = 0 AND bill_method_id = ' . $activeMethod->id . ' THEN id END) AS inactiveServices')
			->get()->toArray();
		$data['serviceRecord'] = collect($services)->collapse();

		$bills = BillPay::selectRaw('COUNT(id) AS totalBills')
			->selectRaw('COUNT((CASE WHEN status = 2  THEN id END)) AS pendingBills')
			->selectRaw('COUNT((CASE WHEN status = 3  THEN id END)) AS completeBills')
			->selectRaw('COUNT((CASE WHEN status = 4  THEN id END)) AS returnBills')
			->get()->toArray();
		$data['billRecord'] = collect($bills)->collapse();


		$transactions = Transaction::select('created_at')
			->whereMonth('created_at', $today)
			->groupBy([DB::raw("DATE_FORMAT(created_at, '%j')")])
			->selectRaw("SUM(CASE WHEN transactional_type like '%BillPay' THEN amount ELSE 0 END) as BillPay")
			->get()
			->groupBy([function ($query) {
				return $query->created_at->format('j');
			}]);

		$labels = [];
		$dataBillPay = [];
		for ($i = 1; $i <= $dayCount; $i++) {
			$labels[] = date('jS M', strtotime(date('Y/m/') . $i));
			$currentBillPay = 0;
			if (isset($transactions[$i])) {
				foreach ($transactions[$i] as $key => $transaction) {
					$currentBillPay += $transaction->BillPay;
				}
			}
			$dataBillPay[] = round($currentBillPay, $basicControl->fraction_number);
		}

		$data['labels'] = $labels;
		$data['dataBillPay'] = $dataBillPay;

		$automatic = BillPay::selectRaw("DATE_FORMAT(created_at, '%m') as month")
			->where('status', '!=', 0)
			->where('payment_method_id', '!=', -1)
			->whereYear('created_at', $today)
			->groupBy('month')
			->selectRaw("SUM(pay_amount_in_base) as Automatic")
			->get()
			->groupBy('month');

		$wallet = BillPay::selectRaw("DATE_FORMAT(created_at, '%m') as month")
			->where('status', '!=', 0)
			->where('payment_method_id', '=', -1)
			->whereYear('created_at', $today)
			->groupBy('month')
			->selectRaw("SUM(pay_amount_in_base) as Wallet")
			->get()
			->groupBy('month');

		$data['monthLabels'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
		$data['yearLabels'] = ['01', '02', '03', '04', '05', '06', '07 ', '08', '09', '10', '11', '12'];

		$yearAutomatic = [];
		$yearWallet = [];
		foreach ($data['yearLabels'] as $yearLabel) {
			$currentYearAutomatic = 0;
			$currentYearWallet = 0;

			if (isset($automatic[$yearLabel])) {
				foreach ($automatic[$yearLabel] as $key => $automatic) {
					$currentYearAutomatic += $automatic->Automatic;
				}
			}
			if (isset($wallet[$yearLabel])) {
				foreach ($wallet[$yearLabel] as $key => $wallet) {
					$currentYearWallet += $wallet->Wallet;
				}
			}

			$yearAutomatic[] = round($currentYearAutomatic, $basicControl->fraction_number);
			$yearWallet[] = round($currentYearWallet, $basicControl->fraction_number);
		}

		$data['yearAutomatic'] = $yearAutomatic;
		$data['yearWallet'] = $yearWallet;

		$paymentMethods = Deposit::with('gateway:id,name')
			->whereYear('created_at', $today)
			->where('status', 1)
			->groupBy(['payment_method_id'])
			->selectRaw("SUM(amount) as totalAmount, payment_method_id")
			->get()
			->groupBy(['payment_method_id']);

		$paymentMethodeLabel = [];
		$paymentMethodeData = [];

		$paymentMethods = collect($paymentMethods)->collapse();
		foreach ($paymentMethods as $paymentMethode) {
			$currentPaymentMethodeLabel = 0;
			$currentPaymentMethodeData = 0;
			$currentPaymentMethodeLabel = optional($paymentMethode->gateway)->name ?? 'N/A';
			$currentPaymentMethodeData += $paymentMethode->totalAmount;

			$paymentMethodeLabel[] = $currentPaymentMethodeLabel;
			$paymentMethodeData[] = round($currentPaymentMethodeData, $basicControl->fraction_number);
		}

		$data['paymentMethodeLabel'] = $paymentMethodeLabel;
		$data['paymentMethodeData'] = $paymentMethodeData;
		$data['basicControl'] = $basicControl;

		$data['firebaseNotify'] = FirebaseNotify::first();
		return view('admin.home', $data, compact('activeMethod'));
	}

	public function changePassword(Request $request)
	{
		if ($request->isMethod('get')) {
			return view('admin.auth.passwords.change');
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());
			$validator = Validator::make($purifiedData, [
				'current_password' => 'required|min:5',
				'password' => 'required|min:5|confirmed',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$user = Auth::user();
			$purifiedData = (object)$purifiedData;

			if (!Hash::check($purifiedData->current_password, $user->password)) {
				return back()->withInput()->withErrors(['current_password' => 'current password did not match']);
			}

			$user->password = bcrypt($purifiedData->password);
			$user->save();
			return back()->with('success', 'Password changed successfully');
		}
	}

	public function saveToken(Request $request)
	{
		$admin = auth()->user();
		$admin->fcm_token = $request->token;
		$admin->save();
		return response()->json(['token saved successfully.']);
	}
}
