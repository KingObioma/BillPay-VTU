<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
	use HasFactory;

	public function transactional()
	{
		return $this->morphOne(Transaction::class, 'transactional');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function admin()
	{
		return $this->belongsTo(Admin::class, 'admin_id', 'id');
	}

	public function payoutMethod()
	{
		return $this->belongsTo(PayoutMethod::class, 'payout_method_id', 'id');
	}
}
