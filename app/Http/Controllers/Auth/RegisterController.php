<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterEditUserMail;
use App\Providers\RouteServiceProvider;
use App\Models\User;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpseclib3\Math\BigInteger\Engines\PHP;
use PHPUnit\TextUI\Help;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ROOT;
    public $sms;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except(['verifyEmail']);
//        $this->middleware('auth')->only(['verifyEmail']);
        $this->sms = new \SMS();
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    function create(array $data)
    {
        $token = bin2hex(openssl_random_pseudo_bytes(30));
        $user = User::create([
            'username' => @$data['username'],
            'name' => @$data['name'],
            'family' => @$data['family'],
            'email' => @$data['email'],
            'phone' => f2e($data['phone']),
            'password' => isset($data['password']) ? Hash::make(f2e($data['password'])) : null,
            'score' => \Helper::$initScore,
            'remember_token' => $token,
            'active' => true,
            'phone_verified' => true,
            'ref_code' => User::makeRefCode()
//                'expires_at' => $data['ex_date'] ? CalendarUtils::createCarbonFromFormat('Y/m/d', $data['ex_date'])->addDays(1)->timezone('Asia/Tehran') : null,
        ]);
        $user->setReferral(@$data['ref_code']);
        \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, 'user_created', $user);

        if ($user->email)
            Mail::to($user->email)->queue(new RegisterEditUserMail($token, 'register'));

        return $user;
    }

    public function verifyEmail($token, $from)
    {

        if (!$token) {
            return redirect('login')->with('error-alert', 'لینک نامعتبر است!');
        }


        $user = User::where('remember_token', $token)->first();


        if (!$user) {
            return redirect('login')->with('error-alert', 'کاربر یافت نشد و یا لینک منقضی شده است!');
        }

        $user->email_verified = 1;

        if ($user->save()) {

            if ($from == 'register')
                if (auth()->user())
                    return redirect('/')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
                else
                    return redirect('login')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
            else if ($from == 'edit')
                if (auth()->user())
                    return redirect('/')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
                else
                    return redirect('login')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');

        }

    }

    protected function registered(Request $request, $user)
    {

        $this->sms->deleteActivationSMS($user->phone);
        $this->guard()->login($user);
        if ($user->email) {
            $this->guard()->logout();
//        flash('flash-success', 'برای تکمیل ثبت نام لطفا ایمیل خود را تایید کنید پیام تایید ایمیل  برای شما ارسال شده است');
            return redirect('login')->with('success-alert', 'برای تکمیل ثبت نام لطفا ایمیل خود را تایید کنید پیام تایید ایمیل  برای شما ارسال شده است');
        } else
            return redirect('panel')->with('success-alert', 'ثبت نام شما با موفقیت انجام شد! میتوانید از قسمت تنظیمات اطلاعات خود را ویرایش کنید');

    }

    public function resendEmail(Request $request)
    {
//        $this->guard()->logout();
        $user = User::where('remember_token', $request->remember_token)->first();
//        dd($user);
//        return redirect('login')->with('flash-success', $user->token);
        if ($user) {
//            dispatch(new SendEmailJob($user))->onQueue('default');
            Mail::to($user->email)->send(new RegisterEditUserMail($user->remember_token, 'register'));

            return redirect('login')->with('success-alert', 'برای تکمیل ثبت نام لطفا ایمیل خود را تایید کنید پیام تایید ایمیل  برای شما ارسال شد');
        } else {
            return redirect('login')->with('error-alert', 'کاربر وجود ندارد!');

        }
    }

    public function register(Request $request)
    {

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

//        $this->guard()->login($user);
//        return redirect('/login')->with('flash-success', 'برای تکمیل ثبت نام لطفا ایمیل خود را تایید کنید پیام تایید ایمیل  برای شما ارسال شده است');


        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
//        $this->guard()->logout();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    function validator(array $data)
    {


        return Validator::make($data, [
//            'recaptcha' => ['required', new  Recaptcha()],
//            'g-recaptcha-response' => 'recaptcha',
            'name' => 'nullable|string|min:3|max:50',
            'family' => 'nullable|string|min:3|max:50',
            'username' => 'required|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username',
            'email' => ['nullable', 'string', 'email', 'min:6', 'max:50', 'unique:users,email'],
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone',
            'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where(function ($query) use ($data) {
                return $query->where('phone', $data['phone']);
            }),],
            'password' => 'nullable|string|min:6|max:50|confirmed',

        ],

            [
                'recaptcha.required' => 'لطفا گزینه من ربات نیستم را تایید نمایید',

                'name.required' => 'نام  نمی تواند خالی باشد',
                'name.string' => 'نام  نامعتبر است',
                'name.min' => 'نام  حداقل 3 حرف باشد',
                'name.max' => 'نام  حداکثر 50 حرف باشد',

                'family.required' => 'نام خانوادگی  نمی تواند خالی باشد',
                'family.string' => 'نام خانوادگی نامعتبر است',
                'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
                'family.max' => 'نام خانوادگی حداکثر 50 حرف باشد',

                'username.required' => 'نام کاربری نمی تواند خالی باشد',
                'username.min' => 'طول نام کاربری حداقل 5 باشد',
                'username.max' => 'طول نام کاربری حداکثر 50 باشد',
                'username.unique' => 'نام کاربری تکراری است',
//            'username.alpha_dash' => 'نام کاربری فقط شامل حروف، عدد و - و _ باشد',
                'username.regex' => 'نام کاربری با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
                'email.required' => 'ایمیل نمی تواند خالی باشد',
                'email.email' => 'ایمیل نامعتبر است',
                'email.min' => 'ایمیل حداقل 6 حرف باشد',
                'email.max' => 'ایمیل حداکثر 50 حرف باشد',
                'email.unique' => 'ایمیل تکراری است',

                'phone.required' => 'شماره تماس نمی تواند خالی باشد',
                'phone.numeric' => 'شماره تماس باید عدد باشد',
                'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.unique' => 'شماره تماس تکراری است',

                'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_with' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_if' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',


                'password.required' => 'گذرواژه  ضروری است',
                'password.string' => 'گذرواژه  نمی تواند فقط عدد باشد',
                'password.min' => 'گذرواژه  حداقل 6 حرف باشد',
                'password.max' => 'گذرواژه  حداکثر 30 حرف باشد',
                'password.confirmed' => 'گذرواژه با تکرار آن مطابقت ندارد',


            ]);
    }


}
