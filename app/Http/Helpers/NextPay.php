<?php

use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Shop;
use Illuminate\Http\Request;


class  NextPay
{
    const PAYMENT_LINK = 'https://nextpay.org/nx/gateway/payment/';
    const TOKEN_LINK = 'https://nextpay.org/nx/gateway/token';
    const VERIFY_LINK = 'https://nextpay.org/nx/gateway/verify';


    /**
     *
     *
     * 1-make token
     */
    public static function makePay(Request $request)
    {

        $type = $request->type;
        $id = $request->id;
        $month = $request->month;
        $coupon = $request->coupon;
        $phone = $request->phone;

        $user = auth()->user();
        if (!$user)
            $user = \App\Models\User::where('phone', $phone)->first();
        $order_id = uniqid();
        while (\App\Models\Payment::where('order_id', $order_id)->exists())
            $order_id = uniqid();

        $price = Setting::firstOrNew(['key' => "${type}_${month}_price"])->value ?: -1;
        if ($price == -1)
            return response()->json(['errors' => ['error' => ['نوع اشتراک نامعتبر است']]], 422);

        $c = \App\Models\Coupon::where('code', $coupon)->first();
        $price = self::makeDiscount($price, $c);

        if ($price == 0) {
            $now = \Carbon\Carbon::now();

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

            $time = $data->expires_at != null && $now->timestamp < $data->expires_at ? \Carbon\Carbon::parse($data->expires_at)->addDays($month * 30) : $now->addDays($month * 30);
            $data->active = false;
            $data->expires_at = $time;
            $data->save();

            if ($c && $c->user_id != null && $c->user_id == $user->id && $c->used_at == null) {
                $c->used_at = \Carbon\Carbon::now();
                $c->save();
            }
            $payment = \App\Models\Payment::create([
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
            \App\Models\Ref::where('invited_id', $user->id)->where('invited_purchase_type', null)->update(['invited_purchase_type' => array_flip(Helper::$refMap)[$type], 'invited_purchase_months' => $month]);
            Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'payment', $payment);
            redirect("/panel/$type/edit/$id")->with('success-alert', 'پرداخت شما با موفقیت انجام شد و در صف فعالسازی قرار گرفت');
            return response()->json(['url' => "/panel/$type/edit/$id"], 200);

        }


        $params = [
            'api_key' => env('NEXTPAY_API'),
            'order_id' => $order_id,
            'amount' => $price,
            'callback_uri' => url('payment'),
            'currency' => 'IRT',
            'customer_phone' => $user->phone,
            'payer_name' => $user->name ? $user->name . ' ' . $user->family : $user->username,
            'auto_verify' => false,
            'custom_json_fields' => json_encode(['user_id' => $user->id, 'data_id' => $id, 'pay_for' => "${type}_${month}", 'coupon_id' => isset($c->id) ? $c->id : null]),
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::TOKEN_LINK,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);

        if ($response && $response->code == -1) { //send user to bank page
            \App\Models\Payment::create([
                'token_id' => $response->trans_id,
                'order_id' => $order_id,
                'pay_for' => "${type}_${month}",
                'pay_for_id' => $id,
            ]);
            (new SMS())->deleteActivationSMS($phone);
            return response()->json(['url' => self::PAYMENT_LINK . $response->trans_id], 200);
        } else {
            if (!$response)
                return response()->json(['errors' => ['error' => ['لطفا اتصال به اینترنت را بررسی کنید']]], 422);

            $message = self::MESSAGES[$response->code];

            return response()->json(['errors' => ['error' => [$message]]], 422);

        }
//        return redirect(self::PAYMENT_LINK);
    }

    public static function confirmPay($result)
    {


        $params = [
            'api_key' => env('NEXTPAY_API'),
            'trans_id' => $result->trans_id,
//            'order_id' => $result->order_id,
            'amount' => $result->amount,
            'currency' => 'IRT',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::VERIFY_LINK,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);
        if ($response && $response->code == 0) { //verify success
            $payment = \App\Models\Payment::where('order_id', $response->order_id)->first();
            if (!$payment)
                return;
            $response->custom = isset($response->custom) ? json_decode($response->custom) : $response->custom;
            $payment->amount = $response->amount;
            $payment->card_holder = $response->card_holder;
            $payment->Shaparak_Ref_Id = $response->Shaparak_Ref_Id;
            $payment->user_id = isset($response->custom->user_id) ? $response->custom->user_id : null;
            $payment->pay_for = isset($response->custom->pay_for) ? $response->custom->pay_for : '';
            $payment->pay_for_id = isset($response->custom->data_id) ? $response->custom->data_id : null;
            $payment->coupon_id = isset($response->custom->coupon_id) ? $response->custom->coupon_id : null;
            $payment->save();

            $tmp = explode('_', $payment->pay_for);
            if (count($tmp) != 2) return;
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
            else return;
            $now = \Carbon\Carbon::now();
            $time = $data->expires_at != null && $now->timestamp < $data->expires_at ? \Carbon\Carbon::parse($data->expires_at)->addDays($month * 30) : $now->addDays($month * 30);
            $data->expires_at = $time;
            $data->active = false;
            $data->save();
            \App\Models\Ref::where('invited_id', $payment->user_id)->where('invited_purchase_type', null)->update(['invited_purchase_type' => array_flip(Helper::$refMap)[$type], 'invited_purchase_months' => $month]);
            Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'payment', $payment);

            return redirect(url("panel/$type/edit/$id"))->with('success-alert', 'پرداخت شما با موفقیت انجام شد و در صف تایید قرار گرفتید');

        } else {
            if (!$response) return;
            return redirect(url("panel/"))->with('error-alert', self::MESSAGES[$response->code]);

        }
    }

