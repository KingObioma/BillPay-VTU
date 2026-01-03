<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
	use HasFactory;

	protected $table = 'user_kycs';
	protected $guarded = ['id'];
	protected $casts = [
		'kyc_info' => 'object',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
