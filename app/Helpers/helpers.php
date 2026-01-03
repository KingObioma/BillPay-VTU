<?php

use App\Models\BasicControl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

function template($asset = false)
{
	$activeTheme = config('basic.theme');
	if ($asset) return 'assets/themes/' . $activeTheme . '/';
	return 'themes.' . $activeTheme . '.';
}


function hex2rgba($color, $opacity = false)
{
	$default = 'rgb(0,0,0)';
	//Return default if no color provided
	if (empty($color))
		return $default;
	//Sanitize $color if "#" is provided
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}
	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
	} elseif (strlen($color) == 3) {
		$hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
	} else {
		return $default;
	}
	//Convert hexadec to rgb
	$rgb = array_map('hexdec', $hex);
	//Check if opacity is set(rgba or rgb)
	if ($opacity) {
		if (abs($opacity) > 1)
			$opacity = 1.0;
		$output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode(",", $rgb) . ')';
	}
	//Return rgb(a) color string
	return $output;
}

function collapsedMenu($routeNames = [], $segment = null)
{
	$lastSegment = last(request()->segments());
	$currentName = request()->route()->getName();
	if (isset($segment)) {
		return (in_array($currentName, $routeNames) && $lastSegment == $segment) ? 'active' : '';
	}
	return in_array($currentName, $routeNames) ? '' : 'collapsed';
}

function activeMenu($routeNames = [], $segment = null)
{
	$lastSegment = last(request()->segments());
	$currentName = request()->route()->getName();
	if (isset($segment)) {
		return (in_array($currentName, $routeNames) && $lastSegment == $segment) ? 'active' : '';
	}
	return in_array($currentName, $routeNames) ? 'active' : '';
}

if (!function_exists('isMenuActive')) {
	function isMenuActive($routes, $type = 0)
	{
		$class = [
			'0' => 'active',
			'1' => 'style=display:block',
			'2' => true
		];

		if (is_array($routes)) {
			foreach ($routes as $key => $route) {
				if (request()->routeIs($route)) {
					return $class[$type];
				}
			}
		} elseif (request()->routeIs($routes)) {
			return $class[$type];
		}

		if ($type == 1) {
			return 'style=display:none';
		} else {
			return false;
		}
	}
}

function menuFormater($value)
{
	return ucwords(str_replace(['-', '_'], ' ', $value));
}

function showMenu($routeNames = [])
{
	$currentName = request()->route()->getName();
	return in_array($currentName, $routeNames) ? 'show' : '';
}

function basicControl()
{
	$general = Cache::get('GeneralSetting');
	if (!$general) {
		$general = BasicControl::firstOrCreate(['id' => 1]);
		Cache::put('GeneralSetting', $general);
	}
	return $general;
}

function menuActive($routeName, $type = null)
{
	$class = 'active';
	if ($type == 3) {
		$class = 'selected';
	} elseif ($type == 2) {
		$class = 'has-arrow active';
	} elseif ($type == 1) {
		$class = 'in';
	}
	if (is_array($routeName)) {
		foreach ($routeName as $key => $value) {
			if (request()->routeIs($value)) {
				return $class;
			}
		}
	} elseif (request()->routeIs($routeName)) {
		return $class;
	}
}

if (!function_exists('getTitle')) {
	function getTitle($title)
	{
		return ucwords(preg_replace('/[^A-Za-z0-9]/', ' ', $title));
	}
}

