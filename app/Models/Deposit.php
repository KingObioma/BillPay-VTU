<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
	use HasFactory;

	protected $guarded = ['id'];
	protected $casts = [
		'detail' => 'object'
	];

	public function scopeOwn($query)
	{
		return $query->where('user_id', auth()->id());
	}

	public function transactional()
	{
		return $this->morphOne(Transaction::class, 'transactional');
	}

	public function depositable()
	{
		return $this->morphTo();
	}

	public function receiver()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function gateway()
	{
		return $this->belongsTo(Gateway::class, 'payment_method_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
