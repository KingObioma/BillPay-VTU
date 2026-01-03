<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\UserKyc;
use App\Traits\Notify;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
	use Notify;

	public function create()
	{
		$kyc = Kyc::first();
		return view('admin.kyc.create', compact('kyc'));
	}

	public function update(Request $request)
	{
		$kyc = Kyc::firstOrNew();

		$input_form = [];
		if ($request->has('field_name')) {
			for ($a = 0; $a < count($request->field_name); $a++) {
				$arr = array();
				$arr['field_name'] = clean($request->field_name[$a]);
				$arr['field_level'] = $request->field_name[$a];
				$arr['type'] = $request->type[$a];
				$arr['validation'] = $request->validation[$a];
				$input_form[$arr['field_name']] = $arr;
			}
		}
		$kyc->input_form = $input_form;
		$kyc->status = $request->is_active ?? 0;
		$kyc->save();
		return back()->with('success', 'updated successfully');
	}

	public function list(Request $request, $status = null)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;


		$data['kycLists'] = UserKyc::with('user')
			->when($status == 'pending', function ($query) {
				return $query->whereStatus(0);
			})
			->when($status == 'approve', function ($query) {
				return $query->whereStatus(1);
			})
			->when($status == 'rejected', function ($query) {
				return $query->whereStatus(2);
			})
			->when(isset($search['user']), function ($query) use ($search) {
				return $query->whereHas('user', function ($qry) use ($search) {
					$qry->where('name', 'LIKE', "%{$search['user']}%")
						->orWhere('username', 'LIKE', "{$search['user']}");
				});
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->paginate(config('basic.paginate'));


		return view('admin.kyc.index', $data);
	}

	public function view($id)
	{
		$data['kyc'] = UserKyc::with('user')->findOrFail($id);
		return view('admin.kyc.view', $data);
	}

	public function action($id, Request $request)
	{
		$userKyc = UserKyc::findOrFail($id);
		if ($request->kycBtn == 'approve') {
			$userKyc->status = 1;
			$userKyc->save();
			$userKyc->user->kyc_verified = 2;
			$userKyc->user->save();
			$this->userSendMailNotify($userKyc->user, 'approve');
			return back()->with('success', 'Approved Successfully');

		} elseif ($request->kycBtn == 'rejected') {
			$userKyc->status = 2;
			$userKyc->reason = $request->reason;
			$userKyc->save();
			$userKyc->user->kyc_verified = 3;
			$userKyc->user->save();
			$this->userSendMailNotify($userKyc->user, 'reject');
			return back()->with('success', 'Rejected Successfully');
		}
	}

	public function userSendMailNotify($user, $type)
	{
		if ($type == 'approve') {
			$templateKey = 'KYC_APPROVE';
		} else {
			$templateKey = 'KYC_REJECT';
		}
		$action = [
			"link" => "",
			"icon" => "fa fa-money-bill-alt text-white"
		];
		$this->sendMailSms($user, $templateKey);
		$this->userPushNotification($user, $templateKey, $action);
		$this->userFirebasePushNotification($user, $templateKey);
		return 0;
	}
}
