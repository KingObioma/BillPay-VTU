<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
	use HasFactory;

	public function sender()
	{
		return $this->belongsTo(Admin::class, 'admin_id', 'id');
	}

	public function receiver()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function transactional()
	{
		return $this->morphOne(Transaction::class, 'transactional');
	}

	public function depositable()
	{
		return $this->morphOne(Deposit::class, 'depositable');
	}
}
