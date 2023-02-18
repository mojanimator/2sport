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
    public static function makePay($req)
    {


        $params = [
            'api_key' => env('NEXTPAY_API'),
            'order_id' => $req->order_id,
            'amount' => $req->amount,
            'callback_uri' => url('payment'),
            'currency' => 'IRT',
            'customer_phone' => $req->customer_phone,
            'payer_name' => $req->payer_name,
            'auto_verify' => false,
            'custom_json_fields' => json_encode(['user_id' => $req->user_id, 'data_id' => $req->data_id, 'pay_for' => $req->pay_for, 'coupon_id' => $req->coupon_id]),
        ];

        $response = mRequest(self::TOKEN_LINK, 'POST', $params);

        if ($response && $response->code == -1) { //send user to bank page
            \App\Models\Payment::create([
                'agency_id' => $req->user_agency_id,
                'province_id' => $req->data_province_id,
                'token_id' => $response->trans_id,
                'order_id' => $req->order_id,
                'pay_for' => $req->pay_for,
                'pay_for_id' => $req->data_id,
                'user_id' => $req->user_id,
            ]);
            (new SMS())->deleteActivationSMS($req->phone);
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
        if (!$response) return null;
        if ($response && $response->code == 0) { //verify success
            $payment = \App\Models\Payment::where('order_id', $response->order_id)->first();
            if (!$payment)
                return null;
            else {

                $payment->amount = $response->amount;
                $payment->Shaparak_Ref_Id = $response->Shaparak_Ref_Id;
                $payment->card_holder = $response->card_holder;
                $payment->save();
                $payment->custom = $response->custom;
                $payment->code = $response->code;
                return $payment;
            }
        }
        return null;
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