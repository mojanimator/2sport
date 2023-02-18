<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Coach;
use App\Models\Payment;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Shop;
use Firebase\JWT\JWT;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use SMS;
use Telegram;

class PaymentController extends Controller
{


    protected function confirmPay(Request $request)
    {
        myLog("-----url----------");
        myLog($request->fullUrl());


        $user = auth('api')->user() ?: auth()->user();

        $payment = null;
        if (isset($request->market) && $request->market == 'bazaar') {
            $order_id = uniqid();
            while (\App\Models\Payment::where('order_id', $order_id)->exists())
                $order_id = uniqid();
            $payment = \App\Models\Payment::create([
                'agency_id' => $request->agency_id,
                'province_id' => $request->province_id,
                'token_id' => $request->token_id,
                'order_id' => $order_id,
                'pay_for' => $request->pay_for,
                'pay_for_id' => $request->pay_for_id,
                'Shaparak_Ref_Id' => $request->market,
                'amount' => $request->amount,
                'user_id' => $user->id,
                'coupon_id' => $request->coupon_id,

            ]);
            $payment->code = 0;//success
        }

        if (!isset($request->market) || $request->market == 'bank') {
            if (isset($request->np_status) && $request->np_status == 'Unsuccessful') {
                Payment::where("order_id", $request->order_id)->delete();
                return redirect()->back()->with(['error-alert' => 'پرداخت ناموفق بود']);

            }

            $payment = \NextPay::confirmPay($request);
        }

        myLog("-----payment----------");
        myLog($payment->getAttributes());

        if ($payment && $payment->code == 0) { //verify success


            $tmp = explode('_', $payment->pay_for);
            if (count($tmp) != 2) return redirect(url("panel/"))->with('error-alert', 'پرداخت ناموفق بود');;
            $id = $payment->pay_for_id;
            $type = $tmp[0];
            $month = $tmp[1];
            if ($type == 'player')
                $data = \App\Models\Player::find($id);
            elseif ($type == 'coach')
                $data = \App\Models\Coach::find($id);
            elseif ($type == 'club')
                $data = \App\Models\Club::find($id);
            elseif ($type == 'shop')
                $data = \App\Models\Shop::find($id);
            else return redirect(url("panel/"))->with('error-alert', 'پرداخت ناموفق بود');;
            $now = \Carbon\Carbon::now();
            $time = $data->expires_at != null && $now->timestamp < $data->expires_at ? \Carbon\Carbon::parse($data->expires_at)->addDays($month * 30) : $now->addDays($month * 30);
            $data->expires_at = $time;
            $data->active = true;
            $data->save();
            if ($payment->coupon_id) {
                \App\Models\Coupon::where('id', $payment->coupon_id)->increment('used_times');
//                \App\Models\Coupon::where('id', $payment->coupon_id)->where('user_id', $payment->user_id)->update('used_at', $now);
            }
            \App\Models\Ref::where('invited_id', $payment->user_id)->where('invited_purchase_type', null)->update(['invited_purchase_type' => array_flip(Helper::$refMap)[$type], 'invited_purchase_months' => $month]);
            Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'payment', $payment);
//            if (!auth()->user())
//            auth()->login($user);

            if (auth()->user() && !isset($request->market))
                return redirect(url("panel/$type/edit/$id"))->with('success-alert', 'پرداخت شما با موفقیت انجام شد');
            //response json to application
            if (isset($request->market))
                return response()->json(['url' => url("panel/$type/edit/$id")]);
            return view('payment')->with('payment', $payment);

        } else {

            if (auth()->user())
                if (!$payment) return redirect(url("panel/"))->with('error-alert', isset($payment->code) && isset(\NextPay::MESSAGES[$payment->code]) ? \NextPay::MESSAGES[$payment->code] : 'پرداخت ناموفق بود');

            //response json to application
            if (isset($request->market))
                return response()->json(['error' => 'پرداخت ناموفق بود']);
            return view('payment')->with('payment', $payment);

        }
    }

    public function IAPPurchase(Request $request)
    {

        $type = $request->type;
        $id = $request->id;
        $month = $request->month;
        $coupon = $request->coupon;
        $phone = $request->phone;
        $market = $request->market;
        $price = $request->price;

        $user = auth()->user() ?: auth('api')->user();
        if ($user && !$phone)
            $phone = $user->phone;
        if (!$user)
            $user = \App\Models\User::where('phone', $phone)->first();
        if (!$user)
            return response()->json(['errors' => ['error' => ['کاربر نامعتبر است']]], 422);

        $order_id = uniqid();
        while (\App\Models\Payment::where('order_id', $order_id)->exists())
            $order_id = uniqid();
        $v = $price ?: Setting::firstOrNew(['key' => "${type}_${month}_price"])->value;
        $price = $v != null ? $v : -1;

        if ($price == -1)
            return response()->json(['errors' => ['error' => ['نوع اشتراک نامعتبر است']]], 422);

        $c = \App\Models\Coupon::where('code', $coupon)->first();
        $price = self::makeDiscount($price, $c);

        if (strpos($type, 'player') !== false)
            $data = Player::find($id);
        elseif (strpos($type, 'coach') !== false)
            $data = Coach::find($id);
        elseif (strpos($type, 'club') !== false)
            $data = Club::find($id);
        elseif (strpos($type, 'shop') !== false)
            $data = Shop::find($id);

        if (!isset($data))
            return response()->json(['errors' => ['error' => ['موردی یافت نشد.']]], 422);

        if ($price == 0) {
            $now = \Carbon\Carbon::now();


            $time = $data->expires_at != null && $now->timestamp < $data->expires_at ? \Carbon\Carbon::parse($data->expires_at)->addDays($month * 30) : $now->addDays($month * 30);
            $data->active = false;
            $data->expires_at = $time;
            $data->save();

            if ($c && $c->user_id != null && $c->user_id == $user->id && $c->used_at == null) {
                $c->used_at = \Carbon\Carbon::now();
                $c->save();
            }
            $payment = \App\Models\Payment::create([
                'agency_id' => $user->agency_id,
                'province_id' => $data->province_id,
                'token_id' => null,
                'order_id' => $order_id,
                'amount' => 0,
                'user_id' => $user->id,
                'pay_for' => "${type}_${month}",
                'pay_for_id' => $id,
                'created_at' => $now,
                'coupon_id' => isset($c->id) ? $c->id : null,

            ]);
            (new SMS())->deleteActivationSMS($phone);
//            \App\Models\Ref::where('invited_id', $user->id)->where('invited_purchase_type', null)->update(['invited_purchase_type' => array_flip(Helper::$refMap)[$type], 'invited_purchase_months' => $month]);
            Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'payment', $payment);
            redirect("/panel/$type/edit/$id")->with('success-alert', 'پرداخت شما با موفقیت انجام شد و در صف فعالسازی قرار گرفت');
            return response()->json(['url' => url("panel/$type/edit/$id")], 200);

        }


        if ($market == 'bazaar') {

            $token = $this->getCafeBazaarDiscountToken([
                'sku' => "${type}_${month}_price",
                'price' => $price * 10
            ]);

            return response()->json([
                'dynamicPriceToken' => $token,
                'sku' => "${type}_${month}_price",
                'rsa' => env('BAZAAR_RSA'),
                'agency_id' => $user->agency_id,
                'province_id' => $data->province_id,
                'order_id' => $order_id,
                'pay_for' => "${type}_${month}",
                'pay_for_id' => $id,
                'coupon_id' => isset($c->id) ? $c->id : null,
                'market' => 'bazaar',
                'amount' => $price,
            ], 200);

        } else {

            return \NextPay::makePay((object)[
                'payer_name' => $user->name ? $user->name . ' ' . $user->family : $user->username,
                'customer_phone' => $user->phone,
                'phone' => $phone,
                'order_id' => $order_id,
                'amount' => $price,
                'data_id' => $id,
                'user_id' => $user->id,
                'pay_for' => "${type}_${month}",
                'data_province_id' => $data->province_id,
                'coupon_id' => isset($c->id) ? $c->id : null,
                'user_agency_id' => $user->agency_id,
                'market' => $market,
            ]);

        }
    }

    private static function makeDiscount($price, $c)
    {

        if ($price == 0 || !$c)
            return $price;
        $user_id = optional(auth()->user() ?: auth('api')->user())->id;
        if (!$c || $c->used_at != null || $c->discount_percent == 0 || ($c->expires_at && $c->expires_at < \Carbon\Carbon::now()->timestamp) || \App\Models\Payment::whereNotNull('user_id')->where('user_id', $user_id)->where('coupon_id', $c->id)->exists())
            return $price;
        if ($c->discount_percent == 100)
            return 0;
        $discount = round($price * $c->discount_percent / 100);
        if ($c->limit_price && $c->limit_price < $discount)
            return $price - $c->limit_price;
        return $price - $discount;

    }

    private function getCafeBazaarDiscountToken($data)
    {
        $redirect = 'https://2sport.ir/payment';
        $url = 'https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize/?response_type=code&access_type=offline&redirect_uri=' . $redirect . '&client_id=' . env('BAZAAR_CLIENT_ID');
        $payload = [
            'price' => $data['price'],
            'package_name' => \Helper::$PACKAGE_NAME,
            'sku' => $data['sku'],
            'exp' => Carbon::now()->addDays(30)->timestamp,
            'nonce' => random_int(100000, 9999999999),
//            'account_id' => ''
        ];

        $enc = JWT::encode($payload, env('BAZAAR_JWT'), 'HS256');

        return $enc;

    }
}
