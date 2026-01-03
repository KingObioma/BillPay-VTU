<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPay extends Model
{
	use HasFactory;

	protected $guarded = ['id'];
	protected $casts = [
		'customer' => 'object'
	];

	public function scopeOwn($query)
	{
		return $query->where('user_id', auth()->id());
	}

	public function getCreatedAtAttribute($value)
	{
		return dateTime($value, 'd M Y H:i');
	}

	public function service()
	{
		return $this->belongsTo(BillService::class, 'service_id');
	}

	public function method()
	{
		return $this->belongsTo(BillMethod::class, 'method_id');
	}

	public function gateway()
	{
		return $this->belongsTo(Gateway::class, 'payment_method_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
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
