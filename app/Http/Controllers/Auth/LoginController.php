<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\CalendarUtils;

class LoginController extends Controller
{
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::PANEL;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = /*Cart::count() > 0 ? route('panel/order') :*/
            RouteServiceProvider::PANEL;
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();

    }

    public function authenticated(Request $request, $user)
    {

        if (!$user->active) {
            auth()->logout();
            return back()->with('error-alert', ' حساب کاربری شما غیر فعال است. از طریق لینک های پایین صفحه با ما تماس بگیرید ')
                ->with('token', $user->remember_token);
        }
        if ($user->email && !$user->email_verified) {
//            auth()->logout();
            return redirect('panel/user-settings')->with('error-alert', 'ایمیل شما هنوز تایید نشده است. لطفا روی لینک تایید که به ایمیل شما ارسال شده است کلیک کنید و یا آن را تغییر دهید')
                ->with('token', $user->remember_token);
        }
        if (!$user->phone_verified) {
//            auth()->logout();
            return redirect('panel/user-settings')->with('error-alert', 'تلفن شما هنوز تایید نشده است. لطفا از قسمت تنظیمات، آن را تایید کنید')
                ->with('token', $user->remember_token);
        }
//        dd(CalendarUtils::createCarbonFromFormat('Y/m/d', $user->expires_at));
        if ($user->expires_at && CalendarUtils::createCarbonFromFormat('Y/m/d', $user->expires_at) < Carbon::now()) {
            auth()->logout();
            return back()->with('error-alert', ' اعتبار شما منقضی شده است');
        }


        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        if (filter_var($login, FILTER_VALIDATE_EMAIL))
            $fieldType = 'email';
        elseif (starts_with($login, '09') && is_numeric($login))
            $fieldType = 'phone';
        else
            $fieldType = 'username';


//        if ($fieldType == 'username') {
//            str_replace('@', '', $login);
//            $login = '@' . $login;
//        }

        request()->merge([$fieldType => $login]);
        request()->merge(['password' => f2e(request()->input('password'))]);

        return $fieldType;
    }

    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|' . ($this->username() == 'email' ? 'email' : ''),
            'password' => 'required|string',
        ], [
            'username.required' => 'نام کاربری، ایمیل یا شماره تماس نامعتبر است',
            'password.required' => 'گذرواژه یا کد تایید نا معتبر است',
            'email.email' => 'ایمیل  نامعتبر است',
        ]);
    }

    protected function credentials(Request $request)
    {

        return $request->only($this->username(), 'password');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

//    protected function credentials()
//    {
//        $username = $this->username();
//        $credentials = request()->only($username, 'password');
//        if (isset($credentials[$username])) {
//            $credentials[$username] = strtolower($credentials[$username]);
//        }
//        return $credentials;
//    }
}
