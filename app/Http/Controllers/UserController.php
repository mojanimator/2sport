<?php

namespace App\Http\Controllers;

use App\Mail\RegisterEditUserMail;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use SMS;

class UserController extends Controller
{
    protected function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.numeric' => 'شناسه نامعتبر است',

        ]);
        $user = auth()->user();
        if ($user && ($user->role == 'ad' || $user->role == 'go') && $request->id)
            $user = User::find($request->id);
        else  return redirect()->back();
        Schema::disableForeignKeyConstraints();

        if (is_array($request->attach))
            foreach ($request->attach as $data) {
                $tmp = explode("_", $data);
                if (count($tmp) != 2) continue;
                if ($tmp[0] == 'player')
                    $d = Player::find($tmp[1]);

                if ($tmp[0] == 'coach')
                    $d = Coach::find($tmp[1]);
                if ($tmp[0] == 'club')
                    $d = Club::find($tmp[1]);
                if ($tmp[0] == 'shop')
                    $d = Shop::find($tmp[1]);

                if (!$d) continue;
                if ($d instanceof Shop) {
                    foreach (Product::where('shop_id', $d->id)->get() as $da) {
                        foreach ($da->docs as $doc) {
                            Doc::deleteFile($doc);
                        }
                        $da->delete();
                    }
                }
                foreach ($d->docs as $doc) {
                    Doc::deleteFile($doc);
                }
                $d->delete();

            }
        $user->delete();
        Schema::enableForeignKeyConstraints();

        return redirect('/panel/users');
    }


    protected function edit(Request $request)
    {
        $user = auth()->user();
        if ($user && ($user->role == 'ad' || $user->role == 'go') && $request->id)
            $user = User::find($request->id);


        $request->validate([

            'id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|string|min:3|max:50',
            'family' => 'sometimes|string|min:3|max:50',
            'sheba' => 'sometimes|nullable|numeric|digits:24',
            'cart' => 'sometimes|nullable|numeric|digits:16',
            'username' => 'sometimes|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username',
            'email' => ['sometimes', 'email', 'min:6', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }),],

        ],
            [
                'id.exists' => 'کاربر نامعتبر است',
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


        if ($request->name) {
            if ($user->name == $request->name) return null;
            $user->name = $request->name;
            $this->dataEdited($user, 'user_edited', 'نام با موفقیت ویرایش شد !');
        }
        if ($request->family) {
            if ($user->family == $request->family) return null;
            $user->family = $request->family;
            $this->dataEdited($user, 'user_edited', 'نام خانوادگی با موفقیت ویرایش شد !');

        } elseif ($request->username) {
            if ($user->username == $request->username) return null;
            $user->username = f2e($request->username);
            $this->dataEdited($user, 'user_edited', 'نام کاربری با موفقیت ویرایش شد  !');
        } elseif ($request->email) {
            $emailChanged = $user->email != $request->email ? true : false;
            if ($emailChanged || !$user->email_verified) {
                $user->email = $request->email;
                $user->email_verified = false;
                $user->remember_token = bin2hex(openssl_random_pseudo_bytes(30));
                $user->save();
                Mail::to($request->email)->queue(new RegisterEditUserMail($user->remember_token, 'edit'));

                return response()->json(['res' => 'لینک تایید برای شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.'], 200);
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
            $this->dataEdited($user, 'user_edited', 'شبا با موفقیت ویرایش شد !');
        } elseif ($request->cart) {
            if ($user->cart == $request->cart) return null;
            $user->cart = $request->cart;
            $this->dataEdited($user, 'user_edited', 'کارت با موفقیت ویرایش شد !');
        } elseif (isset($request->active)) {
            $user->active = $request->active;
            $this->dataEdited($user, 'user_edited', ' با موفقیت ویرایش شد !');

        }


    }

    protected function search(Request $request)
    {


        $page = $request->page;
        $paginate = $request->paginate;
        $search = $request->name;
        $orderBy = $request->order_by;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;

        $user = auth()->user();

        if ($user->role != 'ad' && $user->role != 'go')
            return null;

        if (!$paginate) {
            $paginate = 12;
        }
        if (!$page) {
            $page = 1;
        }
        if (!$dir) {
            $dir = 'DESC';
        }
        if (!$orderBy) {
            $orderBy = 'created_at';
        }


        $query = User::query();


        if (isset($search)) {
            foreach (explode(' ', $search) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('family', 'LIKE', '%' . $word . '%')
                        ->orWhere('username', 'LIKE', '%' . $word . '%')
                        ->orWhere('phone', 'LIKE', '%' . $word . '%')
                        ->orWhere('email', 'LIKE', '%' . $word . '%');
                });
            }
        }


        if (isset($active))
            $query = $query->where('active', (boolean)$active);

//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);


        $data = $query->paginate($paginate, ['*'], 'page', $page);

//        foreach ($data as $idx => $item) {
//            $img = \App\Models\Image::on(env('DB_CONNECTION'))->where('type', 'p')->where('for_id', $item->id)->inRandomOrder()->first();
//            if ($img)
//                $item['img'] = $img->id . '.jpg';
//        }

        return $data;
    }
}
