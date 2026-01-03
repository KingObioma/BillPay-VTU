<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillPay;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Language;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\Notify;
use App\Traits\Upload;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	use Upload, Notify;

	public function index()
	{
		$users = User::with('profile')->latest()->paginate(config('basic.paginate'));
		return view('admin.user.index', compact('users'));
	}

	public function inactiveUserList()
	{
		$users = User::where('status', 0)
			->with('profile')
			->latest()
			->paginate(config('basic.paginate'));
		return view('admin.user.inactive', compact('users'));
	}

	public function userProfile($id)
	{
		$user = User::with('profile')->findOrFail($id);
		$data['transactions'] = Transaction::with(['transactional' => function (MorphTo $morphTo) {
			$morphTo->morphWith([
				Fund::class => ['sender', 'receiver'],
				BillPay::class => ['user']
			]);
		}])
			->whereHasMorph('transactional',
				[
					Fund::class,
					BillPay::class,
				], function ($query, $type) use ($user) {
					if ($type === Fund::class || BillPay::class) {
						$query->where('user_id', $user->id);
					}
				})
			->limit(5)
			->latest()->get();
		$data['funds'] = Deposit::where('status', '!=', 0)->orderBy('id', 'DESC')->with('receiver', 'gateway')->where('user_id', $user->id)
			->limit(5)->get();

		$data['bills'] = BillPay::with(['user', 'method'])->where('user_id', $user->id)->whereIn('status', ['2', '3', '4', '5'])->limit(5)
			->latest()->get();
		return view('admin.user.profile', compact('user'), $data);
	}

	public function userTransaction($id)
	{
		$user = User::with('profile')->findOrFail($id);
		$data['transactions'] = Transaction::with(['transactional' => function (MorphTo $morphTo) {
			$morphTo->morphWith([
				Fund::class => ['sender', 'receiver'],
				BillPay::class => ['user']
			]);
		}])
			->whereHasMorph('transactional',
				[
					Fund::class,
					BillPay::class,
				], function ($query, $type) use ($user) {
					if ($type === Fund::class || BillPay::class) {
						$query->where('user_id', $user->id);
					}
				})
			->latest()->paginate(config('basic.paginate'));
		return view('admin.user.userTransaction', $data, compact('user'));
	}

	public function userTransactionSearch(Request $request, $id)
	{
		$user = User::with('profile')->findOrFail($id);
		$filterData = $this->_filter($request);
		$search = $filterData['search'];
		$transactions = $filterData['transactions']
			->latest()
			->paginate(config('basic.paginate'));
		$transactions->appends($filterData['search']);
		return view('admin.user.userTransaction', compact('search', 'transactions', 'user'));
	}

	public function _filter($request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		if (isset($search['type'])) {
			if ($search['type'] == 'Fund') {
				$morphWith = [Fund::class => ['sender', 'receiver']];
				$whereHasMorph = [Fund::class];
			}
			if ($search['type'] == 'BillPay') {
				$morphWith = [BillPay::class => ['user']];
				$whereHasMorph = [BillPay::class];
			}
		} else {
			$morphWith = [
				Fund::class => ['sender', 'receiver'],
				BillPay::class => ['user'],
			];
			$whereHasMorph = [
				Fund::class,
				BillPay::class,
			];
		}

		$transactions = Transaction::with(['transactional' => function (MorphTo $morphTo) use ($morphWith, $whereHasMorph) {
			$morphTo->morphWith($morphWith);
		}])
			->whereHasMorph('transactional', $whereHasMorph, function ($query, $type) use ($search, $created_date) {
				$query->when($search['utr'], function ($query) use ($search) {
					return $query->where('utr', 'LIKE', $search['utr']);
				})
					->when($search['min'], function ($query) use ($search) {
						return $query->where('amount', '>=', $search['min']);
					})
					->when($search['max'], function ($query) use ($search) {
						return $query->where('amount', '<=', $search['max']);
					})
					->when($created_date == 1, function ($query) use ($search) {
						return $query->whereDate("created_at", $search['created_at']);
					});
			}
			);

		$data = [
			'transactions' => $transactions,
			'search' => $search,
		];
		return $data;
	}

	public function userPaymentLog($id)
	{
		$user = User::with('profile')->findOrFail($id);
		$data['funds'] = Deposit::where('status', '!=', 0)->orderBy('id', 'DESC')->with('receiver', 'gateway')->where('user_id', $user->id)
			->paginate(config('basic.paginate'));
		return view('admin.user.userPaymentHistory', $data, compact('user'));
	}

	public function userPaymentLogSearch(Request $request, $id)
	{
		$user = User::with('profile')->findOrFail($id);
		$search = $request->all();
		$dateSearch = $request->date_time;
		$date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);

		$funds = Deposit::when(isset($search['name']), function ($query) use ($search) {
			return $query->where('utr', 'LIKE', $search['name']);
		})
			->when($date == 1, function ($query) use ($dateSearch) {
				return $query->whereDate("created_at", $dateSearch);
			})
			->when($search['status'] != -1, function ($query) use ($search) {
				return $query->where('status', $search['status']);
			})
			->where('status', '!=', 0)
			->with('receiver', 'gateway')
			->where('user_id', $user->id)
			->paginate(config('basic.paginate'));
		$funds->appends($search);
		return view('admin.user.userPaymentHistory', compact('funds', 'user'));
	}

	public function userBillPay(Request $request, $id)
	{
		$user = User::with('profile')->findOrFail($id);

		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		$data['bills'] = BillPay::with(['user', 'method'])->when(isset($search['category']), function ($query) use ($search) {
			return $query->where('category_name', 'LIKE', "%{$search['category']}%");
		})
			->when(isset($search['searchType']), function ($query) use ($search) {
				return $query->where('type', 'LIKE', "%{$search['searchType']}%");
			})
			->when(isset($search['status']), function ($query) use ($search) {
				if ($search['status'] == 'generate') {
					return $query->where('status', 0);
				} elseif ($search['status'] == 'pending') {
					return $query->where('status', 1);
				} elseif ($search['status'] == 'processing') {
					return $query->where('status', 5);
				} elseif ($search['status'] == 'payment_completed') {
					return $query->where('status', 2);
				} elseif ($search['status'] == 'bill_completed') {
					return $query->where('status', 3);
				} elseif ($search['status'] == 'bill_return') {
					return $query->where('status', 4);
				}
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->whereIn('status', ['2', '3', '4', '5'])
			->latest()->paginate(config('basic.paginate'));

		return view('admin.user.userBillPayHistory', compact('user'), $data);
	}

	public function search(Request $request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;
		$last_login_at = isset($search['last_login_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['last_login_at']) : 0;

		$active = isset($search['status']) ? preg_match("/active/", $search['status']) : 0;
		$inactive = isset($search['status']) ? preg_match("/inactive/", $search['status']) : 0;

		$users = User::with('profile')
			->when(isset($search['name']), function ($query) use ($search) {
				return $query->where('name', 'LIKE', "%{$search['name']}%");
			})
			->when(isset($search['email']), function ($query) use ($search) {
				return $query->where('email', 'LIKE', "%{$search['email']}%");
			})
			->when($active == 1, function ($query) use ($search) {
				return $query->where("status", 1);
			})
			->when($inactive == 1, function ($query) use ($search) {
				return $query->where("status", 0);
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->when($last_login_at == 1, function ($query) use ($search) {
				return $query->whereHas('profile', function ($qry) use ($search) {
					$qry->whereDate("last_login_at", $search['last_login_at']);
				});
			})
			->when(isset($search['phone']), function ($query) use ($search) {
				return $query->whereHas('profile', function ($qry) use ($search) {
					$qry->where('phone', 'LIKE', "%{$search['phone']}%");
				});
			})
			->latest()
			->paginate();
		$users->appends($search);
		return view('admin.user.index', compact('search', 'users'));
	}

	public function inactiveUserSearch(Request $request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;
		$last_login_at = isset($search['last_login_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['last_login_at']) : 0;

		$active = isset($search['status']) ? preg_match("/active/", $search['status']) : 0;
		$inactive = isset($search['status']) ? preg_match("/inactive/", $search['status']) : 0;

		$users = User::where('status', 0)->with('profile')
			->when(isset($search['name']), function ($query) use ($search) {
				return $query->where('name', 'LIKE', "%{$search['name']}%");
			})
			->when(isset($search['email']), function ($query) use ($search) {
				return $query->where('email', 'LIKE', "%{$search['email']}%");
			})
			->when($active == 1, function ($query) use ($search) {
				return $query->where("status", 1);
			})
			->when($inactive == 1, function ($query) use ($search) {
				return $query->where("status", 0);
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->when($last_login_at == 1, function ($query) use ($search) {
				return $query->whereHas('profile', function ($qry) use ($search) {
					$qry->whereDate("last_login_at", $search['last_login_at']);
				});
			})
			->when(isset($search['phone']), function ($query) use ($search) {
				return $query->whereHas('profile', function ($qry) use ($search) {
					$qry->where('phone', 'LIKE', "%{$search['phone']}%");
				});
			})
			->latest()
			->paginate();
		$users->appends($search);
		return view('admin.user.inactive', compact('search', 'users'));
	}

	public function edit(Request $request, user $user)
	{
		$userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
		$languages = Language::get();
		if ($request->isMethod('get')) {
			$userId = $user->id;
			$countries = config('country');

			$billPayCount = BillPay::where(['user_id' => $userId])->whereIn('status', ['2', '3', '4'])->count();

			$transactionCount = [
				'billPay' => $billPayCount
			];

			return view('admin.user.show', compact('user', 'userProfile', 'transactionCount', 'countries', 'languages'));
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());

			$validator = Validator::make($purifiedData, [
				'name' => 'required|min:3|max:100|string',
				'city' => 'nullable|min:3|max:32|string',
				'state' => 'nullable|min:3|max:32|string',
				'phone' => 'required|max:32',
				'address' => 'nullable|max:250',
				'password' => 'nullable|min:5|max:50',
				'username' => 'required|min:5|max:50|unique:users,username,' . $user->id,
				'email' => 'required|email|min:5|max:100|unique:users,email,' . $user->id,
				'language' => 'required|numeric|not_in:0'
			]);
			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}
			$purifiedData = (object)$purifiedData;
			$user->name = $purifiedData->name;
			$user->username = $purifiedData->username;
			$user->email = $purifiedData->email;
			$user->status = $purifiedData->status;
			$user->language_id = $purifiedData->language;
			$user->email_verification = $purifiedData->email_verification;
			$user->sms_verification = $purifiedData->sms_verification;
			$userProfile->city = $purifiedData->city;
			$userProfile->state = $purifiedData->state;
			$userProfile->phone = $purifiedData->phone;
			$userProfile->phone_code = $purifiedData->phone_code;
			$userProfile->address = $purifiedData->address;

			$request->whenFilled('password', function ($input) use ($user, $purifiedData) {
				$user->password = bcrypt($purifiedData->password);
			});

			if ($request->file('profile_picture') && $request->file('profile_picture')->isValid()) {
				$extension = $request->profile_picture->extension();
				$profileName = strtolower($user->username . '.' . $extension);
				$image = $this->fileUpload($request->profile_picture, config('location.user.path'), $userProfile->driver, $profileName, $userProfile->profile_picture);
				if ($image) {
					$userProfile->profile_picture = $image['path'];
					$userProfile->driver = $image['driver'];
				}
			}

			$user->save();
			$userProfile->save();

			return back()->with('success', 'Profile Update Successfully');
		}
	}

	public function sendMailUser(Request $request, user $user = null)
	{
		if ($request->isMethod('get')) {
			return view('admin.user.sendMail', compact('user'));
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());
			$validator = Validator::make($purifiedData, [
				'subject' => 'required|min:5',
				'template' => 'required|min:10',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}

			$purifiedData = (object)$purifiedData;
			$subject = $purifiedData->subject;
			$template = $purifiedData->template;

			if (isset($user)) {
				$this->mail($user, null, [], $subject, $template);
			} else {
				$users = User::all();
				foreach ($users as $user) {
					$this->mail($user, null, [], $subject, $template);
				}
			}
			return redirect(route('user-list'))->with('success', 'Email Send Successfully');
		}
	}

	public function userBalanceUpdate(Request $request, $id)
	{
		$userData = Purify::clean($request->all());
		if ($userData['balance'] == null) {
			return back()->with('error', 'Balance Value Empty!');
		} else {
			$control = (object)config('basic');
			$user = User::findOrFail($id);

			$trx = strRandom();

			if ($userData['add_status'] == "1") {
				$user->balance += $userData['balance'];
				$user->save();

				$fund = new Fund();
				$fund->user_id = $user->id;
				$fund->amount = $userData['balance'];
				$fund->admin_id = auth()->id();
				$fund->status = 1;
				$fund->email = $user->email ?? null;
				$fund->utr = $trx;
				$fund->save();

				$transaction = new Transaction();
				$transaction->amount = getAmount($userData['balance']);
				$transaction->charge = 0;
				$transaction->remark = getAmount($userData['balance']) . ' ' . config('basic.base_currency') . ' credited to your balance';
				$fund->transactional()->save($transaction);

				$msg = [
					'amount' => getAmount($userData['balance']),
					'currency' => $control->base_currency,
					'main_balance' => $user->balance,
					'transaction' => $trx
				];
				$action = [
					"link" => '#',
					"icon" => "fa fa-money-bill-alt text-white"
				];

				$this->userPushNotification($user, 'ADD_BALANCE', $msg, $action);

				$this->sendMailSms($user, 'ADD_BALANCE', [
					'amount' => getAmount($userData['balance']),
					'currency' => $control->base_currency,
					'main_balance' => $user->balance,
					'transaction' => $trx
				]);
				return back()->with('success', 'Balance Add Successfully.');

			} else {

				if ($userData['balance'] > $user->balance) {
					return back()->with('error', 'Insufficient Balance to deducted.');
				}
				$user->balance -= $userData['balance'];
				$user->save();

				$fund = new Fund();
				$fund->user_id = $user->id;
				$fund->admin_id = auth()->id();
				$fund->amount = $userData['balance'];
				$fund->status = 1;
				$fund->email = $user->email ?? null;
				$fund->utr = $trx;
				$fund->save();

				$transaction = new Transaction();
				$transaction->amount = getAmount($userData['balance']);
				$transaction->charge = 0;
				$transaction->remark = getAmount($userData['balance']) . ' ' . config('basic.base_currency') . ' debited from your balance';
				$fund->transactional()->save($transaction);

				$msg = [
					'amount' => getAmount($userData['balance']),
					'currency' => $control->base_currency,
					'main_balance' => $user->balance,
					'transaction' => $trx
				];
				$action = [
					"link" => '#',
					"icon" => "fa fa-money-bill-alt text-white"
				];

				$this->userPushNotification($user, 'DEDUCTED_BALANCE', $msg, $action);

				$this->sendMailSms($user, 'DEDUCTED_BALANCE', [
					'amount' => getAmount($userData['balance']),
					'currency' => $control->base_currency,
					'main_balance' => $user->balance,
					'transaction' => $trx,
				]);
				return back()->with('success', 'Balance deducted Successfully.');
			}
		}
	}

	public function asLogin($id)
	{
		Auth::guard('web')->loginUsingId($id);
		return redirect()->route('user.dashboard');
	}

	public function twoFaStatus($id)
	{
		$user = User::findOrFail($id);
		if ($user->two_fa) {
			$user->two_fa = 0;
		} else {
			$user->two_fa = 1;
		}
		$user->save();
		return back()->with('success', 'Updated Successfully');
	}
}
