<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Traits\Notify;
use Illuminate\Http\Request;


class AdminDepositController extends Controller
{
	use Notify;

	public function index()
	{
		$deposits = Deposit::with(['sender', 'receiver'])
			->latest()->paginate();
		return view('admin.deposit.index', compact('deposits'));
	}

	public function search(Request $request)
	{
		$filterData = $this->_filter($request);
		$search = $filterData['search'];
		$deposits = $filterData['deposits']
			->latest()
			->paginate();
		$deposits->appends($filterData['search']);
		return view('admin.deposit.index', compact('search', 'deposits'));
	}

	public function _filter($request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		$deposits = Deposit::with('sender', 'receiver')
			->when(isset($search['email']), function ($query) use ($search) {
				return $query->where('email', 'LIKE', "%{$search['email']}%");
			})
			->when(isset($search['utr']), function ($query) use ($search) {
				return $query->where('utr', 'LIKE', "%{$search['utr']}%");
			})
			->when(isset($search['min']), function ($query) use ($search) {
				return $query->where('amount', '>=', $search['min']);
			})
			->when(isset($search['max']), function ($query) use ($search) {
				return $query->where('amount', '<=', $search['max']);
			})
			->when(isset($search['status']), function ($query) use ($search) {
				return $query->where('status', $search['status']);
			})
			->when(isset($search['sender']), function ($query) use ($search) {
				return $query->whereHas('sender', function ($qry) use ($search) {
					$qry->where('name', 'LIKE', "%{$search['sender']}%");
				});
			})
			->when(isset($search['receiver']), function ($query) use ($search) {
				return $query->whereHas('receiver', function ($qry) use ($search) {
					$qry->where('name', 'LIKE', "%{$search['receiver']}%");
				});
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			});

		$data = [
			'search' => $search,
			'deposits' => $deposits,
		];
		return $data;
	}

	public function showByUser($userId)
	{
		$deposits = Deposit::with(['sender', 'receiver'])
			->where('user_id', $userId)
			->latest()
			->paginate();
		return view('admin.deposit.index', compact( 'deposits', 'userId'));
	}

	public function searchByUser(Request $request, $userId)
	{
		$filterData = $this->_filter($request);
		$search = $filterData['search'];
		$deposits = $filterData['deposits']
			->where('user_id', $userId)
			->latest()
			->paginate();
		$deposits->appends($filterData['search']);
		return view('admin.deposit.index', compact('search', 'deposits', 'userId'));
	}
}