if (!function_exists('getRoute')) {
	function getRoute($route, $params = null)
	{
		return isset($params) ? route($route, $params) : route($route);
	}
}
function strRandom($length = 12)
{
	$characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function code($length = 6)
{
	if ($length == 0) return 0;
	$min = pow(10, $length - 1);
	$max = 0;
	while ($length > 0 && $length--) {
		$max = ($max * 10) + 9;
	}
	return random_int($min, $max);
}

// function getFile($disk = 'local', $image = '')
// {
// 	try {
// 		if ($disk == 'local') {
// 			$localImage = asset('/assets/upload') . '/' . $image;
// 			return Storage::disk($disk)->exists($image) ? $localImage : asset(config('location.default'));
// 		} else {
// 			return Storage::disk($disk)->exists($image) ? Storage::disk($disk)->url($image) : asset(config('location.default'));
// 		}
// 	} catch (Exception $e) {
// 		return asset(config('location.default'));
// 	}
// }

function getFile($disk = 'local', $image = '')
{
	try {
		$path = public_path('assets/upload/' . $image);

		return file_exists($path)
			? asset('assets/upload/' . $image)
			: asset(config('location.default'));

	} catch (Exception $e) {
		return asset(config('location.default'));
	}
}



// $action 0 = deduct, 1 = Add //
function updateWallet($user_id, $amount, $action = 0)
{
	$user = \App\Models\User::find($user_id);
	$balance = 0;

	if ($action == 1) { //add money
		$balance = $user->balance + $amount;
		$user->balance = $balance;
	} elseif ($action == 0) { //deduct money
		$balance = $user->balance - $amount;
		$user->balance = $balance;
	}

	$user->save();
	return $balance;
}

function camelToWord($str)
{
	$arr = preg_split('/(?=[A-Z])/', $str);
	return trim(join(' ', $arr));
}


function title2snake($string)
{
	return Str::title(str_replace(' ', '_', $string));
}

function snake2Title($string)
{
	return Str::title(str_replace('_', ' ', $string));
}

function kebab2Title($string)
{
	return Str::title(str_replace('-', ' ', $string));
}

function recursive_array_replace($find, $replace, $array)
{
	if (!is_array($array)) {
		return str_replace($find, $replace, $array);
	}
	$newArray = [];
	foreach ($array as $key => $value) {
		$newArray[$key] = recursive_array_replace($find, $replace, $value);
	}
	return $newArray;
}

function getAmount($amount, $length = 0)
{
	if ($amount == 0) {
		return 0;
	}

	if ($length == 0) {
		preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($amount), $o);
		return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
	}

	return round($amount, $length);
}

function getMethodCurrency($gateway)
{
	foreach ($gateway->currencies as $key => $currency) {
		if (property_exists($currency, $gateway->currency)) {
			if ($key == 0) {
				return $gateway->currency;
			} else {
				return 'USD';
			}
		}
//		return 'N/A';
	}
}

function twoStepPrevious($deposit)
{
	return route('failed');
}

function wordTruncate($string, $offset = 0, $length = null)
{
	$words = explode(" ", $string);
	isset($length) ? array_splice($words, $offset, $length) : array_splice($words, $offset);
	return implode(" ", $words);
}

function linkToEmbed($string)
{
	$words = explode("/", $string);
	if (strpos($string, 'embed') == false) {
		array_splice($words, -1, 0, 'embed');
	}
	$words = str_ireplace('watch?v=', '', implode("/", $words));
	return $words;
}

function getIpInfo()
{
//	$ip = '210.1.246.42';
	$ip = null;
	$deep_detect = TRUE;

	if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
		$ip = $_SERVER["REMOTE_ADDR"];
		if ($deep_detect) {
			if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
				$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
	}
	$xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);

	$country = @$xml->geoplugin_countryName;
	$city = @$xml->geoplugin_city;
	$area = @$xml->geoplugin_areaCode;
	$code = @$xml->geoplugin_countryCode;
	$long = @$xml->geoplugin_longitude;
	$lat = @$xml->geoplugin_latitude;


	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$os_platform = "Unknown OS Platform";
	$os_array = array(
		'/windows nt 10/i' => 'Windows 10',
		'/windows nt 6.3/i' => 'Windows 8.1',
		'/windows nt 6.2/i' => 'Windows 8',
		'/windows nt 6.1/i' => 'Windows 7',
		'/windows nt 6.0/i' => 'Windows Vista',
		'/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
		'/windows nt 5.1/i' => 'Windows XP',
		'/windows xp/i' => 'Windows XP',
		'/windows nt 5.0/i' => 'Windows 2000',
		'/windows me/i' => 'Windows ME',
		'/win98/i' => 'Windows 98',
		'/win95/i' => 'Windows 95',
		'/win16/i' => 'Windows 3.11',
		'/macintosh|mac os x/i' => 'Mac OS X',
		'/mac_powerpc/i' => 'Mac OS 9',
		'/linux/i' => 'Linux',
		'/ubuntu/i' => 'Ubuntu',
		'/iphone/i' => 'iPhone',
		'/ipod/i' => 'iPod',
		'/ipad/i' => 'iPad',
		'/android/i' => 'Android',
		'/blackberry/i' => 'BlackBerry',
		'/webos/i' => 'Mobile'
	);
	foreach ($os_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$os_platform = $value;
		}
	}
	$browser = "Unknown Browser";
	$browser_array = array(
		'/msie/i' => 'Internet Explorer',
		'/firefox/i' => 'Firefox',
		'/safari/i' => 'Safari',
		'/chrome/i' => 'Chrome',
		'/edge/i' => 'Edge',
		'/opera/i' => 'Opera',
		'/netscape/i' => 'Netscape',
		'/maxthon/i' => 'Maxthon',
		'/konqueror/i' => 'Konqueror',
		'/mobile/i' => 'Handheld Browser'
	);
	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser = $value;
		}
	}

	$data['country'] = $country;
	$data['city'] = $city;
	$data['area'] = $area;
	$data['code'] = $code;
	$data['long'] = $long;
	$data['lat'] = $lat;
	$data['os_platform'] = $os_platform;
	$data['browser'] = $browser;
	$data['ip'] = request()->ip();
	$data['time'] = date('d-m-Y h:i:s A');

	return $data;
}


