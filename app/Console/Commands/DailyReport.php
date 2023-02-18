<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PHPUnit\TextUI\Help;

class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Report Status';

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
        $ptxt .= " 🌍 مانده اعتبار پیامک: " . (new \SMS())->getCredit() . " ریال " . PHP_EOL;
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= "بازیکنان: " . Player::count() . PHP_EOL;
        $ptxt .= "مربیان: " . Coach::count() . PHP_EOL;
        $ptxt .= "مراکز ورزشی: " . Club::count() . PHP_EOL;
        $ptxt .= "فروشگاهها: " . Shop::count() . PHP_EOL;
        $ptxt .= " محصولات:" . Product::count() . PHP_EOL;
        $ptxt .= "اخبار: " . Blog::count() . PHP_EOL;

        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= " 💻 فعالیت ادمین ها:" . PHP_EOL;

        foreach (User::whereIn('role', ['ad', 'go'])->select('id', 'username') as $admin) {

            $ptxt .= "🔴" . " " . $admin->username . PHP_EOL;
            $ptxt .= "اخبار: " . Blog::where('user_id', $admin->id)->count() . PHP_EOL;
            $ptxt .= "➖➖➖➖➖➖" . PHP_EOL;
        }
        $ptxt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $ptxt .= "🅳🅰🅱🅴🅻 🆃🅴🅰🅼";

        \Telegram::sendMessage(\Helper::$TELEGRAM_GROUP_ID, $ptxt, null);

    }
}
