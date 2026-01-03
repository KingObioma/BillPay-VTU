<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotifyTemplate;
use App\Models\Template;
use App\Providers\RouteServiceProvider;
use App\Traits\Notify;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
	use Notify;

	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers, RedirectsUsers, ThrottlesLogins;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = RouteServiceProvider::HOME;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\View\View
	 */
	public function showLoginForm(Request $request)
	{
		$template = Template::where('section_name', 'login')->first() ?? null;
		return view(template() . 'auth.login', compact('template'));
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function login(Request $request)
	{
		$this->validateLogin($request);

		if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);
			return $this->sendLockoutResponse($request);
		}

		if ($this->guard()->validate($this->credentials($request))) {
			if (Auth::attempt([$this->username() => $request->identity, 'password' => $request->password, 'status' => 1])) {

				auth()->user()->profile()->update([
					'last_login_at' => date('Y-m-d H:i:s'),
					'last_login_ip' => $request->getClientIp(),
				]);


				return $this->sendLoginResponse($request);
			} else {
				return back()
					->withInput()
					->withErrors(['password' => 'You are banned from this application. Please contact with system Administrator'])
					->with('status', 'You are banned from this application. Please contact with system Administrator.');
			}
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}


	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		$login = request()->input('identity');
		$field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		request()->merge([$field => $login]);
		return $field;
	}

	protected function validateLogin(Request $request)
	{
		$rules[$this->username()] = 'required';
		$rules ['password'] = 'required';
		$request->validate($rules);
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param mixed $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user)
	{
		$user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
		$user->save();
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @return \Illuminate\Contracts\Auth\StatefulGuard
	 */
	protected function guard()
	{
		return Auth::guard('web');
	}

	public function logout(Request $request)
	{
		$this->guard('guard')->logout();
		$request->session()->invalidate();

		session()->put('status', trans('Logout successfully'));
		return $this->loggedOut($request) ?: redirect()->route('login');
	}
}