function checkTo($currencies, $selectedCurrency = 'USD')
{
	foreach ($currencies as $key => $currency) {
		if (property_exists($currency, strtoupper($selectedCurrency))) {
			return $key;
		}
	}
}

function slug($title)
{
	return Str::slug($title);
}

function diffForHumans($date)
{
	$lang = session()->get('lang');
	\Carbon\Carbon::setlocale($lang);
	return \Carbon\Carbon::parse($date)->diffForHumans();
}

function loopIndex($object)
{
	return ($object->currentPage() - 1) * $object->perPage() + 1;
}

function dateTime($date, $format = 'd/m/Y H:i')
{
	return date($format, strtotime($date));
}

function getPercent($total, $current)
{
	if ($current > 0 && $total > 0) {
		$percent = (($current * 100) / $total) ?: 0;
	} else {
		$percent = 0;
	}
	return round($percent, 0);
}

function resourcePaginate($data, $callback)
{
	return $data->setCollection($data->getCollection()->map($callback));
}

function clean($string)
{
	$string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function wordSplice($string, $qty = 2)
{
	$words = explode(" ", $string);
	$last_two_word = implode(' ', array_splice($words, -$qty));
	$except_last_two = implode(' ', $words);
	return [
		'normal' => $except_last_two,
		'highLights' => $last_two_word,
	];
}

if (!function_exists('getProjectDirectory')) {
	function getProjectDirectory()
	{
		return str_replace((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]", "", url("/"));
	}
}

if (!function_exists('getReloadlyAmount')) {
	function getReloadlyAmount($service, $amountId)
	{
		foreach ($service->extra_response as $res) {
			if ($service->service == 'AIRTIME') {
				$billMethod = $service->method;
				$convertRate = $billMethod->convert_rate->{$service->currency} ?? 1;
				return $convertRate * $amountId;
			} else {
				if ($res['id'] == $amountId) {
					return $res['amount'];
				}
			}
		}
	}
}

if (!function_exists('getReloadlyFixedAmountAirtime')) {
	function getReloadlyFixedAmountAirtime($res)
	{
		$fixedAmount = [];
		if (isset($res['fixedAmounts']) && count($res['fixedAmounts']) > 0) {
			if (isset($res['fixedAmountsDescriptions']) && count((array)$res['fixedAmountsDescriptions']) > 0) {
				foreach ($res['fixedAmountsDescriptions'] as $key => $fix) {
					$fixedAmount[] = [
						'id' => $key,
						'description' => $fix,
					];
				}
			} elseif (isset($res['localFixedAmounts']) && count((array)$res['localFixedAmounts']) > 0) {
				foreach ($res['localFixedAmounts'] as $key => $fix) {
					$fixedAmount[] = [
						'id' => $res['localFixedAmounts'][$key],
						'description' => $fix,
					];
				}
			}
		} elseif (isset($res['geographicalRechargePlans']) && count((array)$res['geographicalRechargePlans']) > 0) {
			$geos = (array)$res['geographicalRechargePlans'][0]->fixedAmountsDescriptions;
			foreach ($geos as $key => $fix) {
				$fixedAmount[] = [
					'id' => $key,
					'description' => $fix,
				];
			}
		}
		return $fixedAmount;
	}
}

if (!function_exists('getPhoneCode')) {
	function getPhoneCode($countryIso)
	{
		foreach (config('country') as $country) {
			if ($country['code'] == $countryIso) {
				return $country['phone_code'];
			}
		}
		return "+880";
	}
}

function renderCaptCha($rand)
{
	$captcha_code = '';
	$captcha_image_height = 50;
	$captcha_image_width = 130;
	$total_characters_on_image = 6;

	$possible_captcha_letters = 'bcdfghjkmnpqrstvwxyz23456789';
	$captcha_font = 'assets/monofont.ttf';

	$random_captcha_dots = 50;
	$random_captcha_lines = 25;
	$captcha_text_color = "0x142864";
	$captcha_noise_color = "0x142864";


	$count = 0;
	while ($count < $total_characters_on_image) {
		$captcha_code .= substr(
			$possible_captcha_letters,
			mt_rand(0, strlen($possible_captcha_letters) - 1),
			1);
		$count++;
	}


	$captcha_font_size = $captcha_image_height * 0.65;
	$captcha_image = @imagecreate(
		$captcha_image_width,
		$captcha_image_height
	);

	/* setting the background, text and noise colours here */
	$background_color = imagecolorallocate(
		$captcha_image,
		255,
		255,
		255
	);

	$array_text_color = hextorgb($captcha_text_color);
	$captcha_text_color = imagecolorallocate(
		$captcha_image,
		$array_text_color['red'],
		$array_text_color['green'],
		$array_text_color['blue']
	);

	$array_noise_color = hextorgb($captcha_noise_color);
	$image_noise_color = imagecolorallocate(
		$captcha_image,
		$array_noise_color['red'],
		$array_noise_color['green'],
		$array_noise_color['blue']
	);

	/* Generate random dots in background of the captcha image */
	for ($count = 0; $count < $random_captcha_dots; $count++) {
		imagefilledellipse(
			$captcha_image,
			mt_rand(0, $captcha_image_width),
			mt_rand(0, $captcha_image_height),
			2,
			3,
			$image_noise_color
		);
	}

	/* Generate random lines in background of the captcha image */
	for ($count = 0; $count < $random_captcha_lines; $count++) {
		imageline(
			$captcha_image,
			mt_rand(0, $captcha_image_width),
			mt_rand(0, $captcha_image_height),
			mt_rand(0, $captcha_image_width),
			mt_rand(0, $captcha_image_height),
			$image_noise_color
		);
	}

	/* Create a text box and add 6 captcha letters code in it */
	$text_box = imagettfbbox(
		$captcha_font_size,
		0,
		$captcha_font,
		$captcha_code
	);
	$x = ($captcha_image_width - $text_box[4]) / 2;
	$y = ($captcha_image_height - $text_box[5]) / 2;
	imagettftext(
		$captcha_image,
		$captcha_font_size,
		0,
		$x,
		$y,
		$captcha_text_color,
		$captcha_font,
		$captcha_code
	);

	/* Show captcha image in the html page */
// defining the image type to be shown in browser widow
	header('Content-Type: image/jpeg');
	imagejpeg($captcha_image); //showing the image
	imagedestroy($captcha_image); //destroying the image instance
	session()->put('captcha', $captcha_code);
}

function hextorgb($hexstring)
{
	$integar = hexdec($hexstring);
	return array("red" => 0xFF & ($integar >> 0x10),
		"green" => 0xFF & ($integar >> 0x8),
		"blue" => 0xFF & $integar);
}

function getCountryName($countryCode)
{
	foreach (config('country') as $country) {
		if ($country['code'] == $countryCode) {
			return $country['name'];
		}
	}
	return $countryCode;
}