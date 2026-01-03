<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class ManualRecaptchaController extends Controller
{
	public function reCaptCha(Request $request)
	{
		renderCaptCha($request->rand);
	}
}
