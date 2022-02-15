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
    protected $description = 'delete empty payments and expired models in last 24 hours. ';

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

        $now = Carbon::now()->subDay();
        $c = Payment::where('user_id', null)->where('created_at', '<', $now)->count();
        Payment::where('user_id', null)->where('created_at', '<', $now)->delete();
        $ptxt .= " 📊 پاکسازی پرداخت های انجام نشده: " . PHP_EOL;
        $ptxt .= " 🌍 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Player::where(function ($query) use ($now) {
            $query->where('expires_at', '<', $now)->orWhereNull('expires_at');
        })->get() as $data) {
            $c++;
            foreach ($data->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $data->delete();
        }
        $ptxt .= " ✅ پاکسازی کاربران منقضی شده: " . PHP_EOL;
        $ptxt .= " 🗑 تعداد: " . $c . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $c = 0;
        foreach (Coach::where(function ($query) use ($now) {
            $query->where('expires_at', '<', $now)->orWhereNull('expires_at');
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
        foreach (Club::where(function ($query) use ($now) {
            $query->where('expires_at', '<', $now)->orWhereNull('expires_at');
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
        foreach (Shop::where(function ($query) use ($now) {
            $query->where('expires_at', '<', $now)->orWhereNull('expires_at');
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
        $ptxt .= "🅳🅰🅱🅴🅻 🆃🅴🅰🅼";

        echo $ptxt;
        \Telegram::sendMessage(\Helper::$TELEGRAM_GROUP_ID, $ptxt, null);

    }
}
