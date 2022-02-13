<?php

namespace App\Http\Controllers;

use App\Mail\RegisterEditUserMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use SMS;

class UserController extends Controller
{
    protected function edit(Request $request)
    {


        $request->validate([

            'name' => 'sometimes|string|min:3|max:50',
            'family' => 'sometimes|string|min:3|max:50',
            'sheba' => 'sometimes|nullable|numeric|digits:24',
            'cart' => 'sometimes|nullable|numeric|digits:16',
            'username' => 'sometimes|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username',
            'email' => ['sometimes', 'email', 'min:6', 'max:50', Rule::unique('users')->ignore(auth()->user())],
            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }),],

        ],
            [
                'name.string' => 'نام  نمی تواند عدد باشد',
                'name.min' => 'نام  حداقل 3 حرف باشد',
                'name.max' => 'نام  حداکثر 50 حرف باشد',

                'family.string' => 'نام خانوادگی  نمی تواند عدد باشد',
                'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
                'family.max' => 'نام خانوادگی حداکثر 50 حرف باشد',


                'username.min' => 'طول نام کاربری حداقل 5 باشد',
                'username.max' => 'طول نام کاربری حداکثر 50 باشد',
                'username.unique' => 'نام کاربری تکراری است',
//            'username.alpha_dash' => 'نام کاربری فقط شامل حروف، عدد و - و _ باشد',
                'username.regex' => 'نام کاربری با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
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

                'sheba.numeric' => 'شماره شبا باید عدد باشد',
                'sheba.digits' => 'شماره شبا 24 رقم باشد',
                'cart.numeric' => 'شماره کارت باید عدد باشد',
                'cart.digits' => 'شماره کارت 16 رقم باشد',
            ]);

//


        $user = auth()->user();


        if ($request->name) {
            if ($user->name == $request->name) return null;
            $user->name = $request->name;
            $this->dataEdited($user, 'user_edited', 'نام با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');
        }
        if ($request->family) {
            if ($user->family == $request->family) return null;
            $user->family = $request->family;
            $this->dataEdited($user, 'user_edited', 'نام خانوادگی با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($request->username) {
            if ($user->username == $request->username) return null;
            $user->username = $request->username;
            $this->dataEdited($user, 'user_edited', 'نام کاربری با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');
        } elseif ($request->email) {
            $emailChanged = $user->email != $request->email ? true : false;
            if ($emailChanged || !$user->email_verified) {
                $user->email = $request->email;
                $user->email_verified = false;
                $user->remember_token = bin2hex(openssl_random_pseudo_bytes(30));
                $user->save();
                Mail::to($request->email)->queue(new RegisterEditUserMail($user->remember_token, 'edit'));

                return redirect()->back()->with('success-alert', 'لینک تایید ایمیل به شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.');
            }
//                return redirect()->to('panel/user-settings')->with('success-alert', 'لینک تایید ایمیل به شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.');
        } elseif ($request->phone && $request->phone_verify) {
            $user->phone = $request->phone;
            (new SMS())->deleteActivationSMS($request->phone);
            $user->save();

        } elseif ($request->sheba) {
            if ($user->sheba == $request->sheba) return null;
            $user->sheba = $request->sheba;
            $this->dataEdited($user, 'user_edited', 'شبا با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');
        } elseif ($request->cart) {
            if ($user->cart == $request->cart) return null;
            $user->cart = $request->cart;
            $this->dataEdited($user, 'user_edited', 'کارت با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');
        }


    }
}
