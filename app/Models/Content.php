<?php

namespace App\Models;

use App\Traits\ContentDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
	use HasFactory, ContentDelete;

	public function contentDetails()
	{
		return $this->hasMany(ContentDetails::class, 'content_id', 'id');
	}

	public function contentMedia()
	{
		return $this->hasOne(ContentMedia::class, 'content_id', 'id');
	}
}
