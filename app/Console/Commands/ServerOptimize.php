<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Doc;
use App\Models\Payment;
use App\Models\Player;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PHPUnit\TextUI\Help;
use SMS;

class ServerOptimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete empty payments and expired models in last 24 hours. send alert for 48 hours expiration ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Jalalian::forge('now', new DateTimeZone('Asia/Tehran'));
        $time = $now->format('%A, %d %B %Y ⏰ H:i');
        $ptxt = $time . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= " 🌍 بهینه سازی سرور: " . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

        $now = Carbon::now()->subDay();
        DB::table('sms_verify')->where('created_at', '<', $now)->delete();
        $c = Payment::where('user_id', null)->where('created_at', '<', $now)->count();
        Payment::where('user_id', null)->where('created_at', '<', $now)->delete();
        $ptxt .= " 📊 پاکسازی پرداخت های انجام نشده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Player::where('expires_at', '<', $now)->orWhere(function ($query) use ($now) {
            $query->where('created_at', '<', $now)->whereNull('expires_at');
        })->get() as $data) {
            $c++;
            foreach ($data->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $data->delete();
        }
        $ptxt .= " ✅ پاکسازی بازیکنان منقضی شده: " . PHP_EOL;
        $ptxt .= " 🗑 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Coach::where('expires_at', '<', $now)->orWhere(function ($query) use ($now) {
            $query->where('created_at', '<', $now)->whereNull('expires_at');
        })->get() as $data) {
            $c++;
            foreach ($data->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $data->delete();
        }
        $ptxt .= " ✅ پاکسازی مربیان منقضی شده: " . PHP_EOL;
        $ptxt .= " 🗑 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Club::where('expires_at', '<', $now)->orWhere(function ($query) use ($now) {
            $query->where('created_at', '<', $now)->whereNull('expires_at');
        })->get() as $data) {
            $c++;
            foreach ($data->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $data->delete();
        }
        $ptxt .= " ✅ پاکسازی باشگاههای منقضی شده: " . PHP_EOL;
        $ptxt .= " 🗑 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Shop::where('expires_at', '<', $now)->orWhere(function ($query) use ($now) {
            $query->where('created_at', '<', $now)->whereNull('expires_at');
        })->get() as $data) {
            $c++;
            foreach (Product::where('shop_id', $data->id)->get() as $d) {
                foreach ($d->docs as $doc) {
                    Doc::deleteFile($doc);
                }
                $d->delete();
            }
            foreach ($data->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $data->delete();
        }
        $ptxt .= " ✅ پاکسازی فروشگاههای منقضی شده: " . PHP_EOL;
        $ptxt .= " 🗑 تعداد: " . $c . PHP_EOL;


        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;


        //send alarm to 48 hours remained
        $day2 = Carbon::now()->addDays(3);
        $day1 = Carbon::now()->addDays(2);
        $c = 0;
        $phones = [];
        $tmp = PHP_EOL . 'اشتراک شما طی 48 ساعت آینده منقضی و اطلاعات شما از سامانه حذف خواهد شد. لطفا نسبت به تمدید اقدام نمایید.' . PHP_EOL . 'با تشکر:' . PHP_EOL . '2sport';
        foreach (Player::where('expires_at', '<', $day2)->where('expires_at', '>', $day1)->get() as $data) {
            $c++;
            $phones[] = $data->phone;
        }

        (new SMS())->sendSMS($phones, 'بازیکن عزیز دبل اسپورت: ' . $tmp);
        $ptxt .= " 📊 ارسال پیامک حذف بازیکن در 48 ساعت آینده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;
        $c = 0;
        $phones = [];
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

        foreach (Coach::where('expires_at', '<', $day2)->where('expires_at', '>', $day1)->get() as $data) {
            $c++;
            $phones[] = $data->phone;
        }
        (new SMS())->sendSMS($phones, 'مربی عزیز دبل اسپورت: ' . $tmp);
        $ptxt .= " 📊 ارسال پیامک حذف مربی در 48 ساعت آینده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;
        $c = 0;
        $phones = [];
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

        foreach (Club::where('expires_at', '<', $day2)->where('expires_at', '>', $day1)->get() as $data) {
            $c++;
            $phones[] = $data->phone;
        }
        (new SMS())->sendSMS($phones, 'باشگاه دار عزیز دبل اسپورت: ' . $tmp);
        $ptxt .= " 📊 ارسال پیامک حذف باشگاه در 48 ساعت آینده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;
        $c = 0;
        $phones = [];
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        foreach (Shop::where('expires_at', '<', $day2)->where('expires_at', '>', $day1)->get() as $data) {
            $c++;
            $phones[] = $data->phone;
        }
        (new SMS())->sendSMS($phones, 'فروشگاه دار عزیز دبل اسپورت: ' . $tmp);
        $ptxt .= " 📊 ارسال پیامک حذف فروشگاه در 48 ساعت آینده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;


        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= "🅳🅰🅱🅴🅻 🆃🅴🅰🅼";

        echo $ptxt;
        \Telegram::sendMessage(\Helper::$TELEGRAM_GROUP_ID, $ptxt, null);
    }
}
