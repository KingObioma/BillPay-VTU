<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class SubscribeController extends Controller
{
	public function index()
	{
		$subscribers = Subscribe::latest()->paginate();
		return view('admin.subscribe.index', compact('subscribers'));
	}

	public function search(Request $request)
	{
		$filterData = $this->_filter($request);
		$search = $filterData['search'];
		$subscribers = $filterData['subscribers']
			->latest()
			->paginate();
		$subscribers->appends($filterData['search']);
		return view('admin.subscribe.index', compact('search', 'subscribers'));
	}

	public function _filter($request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		$subscribers = Subscribe::when(isset($search['email']), function ($query) use ($search) {
			return $query->where('email', 'LIKE', "%{$search['email']}%");
		})->when($created_date == 1, function ($query) use ($search) {
			return $query->whereDate("created_at", $search['created_at']);
		});

		$data = [
			'subscribers' => $subscribers,
			'search' => $search,
		];
		return $data;
	}

	public function sendMailSubscribe(Request $request, subscribe $subscribe = null)
	{
		if ($request->isMethod('get')) {
			return view('admin.subscribe.sendMail', compact('subscribe'));
		} elseif ($request->isMethod('post')) {
			$purifiedData = Purify::clean($request->all());
			$validator = Validator::make($purifiedData, [
				'subject' => 'required|min:5',
				'template' => 'required|min:10',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}

			$purifiedData = (object)$purifiedData;
			$subject = $purifiedData->subject;
			$template = $purifiedData->template;

			$basic = basicControl();
			$email_from = $basic->sender_email;
			$email_body = json_decode($basic->email_description);

			if (isset($subscribe)) {
				$this->subscriberMail($subscribe, $email_body, $template, $email_from, $subject);
			} else {
				$subscribers = Subscribe::all();
				foreach ($subscribers as $subscribe) {
					$this->subscriberMail($subscribe, $email_body, $template, $email_from, $subject);
				}
			}
			return redirect(route('subscribe.index'))->with('success', 'Email send successfully to subscriber');
		}
	}

	public function subscriberMail($subscribe, $email_body, $template, $email_from, $subject)
	{
		$name = explode('@', $subscribe->email)[0];
		$message = str_replace("[[name]]", $name, $email_body);
		$message = str_replace("[[message]]", $template, $message);
		Mail::to($subscribe->email)->queue(new SendMail($email_from, $subject, $message));
	}
}
