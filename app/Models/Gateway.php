<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
	protected $fillable = [
		'code',
		'name',
		'sort_by',
		'image',
		'driver',
		'status',
		'parameters',
		'currencies',
		'extra_parameters',
		'currency',
		'symbol',
		'is_sandbox',
		'environment',
		'min_amount',
		'max_amount',
		'percentage_charge',
		'fixed_charge',
		'convention_rate'
	];
	protected $casts = [
		'parameters' => 'object',
		'currencies' => 'object',
		'extra_parameters' => 'object'
	];

	public function scopeAutomatic()
	{
		return $this->where('id', '<', 1000);
	}

	public function scopeManual()
	{
		return $this->where('id', '>=', 1000);
	}
}
