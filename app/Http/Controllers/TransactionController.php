<?php

namespace App\Http\Controllers;

use App\Models\BillPay;
use App\Models\Fund;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function index()
	{
		$user = Auth::user();
		$transactions = Transaction::with(['transactional' => function (MorphTo $morphTo) {
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
			->latest()
			->paginate(config('basic.paginate'));

		return view($this->theme . 'user.transaction.index', compact('transactions'));
	}

	public function search(Request $request)
	{
		$filterData = $this->_filter($request);
		$search = $filterData['search'];
		$user = $filterData['user'];
		$transactions = $filterData['transactions']
			->latest()
			->paginate(config('basic.paginate'));
		$transactions->appends($filterData['search']);
		return view($this->theme . 'user.transaction.index', compact('search', 'user', 'transactions'));
	}

	public function _filter($request)
	{
		$user = Auth::user();
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		if ($search['type']) {
			if ($search['type'] == 'BillPay') {
				$morphWith = [BillPay::class => ['user']];
				$whereHasMorph = [BillPay::class];
			}
		} else {
			$morphWith = [
				BillPay::class => ['user'],
			];
			$whereHasMorph = [
				BillPay::class,
			];
		}

		$transactions = Transaction::with(['transactional' => function (MorphTo $morphTo) use ($morphWith, $whereHasMorph) {
			$morphTo->morphWith($morphWith);
		}])
			->whereHasMorph('transactional', $whereHasMorph, function ($query, $type) use ($search, $created_date, $user) {
				if ($type === BillPay::class) {
					$query->where('user_id', $user->id);
				}

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
			'user' => $user,
			'transactions' => $transactions,
			'search' => $search,
		];
		return $data;
	}
}
