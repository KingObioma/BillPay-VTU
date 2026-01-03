<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Traits\Notify;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, Notify;

	protected $appends = ['mobile'];

	public function siteNotificational()
	{
		return $this->morphOne(SiteNotification::class, 'siteNotificational', 'site_notificational_type', 'site_notificational_id');
	}

	public function profile()
	{
		return $this->hasOne(UserProfile::class, 'user_id', 'id');
	}

	public function getMobileAttribute()
	{
		return optional($this->profile)->phone_code . optional($this->profile)->phone;
	}

	public function profilePicture()
	{
		$disk = $this->profile->driver;
		$image = $this->profile->profile_picture;

		try {
			if ($disk == 'local') {
				$localImage = asset('/assets/upload') . '/' . $image;
				return \Illuminate\Support\Facades\Storage::disk($disk)->exists($image) ? $localImage : asset(config('location.default'));
			} else {
				return \Illuminate\Support\Facades\Storage::disk($disk)->exists($image) ? Storage::disk($disk)->url($image) : asset(config('location.default'));
			}
		} catch (\Exception $e) {
			return asset(config('location.default'));
		}
	}

	protected $guarded = ['id'];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'email_key' => 'array',
		'sms_key' => 'array',
		'push_key' => 'array',
		'in_app_key' => 'array',
	];

	public function sendPasswordResetNotification($token)
	{
		$this->mail($this, 'PASSWORD_RESET', $params = [
			'message' => '<a href="' . url('password/reset', $token) . '?email=' . $this->email . '" target="_blank">Click To Reset Password</a>'
		]);

		/*
		$this->mail($this, 'PASSWORD_RESET', $params = [
//			'message' => '<a href="'.url('user/password/reset',$token).'" target="_blank">Click To Reset Password</a>',
			'message' => '<a href="'.url('user/password/reset',$token).'?email='.$this->email.'" target="_blank">Click To Reset Password</a>'

		]);

//			'message' => '<a href="'.url('user/password/reset',$token).'?email='.$this->email.'" target="_blank">Click To Reset Password</a>'

		$this->notify(new ResetPasswordNotification($token));
		*/
	}
}
