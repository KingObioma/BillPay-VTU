<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
	use HasFactory, Translatable;

	protected $casts = [
		'description' => 'object'
	];

	public function scopeTemplateMedia()
	{
		$media = TemplateMedia::where('section_name', $this->section_name)->first();
		if (!$media) {
			return null;
		}
		return $media->description;
	}

	public function media()
	{
		return $this->hasOne(TemplateMedia::class, 'section_name', 'section_name');
	}
}
