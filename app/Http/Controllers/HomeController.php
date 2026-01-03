<?php

namespace App\Http\Controllers;


use App\Models\BillPay;
use App\Models\FirebaseNotify;
use App\Models\Kyc;
use App\Models\UserKyc;
use Carbon\Carbon;
use DateTime;
use App\Traits\Upload;
use App\Models\Content;
use App\Models\Language;
use App\Models\Template;
use App\Models\Subscribe;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ContentDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HomeController extends Controller
{
	use Upload, \App\Traits\BillPay;

	public function __construct()
	{
		$this->theme = template();
	}

	public function getTransactionChart(Request $request)
	{
		$start = $request->start;
		$end = $request->end;
		$user = Auth::user();

		$transactions = Transaction::select('created_at')
			->whereBetween('created_at', [$start, $end])
			->with(['transactional' => function (MorphTo $morphTo) {
				$morphTo->morphWith([
					BillPay::class => ['user'],
				]);
			}])
			->whereHasMorph('transactional',
				[
					BillPay::class,
				], function ($query, $type) use ($user) {
					if ($type === BillPay::class) {
						$query->where('user_id', $user->id);
					}
				})
			->groupBy([DB::raw("DATE_FORMAT(created_at, '%j')")])
			->selectRaw("SUM(CASE WHEN transactional_type like '%BillPay' THEN amount ELSE 0 END) as BillPay")
			->get()
			->groupBy([function ($query) {
				return $query->created_at->format('j');
			}]);

		$labels = [];
		$dataBillPay = [];
		$start = new DateTime($start);
		$end = new DateTime($end);

		for ($day = $start; $day <= $end; $day->modify('+1 day')) {
			$i = $day->format('j');
			$labels[] = $day->format('jS M');
			$currentBillPay = 0;
			if (isset($transactions[$i])) {
				foreach ($transactions[$i] as $key => $transaction) {
					$currentBillPay += $transaction->BillPay;
				}
			}
			$dataBillPay[] = round($currentBillPay, basicControl()->fraction_number);
		}

		$data['labels'] = $labels;
		$data['dataBillPay'] = $dataBillPay;


		return response()->json($data);
	}

	public function index()
	{
		$basic = basicControl();
		$fraction = $basic->fraction_number;
		$user = Auth::user();

		$bills = BillPay::own()->selectRaw('COUNT(CASE WHEN status = 2  THEN id END) AS pendingBills')
			->selectRaw('COUNT(CASE WHEN status = 3  THEN id END) AS completeBills')
			->selectRaw('COUNT(CASE WHEN status = 4  THEN id END) AS returnBills')
			->selectRaw('SUM(CASE WHEN payment_method_id = -1 AND status != 0 THEN pay_amount_in_base END) AS totalWalletPays')
			->get()->toArray();
		$data['billRecord'] = collect($bills)->collapse();

		$lastMonthBills = BillPay::own()->whereDate('created_at', '>=', Carbon::now()->subDays(30))->selectRaw('SUM(CASE WHEN status = 2  THEN pay_amount_in_base END) AS pendingBills')
			->selectRaw('SUM(CASE WHEN status = 3  THEN pay_amount_in_base END) AS completeBills')
			->selectRaw('SUM(CASE WHEN status = 4  THEN pay_amount_in_base END) AS returnBills')
			->get()->toArray();
		$data['lastMonthBillRecord'] = collect($lastMonthBills)->collapse();

		$last30 = date('Y-m-d', strtotime('-30 days'));
		$last7 = date('Y-m-d', strtotime('-7 days'));
		$today = today();
		$dayCount = date('t', strtotime($today));

		$transactions = Transaction::select('created_at')
			->whereMonth('created_at', $today)
			->with(['transactional' => function (MorphTo $morphTo) {
				$morphTo->morphWith([
					BillPay::class => ['user'],
				]);
			}])
			->whereHasMorph('transactional',
				[
					BillPay::class
				],
				function ($query, $type) use ($user) {
					if ($type === BillPay::class) {
						$query->where('user_id', auth()->id());
					}
				})
			->groupBy([DB::raw("DATE_FORMAT(created_at, '%j')")])
			->selectRaw("SUM(CASE WHEN transactional_type like '%BillPay' THEN amount ELSE 0 END) as BillPay")
			->get()
			->groupBy([function ($query) {
				return $query->created_at->format('j');
			}]);

		$labels = [];
		$dataBillPay = [];
		for ($i = 1; $i <= $dayCount; $i++) {
			$labels[] = date('jS M', strtotime(date('Y/m/') . $i));
			$currentBillPay = 0;
			if (isset($transactions[$i])) {
				foreach ($transactions[$i] as $key => $transaction) {
					$currentBillPay += $transaction->BillPay;
				}
			}
			$dataBillPay[] = round($currentBillPay, $fraction);
		}

		$data['basic'] = $basic;
		$data['labels'] = $labels;
		$data['dataBillPay'] = $dataBillPay;

		$chartOrders = BillPay::selectRaw("DATE_FORMAT(created_at, '%m') as month")
			->where('status', '!=', 0)
			->whereYear('created_at', $today)
			->groupBy([DB::raw("DATE_FORMAT(created_at, '%m')")])
			->selectRaw("COUNT((CASE WHEN status = 2 THEN id END)) AS pendingBills")
			->selectRaw("COUNT((CASE WHEN status = 4  THEN id END)) AS returnBills")
			->selectRaw("COUNT((CASE WHEN status = 3  THEN id END)) AS completeBills")
			->where('user_id', $user->id)
			->get()
			->groupBy('month');

		$data['monthLabels'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
		$data['yearLabels'] = ['01', '02', '03', '04', '05', '06', '07 ', '08', '09', '10', '11', '12'];

		$yearPendingBills = [];
		$yearReturnBills = [];
		$yearCompleteBills = [];


		foreach ($data['yearLabels'] as $yearLabel) {
			$currentYearPendingBills = 0;
			$currentYearReturnBills = 0;
			$currentYearCompleteBills = 0;

			if (isset($chartOrders[$yearLabel])) {
				foreach ($chartOrders[$yearLabel] as $key => $itemOrder) {
					$currentYearPendingBills += $itemOrder->pendingBills;
					$currentYearReturnBills += $itemOrder->returnBills;
					$currentYearCompleteBills += $itemOrder->completeBills;
				}
			}
			$yearPendingBills[] = round($currentYearPendingBills, 2);
			$yearReturnBills[] = round($currentYearReturnBills, 2);
			$yearCompleteBills[] = round($currentYearCompleteBills, 2);
		}

		$data['yearPendingBills'] = $yearPendingBills;
		$data['yearReturnBills'] = $yearReturnBills;
		$data['yearCompleteBills'] = $yearCompleteBills;

		$data['firebaseNotify'] = FirebaseNotify::first();
		return view($this->theme . 'user.home', $data);
	}

	public function home()
	{
		$templateSection = ['hero', 'feature', 'about-us', 'how-it-work', 'why-choose-us', 'testimonial', 'faq', 'blog', 'app-section', 'newsletter', 'contact', 'login', 'register', 'forget-password', 'reset-password', 'email-verification', 'sms-verification'];
		$data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

		$contentSection = ['feature', 'how-it-work', 'why-choose-us', 'testimonial', 'faq', 'blog', 'social', 'support', 'app-section'];
		$data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description', 'driver']);
				}])
			->get()->groupBy('content.name');

		$responses = config('billservices.' . $this->getActiveMethod()->code);
		$activeServices = [];

		// foreach ($this->getActiveServices() as $service) {
		// 	if (!isset($responses[$service])) continue;
		// 	$activeServices[$service] = [
		// 		'name' => $responses[$service]['name'],
		// 		'image' => asset(config('billservices.imagePath') . $responses[$service]['image'])
		// 	];
		// }


		foreach ($this->getActiveServices() as $service) {
			$activeServices[$service] = [
				'name' => $responses[$service]['name'],
				'image' => asset(config('billservices.imagePath') . $responses[$service]['image'])
			];
		}

		return view($this->theme . 'home', $data, compact('activeServices'));
	}

	public function about()
	{
		$templateSection = ['about-us', 'testimonial', 'faq', 'blog', 'app-section', 'newsletter'];
		$data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

		$contentSection = ['testimonial', 'faq', 'blog', 'app-section', 'newsletter'];
		$data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description', 'driver']);
				}])
			->get()->groupBy('content.name');
		return view($this->theme . 'about', $data);
	}

	public function features()
	{
		$templateSection = ['feature', 'app-section', 'newsletter'];
		$data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

		$contentSection = ['feature', 'app-section', 'newsletter'];
		$data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description', 'driver']);
				}])
			->get()->groupBy('content.name');
		return view($this->theme . 'feature', $data);
	}

	public function faq()
	{
		$templateSection = ['faq', 'app-section', 'newsletter'];
		$data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

		$contentSection = ['faq', 'app-section', 'newsletter'];
		$data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description', 'driver']);
				}])
			->get()->groupBy('content.name');
		return view($this->theme . 'faq', $data);
	}

	public function blog()
	{
		$data['title'] = "Blog";
		$contentSection = ['blog'];

		$templateSection = ['blog'];
		$data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

		$data['blogContents'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description', 'driver']);
				}])
			->orderBy('id', 'desc')
			->paginate(config('basic.paginate'));
		return view($this->theme . 'blog', $data);
	}

	public function blogDetails(Request $request, $id)
	{
		$search = $request->all();
		$getData = Content::findOrFail($id);
		$contentSection = [$getData->name];

		$contentDetail = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->where('content_id', $getData->id)
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'driver', 'description']);
				}])
			->get()->groupBy('content.name');

		$singleItem['title'] = optional($contentDetail[$getData->name][0]->description)->title;
		$singleItem['writer_name'] = optional($contentDetail[$getData->name][0]->description)->writer_name;
		$singleItem['writer_designation'] = optional($contentDetail[$getData->name][0]->description)->writer_designation;
		$singleItem['description'] = optional($contentDetail[$getData->name][0]->description)->description;
		$singleItem['date'] = dateTime(optional($contentDetail[$getData->name][0])->created_at, 'd M, Y');
		$singleItem['image'] = getFile(optional($contentDetail[$getData->name][0]->content->contentMedia)->driver, optional($contentDetail[$getData->name][0]->content->contentMedia->description)->image);
		$singleItem['writer_image'] = getFile(optional($contentDetail[$getData->name][0]->content->contentMedia)->driver, optional($contentDetail[$getData->name][0]->content->contentMedia->description)->writer_image);


		$contentSectionPopular = ['blog'];
		$popularContentDetails = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->where('content_id', '!=', $getData->id)
			->whereHas('content', function ($query) use ($contentSectionPopular) {
				return $query->whereIn('name', $contentSectionPopular);
			})
			->when(isset($search['title']), function ($query) use ($search) {
				$query->where('description', 'LIKE', '%' . $search['title'] . '%');
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'driver', 'description']);
				}])
			->get()->groupBy('content.name');

		return view($this->theme . 'blogDetails', compact('singleItem', 'popularContentDetails', 'getData'));
	}

	public function contact()
	{
		$templateSection = ['contact'];
		$templates = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
		$title = 'Contact Us';
		$contact = @$templates['contact'][0]->description;

		return view($this->theme . 'contact', compact('title', 'contact'));
	}

	public function contactSend(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:50',
			'email' => 'required|email|max:91',
			'subject' => 'required|max:100',
			'message' => 'required|max:1000',
		]);
		$requestData = Purify::clean($request->except('_token', '_method'));

		$basic = (object)config('basic');
		$basicEmail = $basic->sender_email;

		$name = $requestData['name'];
		$email_from = $requestData['email'];
		$subject = $requestData['subject'];
		$message = $requestData['message'] . "<br>Regards<br>" . $name;
		$from = $email_from;

		$headers = "From: <$from> \r\n";
		$headers .= "Reply-To: <$from> \r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$to = $basicEmail;

		if (@mail($to, $subject, $message, $headers)) {
			// echo 'Your message has been sent.';
		} else {
			//echo 'There was a problem sending the email.';
		}

		return back()->with('success', 'Mail has been sent');
	}

	public function subscribe(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'email' => 'required|email|min:8|max:100|unique:subscribes',
		];
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			session()->flash('error', 'Email Field is required');
			return back()->withErrors($validate)->withInput();
		}
		$purifiedData = (object)$purifiedData;

		$subscribe = new Subscribe();
		$subscribe->email = $purifiedData->email;
		$subscribe->save();

		return back()->with('success', 'Subscribed successfully');
	}

	public function logoUpdate(Request $request)
	{
		if ($request->isMethod('get')) {
			return view('admin.control_panel.logo');
		} elseif ($request->isMethod('post')) {

			if ($request->hasFile('logo')) {
				try {
					$old = 'logo.png';
					$image = $this->fileUpload($request->logo, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.logo_image' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Logo could not be uploaded.');
				}
			}
			if ($request->hasFile('footer_logo')) {
				try {
					$old = 'footer-logo.png';
					$image = $this->fileUpload($request->footer_logo, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.footer_image' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Footer Logo could not be uploaded.');
				}
			}
			if ($request->hasFile('admin_logo')) {
				try {
					$old = 'admin-logo.png';
					$image = $this->fileUpload($request->admin_logo, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.admin_logo' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Logo could not be uploaded.');
				}
			}
			if ($request->hasFile('favicon')) {
				try {
					$old = 'favicon.png';
					$image = $this->fileUpload($request->favicon, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.favicon_image' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Favicon could not be uploaded.');
				}
			}

			if ($request->hasFile('banner_image')) {
				try {
					$old = 'banner.png';
					$image = $this->fileUpload($request->banner_image, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.banner_image' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Banner Image could not be uploaded.');
				}
			}

			$fp = fopen(base_path() . '/config/basic.php', 'w');
			fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
			fclose($fp);

			return back()->with('success', 'Logo, favicon and breadcrumb has been updated.');
		}
	}


	public function seoUpdate(Request $request)
	{
		$basicControl = basicControl();
		if ($request->isMethod('get')) {
			return view('admin.control_panel.seo', compact('basicControl'));
		} elseif ($request->isMethod('post')) {

			$purifiedData = Purify::clean($request->all());
			$purifiedData['image'] = $request->image;
			$validator = Validator::make($purifiedData, [
				'meta_keywords' => 'nullable|min:1',
				'meta_description' => 'nullable|string|min:1',
				'social_title' => 'nullable|string|min:1',
				'social_description' => 'nullable|string|min:1',
				'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
			]);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput();
			}

			$purifiedData = (object)$purifiedData;

			$basicControl->meta_keywords = collect($request->meta_keywords)->unique()->filter(function ($item) {
				if (!in_array($item, ['', null])) {
					return $item;
				}
			})->join(',');
			$basicControl->meta_description = $purifiedData->meta_description;
			$basicControl->social_title = $purifiedData->social_title;
			$basicControl->social_description = $purifiedData->social_description;
			$basicControl->save();

			if ($request->hasFile('image')) {
				try {
					$old = 'meta.png';
					$image = $this->uploadImage($request->image, config('location.logo.path'), config('basic.default_file_driver'), $old, $old);
					if ($image) {
						config(['basic.logo_meta' => $image['path']]);
					}
				} catch (\Exception $exp) {
					return back()->with('error', 'Meta image could not be uploaded.');
				}
			}

			$fp = fopen(base_path() . '/config/basic.php', 'w');
			fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
			fclose($fp);
			return back()->with('success', 'Seo has been updated.');
		}
	}

	public function getLink($getLink = null, $id)
	{
		$getData = Content::findOrFail($id);

		$contentSection = [$getData->name];
		$contentDetail = ContentDetails::select('id', 'content_id', 'description', 'created_at')
			->where('content_id', $getData->id)
			->whereHas('content', function ($query) use ($contentSection) {
				return $query->whereIn('name', $contentSection);
			})
			->with(['content:id,name',
				'content.contentMedia' => function ($q) {
					$q->select(['content_id', 'description']);
				}])
			->get()->groupBy('content.name');

		$title = @$contentDetail[$getData->name][0]->description->title;
		$description = @$contentDetail[$getData->name][0]->description->description;
		return view($this->theme . 'getLink', compact('contentDetail', 'title', 'description'));
	}


	public function getTemplate($template = null)
	{
		$contentDetail = Template::where('section_name', $template)->firstOrFail();
		$title = @$contentDetail->description->title;
		$description = @$contentDetail->description->description;
		return view($this->theme . 'getLink', compact('contentDetail', 'title', 'description'));
	}

	public function setLanguage($code)
	{
		$language = Language::where('short_name', $code)->first();
		if (!$language) $code = 'US';
		session()->put('lang', $code);
		session()->put('rtl', $language ? $language->rtl : 0);
		return redirect()->back();
	}

	public function kycShow()
	{
		$data['kyc'] = Kyc::firstOrFail();
		return view($this->theme . 'user.kyc.show', $data);
	}

	public function kycStore(Request $request)
	{
		try {
			$kyc = Kyc::firstOrFail();
			$params = $kyc->input_form;
			$userKyc = new UserKyc();

			$rules = [];
			$inputField = [];

			$verifyImages = [];

			if ($params != null) {
				foreach ($params as $key => $cus) {
					$rules[$key] = [$cus->validation];
					if ($cus->type == 'file') {
						array_push($rules[$key], 'image');
						array_push($rules[$key], 'mimes:jpeg,jpg,png');
						array_push($rules[$key], 'max:2048');
						array_push($verifyImages, $key);
					}
					if ($cus->type == 'text') {
						array_push($rules[$key], 'max:191');
					}
					if ($cus->type == 'textarea') {
						array_push($rules[$key], 'max:300');
					}
					$inputField[] = $key;
				}
			}
			$this->validate($request, $rules);

			$reqField = [];
			$path = config('location.kyc.path');
			$collection = collect($request);

			if ($params != null) {
				foreach ($collection as $k => $v) {
					foreach ($params as $inKey => $inVal) {
						if ($k != $inKey) {
							continue;
						} else {
							if ($inVal->type == 'file') {
								if ($request->hasFile($inKey)) {
									try {
										$reqField[$inKey] = [
											'field_name' => $inVal->field_level,
											'field_value' => $this->fileUpload($request[$inKey], $path),
											'type' => $inVal->type,
										];
									} catch (\Exception $exp) {
										session()->flash('error', 'Could not upload your ' . $inKey);
										return back()->withInput();
									}
								}
							} else {
								$reqField[$inKey] = [
									'field_name' => $inVal->field_level,
									'field_value' => $v,
									'type' => $inVal->type,
								];
							}
						}
					}
				}
				$userKyc->kyc_info = $reqField;
			} else {
				$userKyc->kyc_info = null;
			}
			$user = Auth::user();
			DB::beginTransaction();
			$userKyc->user_id = $user->id;
			$userKyc->save();

			$user->kyc_verified = 1;
			$user->save();
			DB::commit();
			return redirect()->route('user.dashboard')->with('success', 'KYC Submitted');
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('alert', $e->getMessage());
		}
	}

	public function saveToken(Request $request)
	{
		$user = auth()->user();
		$user->fcm_token = $request->token;
		$user->save();
		return response()->json(['token saved successfully.']);
	}

}