    private static function makeDiscount($price, $c)
    {
        if ($price == 0 || !$c)
            return $price;

        if (!$c || $c->used_at != null || $c->discount_percent == 0 || ($c->expires_at && $c->expires_at < \Carbon\Carbon::now()->timestamp) || \App\Models\Payment::where('user_id', auth()->user()->id)->where('coupon_id', $c->id)->exists())
            return $price;
        if ($c->discount_percent == 100)
            return 0;
        $discount = round($price * $c->discount_percent / 100);
        if ($c->limit_price && $c->limit_price < $discount)
            return $price - $c->limit_price;
        return $price - $discount;

    }

    const   MESSAGES = [
        0 => 'پرداخت تکمیل و با موفقیت انجام شده است',
        -1 => 'منتظر ارسال تراکنش و ادامه پرداخت',
        -2 => 'پرداخت رد شده توسط کاربر یا بانک',
        -3 => 'پرداخت در حال انتظار جواب بانک',
        -4 => 'پرداخت لغو شده است',
        -20 => 'کد api_key ارسال نشده است',
        -21 => 'کد trans_id ارسال نشده است',
        -22 => 'مبلغ ارسال نشده',
        -23 => 'لینک ارسال نشده',
        -24 => 'مبلغ صحیح نیست',
        -25 => 'تراکنش قبلا انجام و قابل ارسال نیست',
        -26 => 'مقدار توکن ارسال نشده است',
        -27 => 'شماره سفارش صحیح نیست',
        -28 => 'مقدار فیلد سفارشی [custom_json_fields] از نوع json نیست',
        -29 => 'کد بازگشت مبلغ صحیح نیست',
        -30 => 'مبلغ کمتر از حداقل پرداختی است',
        -31 => 'صندوق کاربری موجود نیست',
        -32 => 'مسیر بازگشت صحیح نیست',
        -33 => 'کلید مجوز دهی صحیح نیست',
        -34 => 'کد تراکنش صحیح نیست',
        -35 => 'ساختار کلید مجوز دهی صحیح نیست',
        -36 => 'شماره سفارش ارسال نشد است',
        -37 => 'شماره تراکنش یافت نشد',
        -38 => 'توکن ارسالی موجود نیست',
        -39 => 'کلید مجوز دهی موجود نیست',
        -40 => 'کلید مجوزدهی مسدود شده است',
        -41 => 'خطا در دریافت پارامتر، شماره شناسایی صحت اعتبار که از بانک ارسال شده موجود نیست',
        -42 => 'سیستم پرداخت دچار مشکل شده است',
        -43 => 'درگاه پرداختی برای انجام درخواست یافت نشد',
        -44 => 'پاسخ دریاف شده از بانک نامعتبر است',
        -45 => 'سیستم پرداخت غیر فعال است',
        -46 => 'درخواست نامعتبر',
        -47 => 'کلید مجوز دهی یافت نشد [حذف شده]',
        -48 => 'نرخ کمیسیون تعیین نشده است',
        -49 => 'تراکنش مورد نظر تکراریست',
        -50 => 'حساب کاربری برای صندوق مالی یافت نشد',
        -51 => 'شناسه کاربری یافت نشد',
        -52 => 'حساب کاربری تایید نشده است',
        -60 => 'ایمیل صحیح نیست',
        -61 => 'کد ملی صحیح نیست',
        -62 => 'کد پستی صحیح نیست',
        -63 => 'آدرس پستی صحیح نیست و یا بیش از ۱۵۰ کارکتر است',
        -64 => 'توضیحات صحیح نیست و یا بیش از ۱۵۰ کارکتر است',
        -65 => 'نام و نام خانوادگی صحیح نیست و یا بیش از ۳۵ کاکتر است',
        -66 => 'تلفن صحیح نیست',
        -67 => 'نام کاربری صحیح نیست یا بیش از ۳۰ کارکتر است',
        -68 => 'نام محصول صحیح نیست و یا بیش از ۳۰ کارکتر است',
        -69 => 'آدرس ارسالی برای بازگشت موفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است',
        -70 => 'آدرس ارسالی برای بازگشت ناموفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است',
        -71 => 'موبایل صحیح نیست',
        -72 => 'بانک پاسخگو نبوده است لطفا با نکست پی تماس بگیرید',
        -73 => 'مسیر بازگشت دارای خطا میباشد یا بسیار طولانیست',
        -90 => 'بازگشت مبلغ بدرستی انجام شد',
        -91 => 'عملیات ناموفق در بازگشت مبلغ',
        -92 => 'در عملیات بازگشت مبلغ خطا رخ داده است',
        -93 => 'موجودی صندوق کاربری برای بازگشت مبلغ کافی نیست',
        -94 => 'کلید بازگشت مبلغ یافت نشد',
    ];
}

?>