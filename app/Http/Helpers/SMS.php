<?php


use Illuminate\Support\Facades\DB;

class  SMS
{
    // وضعیت عملیات
//  $res->R_Success;
//// کد خروجی در صورتی که عملیات موفق نباشد
//  $res->R_Code;
//// توضیحی در مورد عملیات
//  $res->R_Message;

    private $url = "http://sms.parsgreen.ir";
    private $NUMBER = '100002000000';


    public function getCredit()
    {
//        $req = new stdClass();

        $res = $this->Exec("User/Credit", []);

        if (!empty($res->R_Success)) {
            return number_format($res->Amount);
        } else
            return !isset($res->R_Message) ? 'دریافت اعتبار پیامک ناموفق بود!  ' : $res->R_Message;
    }

    /**
     * @param $phone
     * @return int
     */
    public function deleteActivationSMS($phone)
    {

        return DB::table('sms_verify')->where(['phone' => $phone,])->delete();

    }

    /**
     * @param $phone
     * @param $code
     * @return bool
     */
    public function verifyActivationSMS($phone, $code)
    {
        return DB::table('sms_verify')->where(['phone' => $phone, 'code' => f2e($code)])->exists();
    }

    /**
     * @param $phone
     * @return mixed|string
     */
    public function sendActivationSMS($phone)
    {

        $req = new stdClass();
        $req->Mobile = $phone;
        $req->SmsCode = $this->generateCode();     // otp code
//       1 : فاقد پیشوند
//2 : کد فعال سازی
//    : 3 Activation Code
//    : 4 کد تایید
//    : 5 Verify Code
//6 : کد
// 7: Code
        $req->TemplateId = 3; //0-6
        $req->AddName = true;
        // append company name end of sms

        $res = $this->Exec("Message/SendOtp", $req);

        if (!empty($res->R_Success) && $res->R_Success) {

            DB::table('sms_verify')->insert(
                ['code' => $req->SmsCode, 'phone' => $req->Mobile]
            );
            return response()->json(['status' => 'success', 'msg' => "کد تایید به شماره همراه ارسال شد"]);
        } else
            return response()->json(['status' => 'danger', 'msg' => !isset($res->R_Message) ? 'ناموفق! لطفا اتصال به ایترنت را بررسی نمایید و مجدد تلاش کنید' : $res->R_Message]);
        //R_Code,R_Error,R_Message,R_Success


    }

    /**
     * @param $phone
     * @return mixed|string
     */
    public function sendSMS($phones, $msg)
    {

        if (count($phones) == 0) return;
        echo implode(',', $phones);
        $req = new stdClass();
        $req->SmsBody = $msg;
        $req->Mobiles = $phones;
        $req->SmsNumber = $this->NUMBER;
        $res = $this->Exec("Message/SendSms", $req);

        if (!empty($res->R_Success) && $res->R_Success) {

            Telegram::sendMessage(Helper::$TELEGRAM_GROUP_ID, 'ارسال موفق به:' . PHP_EOL . implode(',', $phones));
            return response()->json(['status' => 'success', 'msg' => "پیام به شماره(های) همراه ارسال شد"]);
        } else {
            Telegram::sendMessage(Helper::$TELEGRAM_GROUP_ID, $res->R_Message . PHP_EOL . implode(',', $phones) . PHP_EOL . $msg);
            return response()->json(['status' => 'danger', 'msg' => !isset($res->R_Message) ? 'ناموفق! لطفا اتصال به ایترنت را بررسی نمایید و مجدد تلاش کنید' : $res->R_Message]);
            //R_Code,R_Error,R_Message,R_Success
        }

    }

    protected function Exec($urlpath, $req)
    {

        try {
            $this->url = $this->url . '/Apiv2/' . $urlpath;
            $ch = curl_init($this->url);
            $jsonDataEncoded = json_encode($req, JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $header = array('authorization: BASIC APIKEY:' . env('SMS_API'), 'Content-Type: application/json;charset=utf-8');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($ch);

            $res = json_decode($result);
            curl_close($ch);
            return $res;
        } catch (Exception $ex) {
            myLog('sms Exception');
            myLog($ex);
            return $ex->getMessage();
        }
    }

    public function generateCode($codeLength = 5)
    {
        $max = pow(10, $codeLength);
        $min = $max / 10 - 1;
        $code = mt_rand($min, $max);
        return $code;
    }

}
