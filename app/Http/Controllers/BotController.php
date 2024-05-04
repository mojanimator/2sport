<?php

namespace App\Http\Controllers;


use App\Models\User;
use Carbon\Carbon;
use DateTime;

use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Stmt\Else_;


class BotController extends Controller
{
    protected $Dev, $logs, $channel, $info, $user, $bot, $init_score, $ref_score, $install_chat_score,
        $follow_score, $add_score, $left_score, $divar_show_items, $divar_scores, $bot_id, $tut_link, $app_link;
    //user selected  game type and click on find gamer
    //try to find gamer else connect to a bot

    public function __construct()
    {
        error_reporting(1);
        set_time_limit(0);
        header("HTTP/1.0 200 OK");
        date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//

        $this->channel = Helper::$TELEGRAM_CHANNEL_ID;
        $this->Dev = Helper::$logs;
        $this->logs = Helper::$logs;

//-----------------------------//
        define('API_KEY', env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN')); // توکن ربات
    }


    public function getupdates(Request $request)
    {
        $update = json_decode(file_get_contents('php://input'));
        if (isset($update->message)) {
            $message = $update->message;
            $chat_id = $message->chat->id;
            $chat_username = '@' . $message->chat->username;
            $text = $message->text;
            $message_id = $message->message_id;
            $from_id = $message->from->id;
            $tc = $message->chat->type;
            $title = isset($message->chat->title) ? $message->chat->title : "";
            $first_name = isset($message->from->first_name) ? $message->from->first_name : "";
            $last_name = isset($message->from->last_name) ? $message->from->last_name : "";
            $username = isset($message->from->username) ? '@' . $message->from->username : "";
//            $reply = isset($message->reply_to_message->forward_from->id) ? $message->reply_to_message->forward_from->id : "";
//            $reply_id = isset($message->reply_to_message->from->id) ? $message->reply_to_message->from->id : "";
            $reply = isset($message->reply_to_message) ? $message->reply_to_message : "";

            $new_chat_member = $update->message->new_chat_member; #id,is_bot,first_name,last_name,username
            $new_chat_members = $update->message->new_chat_members; # id,is_bot,first_name,last_name,username]
            $left_chat_member = $update->message->left_chat_member; #id,is_bot,first_name,username
            $new_chat_participant = $update->message->new_chat_participant; #id,username

//            $animation = $update->message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
//            $sticker = $update->message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
//            $photo = $update->message->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
//            $document = $update->message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
//            $video = $update->message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
//            $audio = $update->message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
//            $voice = $update->message->voice; #duration,mime_type,file_id,file_unique_id,file_size
//            $video_note = $update->message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
            $caption = $message->caption;
            $phone_number = isset($message->contact) ? $message->contact->phone_number : null;
        }
        if (isset($update->callback_query)) {
            $Data = $update->callback_query->data;
            $data_id = $update->callback_query->id;
            $chat_id = $update->callback_query->message->chat->id;
            $from_id = $update->callback_query->from->id;
            $first_name = $update->callback_query->from->first_name;
            $last_name = $update->callback_query->from->last_name;
            $username = '@' . $update->callback_query->from->username;
            $tc = $update->callback_query->message->chat->type;
            $message_id = $update->callback_query->message->message_id;

        }
        if (isset($update->channel_post)) {
            $tc = $update->channel_post->chat->type;
            $text = $update->channel_post->text;
            $chat_id = $update->channel_post->chat->id;
            $chat_username = '@' . $update->channel_post->chat->username;
            $chat_title = $update->channel_post->chat->title;

            $message_id = $update->channel_post->message_id;
//            $from_id = json_encode($update);
            $from_id = $this->Dev;
        }
//        return json_encode($from_id);
        // if ($new_chat_members || $new_chat_member || $left_chat_member){
        //     Storage::prepend('file.log', json_encode($update->message));

        //   $this->sendMessage("871016407", "$message", "Markdown", null, null);

        // }


//------------------------------------------------------------------------------
        if (!in_array($from_id, $this->Dev))
            $rank = $this->user_in_chat($this->channel, $from_id, $tc);// $get['result']['status'];

//        $this->bot_id = $this->creator('GetMe', [])->result->id;
//        $INSTALL_ICON = '🥒';
//        $ABOUT_ICON = '🤖';
//        $USER_EDIT_ICON = "✏";
//        $USER_REGISTER_ICON = "✅";
//        $CANCEL_REGISTER_ICON = "❌";
//
//        $INSTALL_BOT = " نصب ربات";
//        $ABOUT_BOT = " درباره ربات";
//        $USER_EDIT = "ویرایش اطلاعات";
//        $USER_REGISTER = " ثبت نام ";
//        $CANCEL_REGISTER = "لغو ثبت نام";


        if ($reply) {
//                sendTelegramMessage($from_id, json_encode($reply), null, null);

            $repText = $reply->text;
            if ($repText) {

                if (str_starts_with($repText, 'ip:')) {
                    $tmp = explode("\n", $repText);
                    if (count($tmp) > 1) {
                        $pusherChannel = $tmp[0];
                        $txt = $tmp[1];
                        if ($pusherChannel && str_contains($pusherChannel, 'ip:')) {
                            $ip = str_replace('ip:', '', $pusherChannel);
                            $t = Carbon::now()->timestamp;
                            sendToDabelAdl([
                                'cmnd' => 'chat',
                                'operator' => 'support' . $chat_id,
                                'ip' => $ip,
                                'text' => $text,
                                'time' => $t
                            ]);
//                            event(new ChatEvent('support' . $chat_id, $ip, $text, $ip, $t));

                        }
                    }

                }
            }
        } elseif ($tc == 'private') {
            $this->sendMessage(72534783, file_get_contents('php://input'));


            $this->sendMessage($from_id, 'در حال بروز رسانی هستیم...');

            return;
            $this->user = User::where('telegram_id', $from_id)->first();
//            return (string)($USER_REGISTER . "\xE2\x9C\x85" == $text);
//            return (string)(0 == null);
//            return $this->user_in_channel("@lamassaba", $from_id);// == 'administrator' or 'creator'
//            return $this->user_in_channel("@twitterfarsi", $from_id);// Bad Request: user not found
//            return $this->user_in_channel("@twitteddrfarsi", $from_id);// Bad Request: chat not found

//            return json_encode($this->inviteToChat($this->channel));
            $team_button = json_encode(['inline_keyboard' => [

                [['text' => "استقلال", 'callback_data' => "team$1"]],
                [['text' => "پرسپولیس", 'callback_data' => "team$2"]],
            ], 'resize_keyboard' => true]);

            $buy_button = json_encode(['inline_keyboard' => [
                [['text' => "📪 ارتباط با ما 📪", 'url' => "telegram.me/" . 'develowper']],
                [['text' => "📌 دریافت بنر دعوت 📌", 'callback_data' => "بنر"]],
            ], 'resize_keyboard' => true]);

            $buy_cancel_button = json_encode(['keyboard' => [[['text' => "لغو خرید ❌"]]], 'resize_keyboard' => true]);

            $button = json_encode(['keyboard' => [
                in_array($from_id, $this->Dev) ? [['text' => 'پنل مدیران🚧']] : [],

                [['text' => "📱 دریافت اپلیکیشن 📱"]],
                [['text' => "⭐ جوایز ⭐"]],
                [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                [['text' => "📌 دریافت بنر دعوت 📌"]],
                [['text' => 'امتیاز من💰']],
                [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                [['text' => "📱 خرید شارژ 📱"], ['text' => "📱 خرید اینترنت 📱"]],
                [['text' => 'درباره ربات🤖'], ['text' => "🙏 حمایت از ما 🙏"]],

            ], 'resize_keyboard' => true]);
            $cancel_button = json_encode(['keyboard' => [
                [['text' => "لغو ثبت نام❌"]],
            ], 'resize_keyboard' => true]);
            $return_button = json_encode(['inline_keyboard' => [
                [['text' => "بازگشت⬅", 'callback_data' => "edit_cancel"]],
            ], 'resize_keyboard' => true]);
            $edit_button = json_encode(['inline_keyboard' => [
                [['text' => 'ویرایش نام کاربری', 'callback_data' => "edit_username"]],
//                [['text' => 'ویرایش ایمیل', 'callback_data' => "edit_email"]],
                [['text' => 'ویرایش گذرواژه', 'callback_data' => "edit_password"]],
                [['text' => 'ویرایش تصویر پروفایل', 'callback_data' => "edit_image"]],
            ], 'resize_keyboard' => true]);
            $admin_button = json_encode(['inline_keyboard' => [
                [['text' => "📬 ارسال همگانی به همه کاربران", 'callback_data' => 'send_to_users$0']],
                [['text' => "📬 ارسال همگانی به کاربران استقلال", 'callback_data' => 'send_to_users$1']],
                [['text' => "📬 ارسال همگانی به کاربران پرسپولیس", 'callback_data' => 'send_to_users$2']],
                [['text' => "📬 ارسال همگانی به کاربران بارسا", 'callback_data' => 'send_to_users$3']],
                [['text' => "📬 ارسال همگانی به کاربران رئال", 'callback_data' => 'send_to_users$4']],
                [['text' => "📬 ارسال همگانی به گروه ها", 'callback_data' => 'send_to_chats']],
                [['text' => "🚶 مشاهده کاربران", 'callback_data' => 'see_users']],
                [['text' => "🚶 مشاهده فالورها", 'callback_data' => 'see_followers']],
                [['text' => "👑 ثبت نتیجه پیش بینی", 'callback_data' => 'predict_response']],
                [['text' => "🤖بروزرسانی سرور", 'callback_data' => 'update_server_status']],
                [['text' => "❓ راهنمای دستورات", 'callback_data' => 'admin_help']],
                [['text' => "📊 آمار", 'callback_data' => 'statistics']],
            ], 'resize_keyboard' => true]);
            $send_cancel_button = json_encode(['inline_keyboard' => [
                [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
            ], 'resize_keyboard' => true]);

//            if (!in_array($from_id, $this->Dev) && strpos($text, "/start ") === false && $text != "/start$this->bot" && (!$this->user || $this->user->step != 80) && $rank != 'creator' && $rank != 'administrator' && $rank != 'member') {
//                $this->sendMessage($chat_id, "🙏تبلیغات و تبادل در اپلیکیشن ها پذیرفته می شود" . PHP_EOL . "@develowper", null, $message_id, null, true);
//
//                $this->sendMessage($chat_id, "■ برای استفاده از ربات و اطلاع از جوایز و بروز رسانی ها ابتدا وارد کانال\n● $this->channel  \n■ شده سپس به ربات برگشته و /start را بزنید \n❌در صورت لفت دادن اکانت شما پاک خواهد شد❌\n.", null, $message_id, json_encode(['KeyboardRemove' => [], 'remove_keyboard' => true]));
//
//            } else
            if (preg_match('/^\/(start)$/i', $text)) {

                if (!$this->user) $this->sendMessage($chat_id, "■ سلام $first_name به دبل اسپورت خوش آمدید\n\n" . "📌 به زودی با امکانات جذاب در خدمت شما خواهیم بود...", null, $message_id, $button);
                else $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋\n\n■ " . "📌 به زودی با امکانات جذاب در خدمت شما خواهیم بود...", null, $message_id, $button);
//                $first_name = $this->MarkDown($first_name);

                foreach ($this->logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات دبل اسپورت را استارت زد.", 'MarkDown');

            }
            return;
            if (preg_match('/^\/(start)$/i', $text)) {

                if (!$this->user) $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید\n\n" . "📌 با این ربات توی تیم محبوبت ثبت نام کن و اپلیکیشن تیمت رو دانلود کن و با کمک هم تیمی ها برنده باش \n 🔵استقلال 👑 پرسپولیس🔴" . "\n\n📌 برای استفاده از تمامی امکانات ربات و اپلیکیشن ابتدا ثبت نام کن :", null, $message_id, $button);
                else $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋\n\n■ چه کاری براتون انجام بدم؟ ", null, $message_id, $button);
//                $first_name = $this->MarkDown($first_name);
                $this->sendMessage($chat_id, "🙏تبلیغات و تبادل در اپلیکیشن ها پذیرفته می شود" . PHP_EOL . "@develowper", null, $message_id, null, true);
                $this->sendMessage($chat_id, " با زدن دکمه دریافت بنر دعوت و ارسال برای دوستات، امتیاز بگیر🙏" . PHP_EOL . "@vartastudiobot", null, $message_id, null, true);

                foreach ($this->logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات ورتا را استارت زد.", 'MarkDown');

            } elseif ($reply) {
                $this->sendMessage($from_id, json_encode($message), null, null);
            }
//            elseif ($text == 'عیدی') {
//                foreach (Helper::$logs as $log)
//                    Helper::sendMessage($log, "[$first_name](tg://user?id=$from_id)" . " عیدی رو زد ", 'MarkDown', null);
//
//                if (!$this->user) {
//                    $m = 'اسمت توی کاربرا نیست!😩 اگه از اپلیکیشن ثبت نام کردی داخل اپلیکیشن دکمه تنظیمات و سپس اتصال به تلگرام رو بزن.' . PHP_EOL;
//                    $m .= " 🙏 اگه سوالی داشتی ازم بپرس " . '@develowper';
//                    Helper::sendMessage($from_id, $m, 'MarkDown', null);
//                    return;
//                }
//                $p = DB::table('prizes')->where('telegram_id', $from_id)->first();
//                if (!$p || $p->created_at < Carbon::now()) {
//                    Helper::creator('sendSticker', [
//                        'chat_id' => $chat_id,
//                        'sticker' => 'CAACAgIAAxkBAAEBT9FgVjJByQRJoCruyQyFeGnrj3A73wACvgkAAoSumEoSQwToTbAzsh4E',
//
//                    ]);
//                    sleep(4);
//                    $arr = [5, 5, 5, 5, 5, 5, 10, 10, 10, 10, 20, 10, 20, 30, 30, 30, 30, 30, 40, 40, 50, 50];
//                    $idx = array_rand($arr);
//                    $res = $arr[$idx];
//                    $this->DeleteMessage($chat_id, $message_id + 1);
//                    Helper::sendMessage($from_id, "✨✨ تبریک ✨✨" . PHP_EOL . "تعداد $res ستاره برنده شدی😍. \n میتونی باهاش عکسهای داخل برنامه رو بخری!!.فردا همین موقع دوباره منتظرتم 😍", 'MarkDown', null);
//                    $this->user->increment('score', $res);
//                    if (!$p)
//                        DB::table('prizes')->insert(['telegram_id' => $from_id, 'created_at' => Carbon::now()->addDay()]);
//                    else
//                        DB::table('prizes')->where('telegram_id', $from_id)->update(['created_at' => Carbon::now()->addDay()]);
//
//
//                } else {
//                    $h = Carbon::now()->diffInHours($p->created_at);
//                    Helper::sendMessage($from_id, "عیدی بعدی $h ساعت دیگه فعال میشه 😜", 'MarkDown', null);
//
//                }
////
//            }
// elseif ($reply) {
//                $this->sendMessage($chat_id, "$update", null, null, null);
//            }
            elseif ($text == "🙏 حمایت از ما 🙏") {
                foreach (Helper::$logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) دکمه حمایت را زد.", 'MarkDown', null, null);

//                $buy_button = json_encode(['inline_keyboard' => [
//                    [['text' => "⬅ کلیک کنید ", 'url' => Helper::$donateLink]],
//                ], 'resize_keyboard' => true]);
                Product::create(['telegram_id' => "$chat_id", 'type' => 'donate', 'step' => 5, 'user_id' => $this->user ? $this->user->id : null]);

                $this->sendMessage($chat_id, "🙏 لطفا مبلغ حمایت را به ریال وارد نمایید 🙏", 'MarkDown', null, $buy_cancel_button);

            } elseif ($text == "📱 خرید شارژ 📱" || $text == "📱 خرید اینترنت 📱") {
                foreach (Helper::$logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) دکمه $text را زد.", 'MarkDown', null, null);

                $type = $text == "📱 خرید شارژ 📱" ? 'charge' : 'internet';
                $step = $type == 'internet' ? 2 : 0;
                $phones_btn = [];
                if ($this->user) {
                    foreach (explode('$', $this->user->phones) as $p)
                        array_push($phones_btn, [['text' => $p, 'callback_data' => 'phone$' . $p]]);
                }
                $or = '';
                if (count($phones_btn) > 0) $or = 'انتخاب یا';
                $phones_btn = json_encode(['inline_keyboard' => $phones_btn, 'resize_keyboard' => true]);
                Product::where('telegram_id', "$chat_id")->where("info", null)->delete();
                Product::create(['telegram_id' => "$chat_id", 'type' => $type, 'step' => $step, 'user_id' => $this->user ? $this->user->id : null]);
                $this->sendMessage($chat_id, "شماره تلفن خود را $or وارد کنید", 'MarkDown', null, $phones_btn);
                $this->sendMessage($chat_id, "برای انصراف دکمه لغو خرید ❌ رابزنید", 'MarkDown', null, $buy_cancel_button);

            } elseif ($text == 'منوی اصلی⬅') {
                $this->sendMessage($chat_id, "منوی اصلی", 'MarkDown', $message_id, $button);

            } elseif ($text == 'امتیاز من💰') {
                $score = $this->user->score ? $this->user->score : 0;

                $this->sendMessage($from_id, "💰 امتیاز فعلی شما:$score \n  برای دریافت امتیاز می توانید بنر تبلیغاتی مخصوص خود را تولید کرده و یا در اپلیکیشن ویدیو تماشا کنید و یا از طریق دکمه ارتباط با ما اقدام به خرید امتیاز نمایید ", 'Markdown', $message_id, $buy_button);


            } elseif ($text == "منوی اصلی💬") {

                $this->sendMessage($chat_id, "منوی اصلی", null, $message_id, $button);

            } elseif ($text == "لغو ❌") {
                if ($this->user) {
                    $this->user->step = null; // for register channel
                    $this->user->save();
                }
                $this->sendMessage($chat_id, "با موفقیت لغو شد!", null, $message_id, $button);

            } elseif ($text == 'درباره ربات🤖') {
                $this->sendMessage($chat_id, "✅توسط این ربات می توانید در *اپلیکیشن های استودیو ورتا* ثبت نام کرده و از *امکانات* و *سرگرمی های*  این اپلیکیشن ها  استفاده کنید✅", 'MarkDown', $message_id);
                $this->sendMessage($chat_id, " \n لینک دریافت اپلیکیشن:\n  $this->app_link \n", 'MarkDown', $message_id);
                $this->sendMessage($chat_id, "$this->info\n", 'Markdown', $message_id, $button);
            } elseif ($text == "لغو ثبت نام❌") {
                $button = json_encode(['keyboard' => [
                    [['text' => "ثبت نام✅"]],
                    [['text' => 'درباره ربات🤖']],
                ], 'resize_keyboard' => true]);# user is registering


                if ($this->user) {
                    $this->user->step = null; // for register channel

                    if (strpos($Data, "team$") !== false || in_array($this->user->step, [0, 1, 5])) {
                        $this->user->delete();
                    }

                }

                $this->sendMessage($chat_id, "ثبت نام شما لغو شد", 'MarkDown', $message_id, $button);

            } elseif ($text == "لغو خرید ❌") {
                Product::where('telegram_id', "$chat_id")->where("info", null)->delete();
                $this->sendMessage($chat_id, "خرید شما لغو شد", 'MarkDown', $message_id, $button);

            } elseif ($text == "ویرایش اطلاعات✏") {

                if (!$this->user) $this->sendMessage($chat_id, "شما  ثبت نام نکرده اید", 'MarkDown', $message_id, $button);
                else {
                    $this->sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, $message_id, $edit_button);
//                    $this->user->step = 0;
//                    $this->user->save();
//                    $this->sendMessage($chat_id, "نام کاربری را وارد کنید", 'MarkDown', $message_id, $button);
                }
                //buy charge
            } elseif ($p = Product::where('telegram_id', "$chat_id")->where("info", null)->first()) {

                switch ($p->step) {

                    case 0:
                        if (strpos($Data, 'phone$') !== false)
                            $phone = explode('$', $Data)[1];
                        else
                            $phone = $text;
                        if (!$this->check('phone', $phone, $chat_id, $message_id, $buy_cancel_button)) return;

                        $this->DeleteMessage($chat_id, $message_id);
                        if (starts_with($phone, '090') || starts_with($phone, '093')) $operator = 'MTN';
                        elseif (starts_with($phone, '0990') || starts_with($phone, '091')) $operator = 'MCI';
                        elseif (starts_with($phone, '0922') || starts_with($phone, '0921')) $operator = 'RTL';
                        elseif (starts_with($phone, '094')) $operator = 'WiMax';
                        else   $operator = '';

                        $or = $operator == 'MTN' ? 'یا مبلغی بین 500 تا 200,000 تومان وارد نمایید' : 'نمایید';
                        $chargeButton = json_encode(['inline_keyboard' => [
                            $operator == 'MTN' ?
                                [['text' => '500 ت⚡', 'callback_data' => 'charge$500']] : [],
                            [['text' => '1000 ت⚡', 'callback_data' => 'charge$1000']],
                            [['text' => '2000 ت⚡', 'callback_data' => 'charge$2000']],
                            [['text' => '5000 ت⚡', 'callback_data' => 'charge$5000']],
                            [['text' => '10,000 ت⚡', 'callback_data' => 'charge$10000']],
                            [['text' => '20,000 ت⚡', 'callback_data' => 'charge$20000']],
                        ], 'resize_keyboard' => true]);
                        $this->sendMessage("$chat_id", "⭐مقدار شارژ را انتخاب $or" . "\n(با احتساب 9 درصد مالیات)", null, null, $chargeButton);
                        $p->phone = $phone;
                        $p->step = 1;
                        $p->save();
                        break;
                    case 1:
                        $this->DeleteMessage($chat_id, $message_id);

                        if (strpos($Data, 'charge$') !== false)
                            $charge = explode('$', $Data)[1];
                        else
                            $charge = $text;
                        $phone = $p->phone;
                        if (starts_with($phone, '090') || starts_with($phone, '093')) $operator = 'MTN';
                        elseif (starts_with($phone, '0990') || starts_with($phone, '091')) $operator = 'MCI';
                        elseif (starts_with($phone, '0922') || starts_with($phone, '0921')) $operator = 'RTL';
                        elseif (starts_with($phone, '094')) $operator = 'WiMax';
                        else   $operator = '';
                        $http = new  \GuzzleHttp\Client();

                        $response = $http->post(
                            Helper::$directChargeLink,
                            array(
                                'form_params' => array(
                                    'webserviceId' => env('CHARGE_RESELLER'),
                                    'scriptVersion' => 'Script',
                                    'redirectToPage' => 'True',
                                    'redirectUrl' => 'https://qr-image-creator.com/wallpapers/api/charge',
                                    'firstOutputType' => 'json',
                                    'secondOutputType' => 'get',
                                    'type' => $operator,
                                    'amount' => $charge,
                                    'cellphone' => $phone,
                                )
                            ));
                        $response = json_decode($response->getBody());

//                        $this->sendMessage("$chat_id", $response, null, null, null);
                        if ($response->status != 'Success') {

                            Product::where('telegram_id', "$chat_id")->where("info", null)->delete();
                            $this->sendMessage("$chat_id", $response->errorMessage, null, null, $button);
                        } else {
                            $url = $response->paymentInfo->url;

                            $gateButton = json_encode(['inline_keyboard' => [

                                [['text' => '✅ ورود به درگاه پرداخت', 'url' => "$url"]],

                            ], 'resize_keyboard' => true]);
                            $this->sendMessage("$chat_id", "⭐جهت خرید بر روی لینک زیر کلیک کنید\n🚧قبل از پرداخت, از صحت مبلغ مطمئن شوید", null, null, $gateButton);
                            $p->step = 100;
                            $p->save();
                        }
                        break;
                    case 2:
                        $this->DeleteMessage($chat_id, $message_id);

                        if (strpos($Data, 'phone$') !== false)
                            $phone = explode('$', $Data)[1];
                        else
                            $phone = $text;
                        if (!$this->check('phone', $phone, $chat_id, $message_id, $buy_cancel_button)) return;


                        if (starts_with($phone, '090') || starts_with($phone, '093')) $operator = 'mtn';
                        elseif (starts_with($phone, '0990') || starts_with($phone, '091')) $operator = 'mci';
                        elseif (starts_with($phone, '0922') || starts_with($phone, '0921')) $operator = 'rtl';
                        elseif (starts_with($phone, '094')) $operator = 'mtn';
                        else   $operator = '';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, Helper::$chargeProductsLink);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = json_decode(curl_exec($ch));
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        if ($httpcode != 200) {
                            $this->sendMessage("$chat_id", "مشکلی در دریافت اطلاعات پیش آمد", null, null, null);
                            $this->sendMessage(Helper::$logs[0], "مشکلی در دریافت محصولات شارژ ریسلر پیش آمد", null, null, null);
                            return;
                        }

                        $products = $response->products->internetPackage->$operator;

                        $packs = [];
                        foreach ($products as $name => $product)
                            array_push($packs, [['text' => $name, 'callback_data' => 'net$' . $operator . '$' . $name . '$' . $phone]]);

                        $chargeButton = json_encode(['inline_keyboard' => $packs
                            , 'resize_keyboard' => true]);
                        $this->sendMessage("$chat_id", "نوع بسته را انتخاب کنید", null, null, $chargeButton);
                        $p->phone = $phone;
                        $p->step = 3;
                        $p->save();
                        break;
                    case 3:
                        $this->DeleteMessage($chat_id, $message_id);

                        $operator = explode('$', $Data)[1];
                        $name = explode('$', $Data)[2];
                        $phone = explode('$', $Data)[3];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, Helper::$chargeProductsLink);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        $response = json_decode(curl_exec($ch));
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        if ($httpcode != 200) {
                            $this->sendMessage("$chat_id", "مشکلی در دریافت اطلاعات پیش آمد", null, null, null);
                            $this->sendMessage(Helper::$logs[0], "مشکلی در دریافت محصولات شارژ ریسلر پیش آمد", null, null, null);
                            return;
                        }
                        $products = $response->products->internetPackage->$operator->$name;
                        $packs = [];
                        foreach ($products as $product)
                            array_push($packs, [['text' => '⚡' . explode('-', $product->name)[1]
                                . "\n💵" . $product->price . ' ت ', 'callback_data' => 'net_product$' . $product->id . '$' . $phone]]);

                        $chargeButton = json_encode(['inline_keyboard' => $packs
                            , 'resize_keyboard' => true]);
                        $this->sendMessage("$chat_id", "بسته مورد نظر خود را از لیست زیر انتخاب کنید", null, null, $chargeButton);

                        $p->step = 4;
                        $p->save();

                        break;
                    case 4:
                        $this->DeleteMessage($chat_id, $message_id);
                        $packageId = explode('$', $Data)[1];
                        $phone = explode('$', $Data)[2];

                        $http = new  \GuzzleHttp\Client();

                        $response = $http->post(
                            Helper::$directInternetLink,
                            array(
                                'form_params' => array(
                                    'webserviceId' => env('CHARGE_RESELLER'),
                                    'scriptVersion' => 'Script',
                                    'redirectToPage' => 'True',
                                    'redirectUrl' => 'https://qr-image-creator.com/wallpapers/api/charge',
                                    'firstOutputType' => 'json',
                                    'secondOutputType' => 'get',
                                    'packageId' => $packageId,
                                    'cellphone' => $phone,
                                )
                            ));
                        $response = json_decode($response->getBody());

//                        $this->sendMessage("$chat_id", $response, null, null, null);
                        if ($response->status != 'Success') {

                            Product::where('telegram_id', "$chat_id")->where("info", null)->delete();
                            $this->sendMessage("$chat_id", $response->errorMessage, null, null, null);
                        } else {
                            $url = $response->paymentInfo->url;

                            $gateButton = json_encode(['inline_keyboard' => [

                                [['text' => '✅ ورود به درگاه پرداخت', 'url' => "$url"]],

                            ], 'resize_keyboard' => true]);
                            $this->sendMessage("$chat_id", "⭐جهت خرید بر روی لینک زیر کلیک کنید\n🚧قبل از پرداخت, از صحت مبلغ مطمئن شوید", null, null, $gateButton);
                            $p->step = 100;
                            $p->save();
                        }
                        break;
                    case 5:
                        //donate
                        if (!$this->check('donate', $text, $chat_id, $message_id, $buy_cancel_button)) return;

                        $http = new  \GuzzleHttp\Client(['http_errors' => false]);
                        $order_id = time();
                        $response = $http->post(
                            Helper::$idPayDonateServiceLink,
                            array(

                                'headers' => array(
                                    'X-API-KEY' => env('IDPAY_TOKEN'),
                                    'Content-Type' => 'application/json',

                                ),
                                \GuzzleHttp\RequestOptions::JSON => array(
                                    'order_id' => $order_id,
                                    'amount' => $text,
                                    'callback' => "https://qr-image-creator.com/wallpapers/api/donate",
                                    'name' => $username == null || $username == '@' ? "$chat_id" : $username,

                                )
                            ));

                        $data = json_decode($response->getBody());

                        if ($response->getStatusCode() != 201)
                            if (isset($data->error_message)) {
                                Helper::sendMessage(Helper::$logs[0], $data->error_message, null, null, null);
                                Helper::sendMessage($chat_id, $data->error_message, null, null, null);
                            } else {
                                Helper::sendMessage(Helper::$logs[0], json_encode($response->getBody()), null, null, null);
                                Helper::sendMessage($chat_id, json_encode($response->getBody()), null, null, null);

                            } else {
                            $p->pay_id = $data->id;
                            $p->order_id = $order_id;
                            $p->name = $username == null || $username == '@' ? "$chat_id" : $username;
                            $p->amount = $text;
                            $p->save();
                            $gateButton = json_encode(['inline_keyboard' => [

                                [['text' => '✅ ورود به درگاه پرداخت', 'url' => $data->link]],

                            ], 'resize_keyboard' => true]);
                            $this->sendMessage("$chat_id", "⭐جهت پرداخت بر روی لینک زیر کلیک کنید\n🚧قبل از پرداخت, از صحت مبلغ مطمئن شوید", null, null, $gateButton);
                            $p->step = 100;
                            $p->save();
                        }


                        break;
                }

            } elseif
            ($Data == "edit_username") {
                $name = $this->user->username;
                $this->user->step = 6;
                $this->user->save();
                $this->sendMessage($chat_id, "نام  فعلی: $name \n  نام کاربری جدید را وارد کنید:", 'MarkDown', null, $return_button);

            }
//            elseif ($Data == "edit_email") {
//                $name = $this->user->email;
//                $this->user->step = 7;
//                $this->user->save();
//                $this->sendMessage($chat_id, "ایمیل  فعلی: $name \n  ایمیل  جدید را وارد کنید:", 'MarkDown', null, $return_button);
//
//            }

            elseif
            ($Data == "edit_password") {
                $this->user->step = 8;
                $this->user->save();
                $this->sendMessage($chat_id, "    \n  گذرواژه جدید را وارد کنید:", 'MarkDown', null, $return_button);

            } elseif
            ($Data == "edit_image") {
//                $this->user->step = 8;
//                $this->user->save();
                $this->createUserImage($this->user->telegram_id);
                $this->sendMessage($chat_id, "تصویر فعلی تلگرام شما بعنوان عکس پروفایل در اپلیکیشن تنظیم شد", 'MarkDown', null, $return_button);

            } elseif
            ($Data == "edit_cancel") {
                $this->user->step = null;
                $this->user->save();
                $this->sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, null, $edit_button);


            } elseif
            ($Data == "predict_response") {

                $quizzes = [];
                foreach (Quiz::where('is_predict', true)->where('expires_at', '<', Carbon::now())->whereNull('response')->orderBy('app_id')->select('id', 'question')->get() as $item) {
                    array_push($quizzes, [['text' => $item['question'], 'callback_data' => "predict$" . $item['id']]]);

                }
                if (count($quizzes) === 0) {
                    $this->sendMessage($chat_id, "■ در حال حاضر سوال پیش بینی وجود ندارد", null, null, null);
                    return;
                }

                $btn = json_encode(['inline_keyboard' => $quizzes, 'resize_keyboard' => true]);
                $this->sendMessage($chat_id, "■ سوال مورد نظر را انتخاب کنید :", null, null, $btn);


            } elseif
            (strpos($Data, "predict$") !== false) {
                $qId = explode('$', $Data)[1];
                $q = Quiz::where('id', $qId)->first();

                $options = [];
                foreach ($q->options as $item) {
                    array_push($options, [['text' => $item, 'callback_data' => $q->id . '$option$' . $item]]);

                }
                array_push($options, [['text' => 'خالی', 'callback_data' => $q->id . '$option$']]);

                $btn = json_encode(['inline_keyboard' => $options, 'resize_keyboard' => true]);
                $this->sendMessage($chat_id, "■ گزینه جواب را انتخاب کنید :", null, null, $btn);


            } elseif
            (strpos($Data, '$option$') !== false) {
                $qId = explode('$', $Data)[0];
                $qResponse = explode('$', $Data)[2];

                Quiz::where('id', $qId)->update(['response' => $qResponse == '' ? null : $qResponse]);


                $btn = json_encode(['inline_keyboard' => [[['text' => 'سایر سوالات', 'callback_data' => 'predict_response']]], 'resize_keyboard' => true]);
                $this->sendMessage($chat_id, "■ جواب با موفقیت ثبت شد", null, null, $btn);
                $this->DeleteMessage($chat_id + 1, $message_id);
                $this->DeleteMessage($chat_id, $message_id);
                $this->DeleteMessage($chat_id - 1, $message_id);


            } elseif (($text == "پنل مدیران🚧" || $text == "p") && in_array($from_id, $this->Dev)) {
//
                $this->sendMessage($chat_id, "🚧فقط مدیران ربات به این پنل دسترسی دارند. گزینه مورد نظر خود را انتخاب کنید:", "Markdown", null, $admin_button);
            } elseif
            (strpos($Data, "send_to_users$") !== false) {

                $step = (int)explode("$", $Data)[1];

                $this->user->step = 90 + $step;
                $this->user->save();
                $this->sendMessage($chat_id, "■ متن یا فایل ارسالی را وارد کنید :", null, null, $send_cancel_button);

            } elseif
            ($Data == "send_cancel") {
                $this->user->step = null;
                $this->user->save();
                $this->DeleteMessage($chat_id, $message_id);
                $this->DeleteMessage($chat_id, $message_id - 1);
                $this->DeleteMessage($chat_id, $message_id - 2);
                $this->sendMessage($chat_id, "با موفقیت لغو شد ", null, null, null);


            } elseif
            ($Data == "see_users") {
                $txt = "";
                $txt .= "\n-------- لیست کاربران-----\n";
                if (in_array($from_id, $this->Dev))

                    foreach (User::get(['id', 'username', 'telegram_username', 'telegram_id', 'score']) as $idx => $user) {

                        $txt .= "id: $user->id\n";
                        $txt .= "username: $user->username\n";
                        $txt .= "telegram_username: $user->telegram_username\n";
                        $txt .= "telegram_id: $user->telegram_id\n";
                        $txt .= "app: $user->app_id\n";
//                        $txt .= "channels:" . json_encode($user->channels) . "\n";
//                        $txt .= "groups: " . json_encode($user->groups) . "\n";
                        $txt .= "score: $user->score\n";
                        $txt .= "\n-----------------------\n";
                        if ($idx % 3 == 0) {
                            $this->sendMessage($chat_id, $txt, null, null, null);
                            $txt = "";
                        }
                    }


            } elseif
            (strpos($Data, "send_to_users_ok$") !== false || ($reply && strpos($text, "send") !== false && in_array($from_id, Helper::$logs))) {

                set_time_limit(0);
//                DB::table('queue')->truncate();
                if ($Data) {
                    $app_id = explode("$", $Data)[1];
                    $message_id = $message_id - 1;
                } elseif (strpos($text, "send") !== false) {
                    $app_id = explode("d", $text)[1];
                    $message_id = $reply->message_id;
                }

                $this->user->step = null;
                $this->user->save();
                if (DB::table('queue')->where('message_id', $message_id)->count() == 0) {
                    if ($app_id == '0')
                        $ids = User::whereIn('app_id', [1, 2])->where('telegram_id', '!=', null)->get('telegram_id AS id', 'app_id');
                    else
                        $ids = User::where('app_id', $app_id)->where('telegram_id', '!=', null)->get('telegram_id AS id', 'app_id');
                    $ids = $ids->map(function ($item) use ($message_id, $from_id) {
                        $item['message_id'] = $message_id;
                        $item['from_id'] = $from_id;
                        return $item;
                    })->toArray();
                    DB::table('queue')->insert($ids);
                    $co = count($ids);
                    $this->sendMessage($chat_id, "■ تعداد $co پیام به صف اضافه شد!", null, null, null);

                } else {
                    $this->sendMessage($chat_id, "صف این پیام پر است", null, null, null);
                }

                //for post to channels
                if (DB::table('queue2')->where('message_id', $message_id)->count() == 0) {
                    if ($app_id == '0')
                        DB::table('queue2')->insert([
                            ['id' => '@vartastudio', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => null],
                            ['id' => '@esteghlalwallpapers', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => null],
                            ['id' => '@perspoliswallpapers', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => null],

                        ]);
                    else if ($app_id == '1')
                        DB::table('queue2')->insert([
                            ['id' => '@vartastudio', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
                            ['id' => '@esteghlalwallpapers', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
                        ]);
                    else if ($app_id == '2')
                        DB::table('queue2')->insert([
                            ['id' => '@vartastudio', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
                            ['id' => '@perspoliswallpapers', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
                        ]);
                    else
                        DB::table('queue2')->insert([
                            ['id' => '@vartastudio', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
//                            ['id' => '@footballapps', 'message_id' => $message_id, 'from_id' => $from_id, 'app_id' => $app_id],
                        ]);
                    $this->sendMessage($chat_id, "■ پیام به صف پستها اضافه شد!", null, null, null);

                } else {
                    $this->sendMessage($chat_id, "صف این پست پر است", null, null, null);
                }

//                if (Storage::exists('message_id.txt'))
//                    $storage = Storage::get('message_id.txt');
//                else {
//                    Storage::put('message_id.txt', json_encode($message_id - 1));
//                    $storage = $message_id - 1;
//                }
//
//                if (in_array($from_id, $this->Dev)) {
////                    $i = 0;
//                    foreach (DB::table('queue')->where('file', null)->get()->pluck('id') as $id) {
////                        $i++;
////                        if ($i > 20) {
////                            $i = 0;
////                            sleep(1);
////                        }
////                        sleep(1);
////                        $this->sendFile($id, $storage, null, User::where('telegram_id', $id)->first()->app_id);
//                        Helper::Forward($id, $from_id, $storage);
//                        DB::table('queue')->where('id', $id)->delete();
//                    }
//                    $this->DeleteMessage($chat_id, $message_id);
//                    if (Storage::exists('message_id.txt'))
//                        Storage::delete('message_id.txt');
//                    $this->sendMessage($chat_id, "■ با موفقیت به کاربران ایرانی ارسال شد!", null, null, null);
////                    DB::table('queue')->where('file', null)->delete();
//                }

            } elseif
            ($Data == "send_to_chats_ok") {
                $this->user->step = null;
                $this->user->save();

                if (in_array($from_id, $this->Dev))

                    foreach (Helper::$admins as $id => $data) {

                        $channel = $data['channel'];
                        $this->sendFile($channel, Storage::get('message.txt'), null);
                    }

//                $this->DeleteMessage($chat_id, $message_id);
                $this->sendMessage($chat_id, "■ با موفقیت به گروه ها ارسال شد!", null, null, null);


            } elseif
            ($Data == "statistics") {


                $txt = "";
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران استقلال" . PHP_EOL;
                $txt .= User::where('app_id', 1)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران پرسپولیس" . PHP_EOL;
                $txt .= User::where('app_id', 2)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران PSG" . PHP_EOL;
                $txt .= User::where('app_id', 5)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران بارسا" . PHP_EOL;
                $txt .= User::where('app_id', 3)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران میلان" . PHP_EOL;
                $txt .= User::where('app_id', 6)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران یونتوس" . PHP_EOL;
                $txt .= User::where('app_id', 8)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران لیورپول" . PHP_EOL;
                $txt .= User::where('app_id', 9)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "تعداد کاربران فورتنایت" . PHP_EOL;
                $txt .= User::where('app_id', 12)->count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;


//                $this->DeleteMessage($chat_id, $message_id);
                $this->sendMessage($chat_id, $txt, null, null, null);


            } elseif
            ($Data == "admin_help") {
                $txt = "اضافه کردن امتیاز به کاربر" . "\n";
                $txt .= "<user_id>:score:<score>" . "\n";

                $txt .= "ساخت بنر" . "\n";
                $txt .= "banner:<متن پیام>" . "\n";
                $txt .= "ساخت متن با کلید شیشه ای" . "\n";
                $txt .= "inline:<متن پیام>\nمتن1\nلینک1\n ..." . "\n";
                $txt .= "تبلیغ انتهای پیام ارسالی" . "\n";
                $txt .= "banner=name=link" . "\n";
                $txt .= "اضافه کردن سفارش تلگرام" . "\n";
                $txt .= "ordert:channel_username:member_limit(0=unlimit):day_limit(0=unlimit):follow_score:force(true,false)" . "\n";
                $this->sendMessage($chat_id, $txt, null, null, null);

            } elseif
            ($Data == "update_server_status") {
                Artisan::call('update:status');

            } elseif
            ((strpos($text, "ordert:") !== false)) {
                // ordert:channel_username:member_limit(0=unlimit):day_limit(0=unlimit):follow_score:force(true,false)
                if (in_array($from_id, $this->Dev)) {
                    $comnd = explode(':', $text);
                    if (count($comnd) != 6)
                        $this->sendMessage($chat_id, "فرمت ناصحیح" . "\nordert:channel_username:member_limit(0=unlimit):day_limit(0=unlimit):follow_score:force(true,false)", null, null, null);
                    else {
                        if (Order::where('chat_username', $comnd[1])->whereColumn('follow_now', '<', 'follow_limit')->exists() && $comnd[5] == false) {
                            $this->sendMessage($from_id, 'سفارش از قبل موجود است', null, null, null);
                            return;
                        }

                        $order = Order::create(['chat_username' => $comnd[1], 'follow_limit' => $comnd[2], 'day_limit' => $comnd[3], 'follow_score' => $comnd[4],
                            'follow_now' => 0, 'type' => 't', 'done' => false,]);
                        //add message to channel and users
                        $msg = "🚀" . PHP_EOL;
                        $msg .= "سفارش #$order->id" . PHP_EOL;
                        $msg .= "✨توی کانال زیر عضو شو و دکمه عضو شدم رو بزن و $order->follow_score ستاره برای خرید تصاویر داخل اپلیکیشن بگیر!" . PHP_EOL;
                        $limit = $order->follow_limit != 0 ? $order->follow_limit : 'ندارد';
                        $msg .= "🔔محدودیت عضویت: $limit" . PHP_EOL;
                        $date = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d | H:i') . PHP_EOL;
                        $punish = $order->follow_score * 2;
                        $msg .= "✅پاداش عضویت: $order->follow_score" . PHP_EOL;
                        $msg .= "⛔جریمه لفت دادن: $punish" . PHP_EOL;
                        $msg .= "⏰تاریخ درج سفارش: $date" . PHP_EOL;
                        $msg .= "🚧پشتیبانی: @develowper" . PHP_EOL;
                        $msg .= "t.me/$order->chat_username" . PHP_EOL;
                        $banner_button = json_encode(['inline_keyboard' => [
                            [['text' => "📪 ورود 📪", 'url' => "t.me/$order->chat_username"]],
                            [['text' => "✅ عضو شدم ✅", 'callback_data' => "member_added$$order->id"]],
                        ], 'resize_keyboard' => true]);
                        $i = 0;
                        foreach (User::where('telegram_id', '!=', null)->pluck('telegram_id')->toArray() as $id) {
                            $i++;
                            if ($i > 20) {
                                $i = 0;
                                sleep(1);
                            }
                            $this->sendMessage($id, $msg, null, null, $banner_button);
                        }
                        $this->sendMessage($from_id, "با موفقیت به همه ارسال شد", null, null, null);

                    }
                }
            } elseif
            ((strpos($Data, "member_added$") !== false)) {
                $d = explode("$", $Data);
                // "member_added$$order->id"
                if (count($d) == 2) {
                    if (!$this->user)
                        $this->popupMessage($data_id, "🚩ابتدا در ربات vartastudiobot ثبت نام کنید و یا اتصال به تلگرام را از داخل اپلیکیشن بزنید\nپشتیبانی develowper ");
                    elseif (Follow::where('telegram_id', $from_id)->exists())
                        $this->popupMessage($data_id, "🚩شما قبلا این امتیاز را دریافت کرده اید");
                    else {
                        $order = Order::where('id', $d[1])->first();
                        if (!$order || $order->follow_limit == $order->follow_now)
                            $this->popupMessage($data_id, "⌚مهلت این پیشنهاد به پایان رسیده است. منتظر سایر پیشنهادات باشید");
                        else {
                            $rank = $this->user_in_chat("@" . $order->chat_username, $from_id, $tc);
                            if ($rank != 'creator' && $rank != 'administrator' && $rank != 'member') {
                                $this->popupMessage($data_id, "🚩شما هنوز عضو نشده اید");
                                return;
                            }
                            Follow::create(['order_id' => $order->id, 'telegram_id' => $from_id, 'chat_username' => $order->chat_username,]);
                            $order->follow_now = $order->follow_now + 1;
                            $order->save();
                            $this->user->score = $this->user->score + $order->follow_score;
                            $this->user->save();
                            $this->sendMessage($chat_id, "✨تبریک!" . "\n" . "شما $order->follow_score امتیاز بابت عضویت در @$order->chat_username دریافت کردید!", null, null, $button);
                            foreach ($this->logs as $log)
                                $this->sendMessage($log, " کاربر  $username  در  @$order->chat_username عضو شد", null);


                        }
                    }
                }
            } elseif
            ((strpos($text, ":score:") !== false)) {


                $id = explode(":", $text)[0];
                $score = explode(":", $text)[2];
                if (in_array($from_id, $this->Dev)) {

                    $col = 'id';
                    if (substr($id, 0, 1) == "@")
                        $col = 'telegram_username';
                    $u = User::where($col, $id)->first();
                    $u->score += $score;
                    $u->save();
                    $this->sendMessage($u->telegram_id, "🙌 تبریک! \n $score  امتیاز به شما اضافه شد!  \n  امتیاز فعلی : $u->score", null, null, null);
                    $this->sendMessage($chat_id, "$score  امتیاز به $u->telegram_username  اضافه شد.", null, null, null);
                }

            } elseif
            ((strpos($text, "banner:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;
                $txt = " سلام   \n  توسط این ربات عضو تیم محبوبت شو و توی مسابقات کمکشون کن! \n     $this->bot ";
                $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => Helper::$app_link]]];
                $tmp = explode(":", $text);
                if (count($tmp) >= 2 && $tmp[1] != '')
                    $txt = $tmp[1];

                $this->sendMessage($chat_id, $txt, "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));


            } elseif ((strpos($text, "inline:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;
                $buttons = [];
                $inlines = explode("\n", $text);
                $txt = explode(":", array_shift($inlines))[1]; //remove first (inline)
                $len = count($inlines);
                foreach ($inlines as $idx => $item) {

                    if ($idx % 2 == 0 && $idx + 1 < $len)
                        array_push($buttons, [['text' => $inlines[$idx], 'url' => $inlines[$idx + 1]]]);

                }


                $this->sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));

            } elseif ($text == "ثبت نام✅") {

                if ($this->user) $this->sendMessage($chat_id, "شما قبلا ثبت نام کرده اید", 'MarkDown', $message_id, $button);
                else if (false && ($username == "@" || $username == "")) $this->sendMessage($chat_id, "لطفا قبل از ثبت نام, از منوی تنظیمات تلگرام خود, یک نام کاربری به اکانت خود تخصیص دهید!", 'MarkDown', $message_id, $button);
                else {
                    $this->user = User::create(['telegram_id' => $from_id, 'username' => $username, 'telegram_username' => $username, 'score' => $this->init_score, 'step' => 0, 'rank' => User::count()]);

                    $this->sendMessage($chat_id, "نام کاربری دلخواه خود را با حروف و اعداد انگلیسی و بدون فاصله وارد کنید \n(حداقل 6 حرف)", 'MarkDown', $message_id, $cancel_button);
                }
            } elseif ($text == "📱 دریافت اپلیکیشن 📱") {

                $this->sendMessage($chat_id, "📱لینک دریافت اپلیکیشن📱" . "\n" . Helper::$app_link, 'MarkDown', $message_id, $button);

            } elseif ($text == "🎴 ساخت دکمه شیشه ای 🎴") {
                if (!$this->user) $this->sendMessage($chat_id, "■  $first_name \n\n■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);

                else {
                    $cancel_button = json_encode(['keyboard' => [
                        [['text' => "لغو ❌"]],
                    ], 'resize_keyboard' => true]);
                    $this->user->step = 10;

                    $this->user->save();

                    $this->sendMessage($chat_id, "متن یا فایل خود را وارد کنید", 'MarkDown', $message_id, $cancel_button);
                }
            } elseif ($text == "⭐ جوایز ⭐") {
//                if (!$this->user) $this->sendMessage($chat_id, "■  $first_name \n\n■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);

//            else {
                $i = 0;
//                foreach (Order::whereColumn('follow_limit', '>', 'follow_now')->get() as $order) {
//                    $rank = $this->user_in_chat("@" . $order->chat_username, $from_id, $tc);
//                    if ($rank == 'creator' || $rank == 'administrator' || $rank == 'member') {
//                        continue;
//                    }
//                    $i++;
//                    $msg = "🚀" . PHP_EOL;
//                    $msg .= "سفارش #$order->id" . PHP_EOL;
//                    $msg .= "✨توی کانال زیر عضو شو و دکمه عضو شدم رو بزن و $order->follow_score ستاره برای خرید تصاویر داخل اپلیکیشن بگیر!" . PHP_EOL;
//                    $limit = $order->follow_limit != 0 ? $order->follow_limit - $order->follow_now : 'ندارد';
//                    $msg .= "🔔محدودیت عضویت: $limit" . PHP_EOL;
//                    $date = Jalalian::forge($order->created_at, new \DateTimeZone('Asia/Tehran'));
//                    $date = str_replace(':00', '', $date) . PHP_EOL;
//
//                    $punish = $order->follow_score * 2;
//                    $msg .= "✅پاداش عضویت: $order->follow_score" . PHP_EOL;
//                    $msg .= "⛔جریمه لفت دادن: $punish" . PHP_EOL;
//                    $msg .= "⏰تاریخ درج سفارش: $date" . PHP_EOL;
//                    $msg .= "🚧پشتیبانی: @develowper" . PHP_EOL;
//                    $msg .= "t.me/$order->chat_username" . PHP_EOL;
//                    $banner_button = json_encode(['inline_keyboard' => [
//                        [['text' => "📪 ورود 📪", 'url' => "t.me/$order->chat_username"]],
//                        [['text' => "✅ عضو شدم ✅", 'callback_data' => "member_added$$order->id"]],
//                    ], 'resize_keyboard' => true]);
//                    $this->sendMessage($chat_id, $msg, null, null, $banner_button);
//                }
                if ($i == 0)
                    $this->sendMessage($chat_id, "در حال حاضر پیشنهادی موجود نیست.روزای دیگه هم این قسمتو چک کن!", null, null, $button);
//                }
            } elseif (strpos($Data, "team$") !== false) {
                $app_id = explode('$', $Data)[1];
                if ($this->check('app_id', $app_id, $chat_id, $message_id, $cancel_button)) {
                    $this->user->app_id = $app_id;
                    $this->user->step = 5;
                    $this->user->save();
                    $this->sendMessage($chat_id, "    \n  رمز ورود خود را ثبت کنید:", 'MarkDown', null, $cancel_button);
                }
            } elseif (!$Data && $this->user && $this->user->step !== null && $this->user->step >= 0) {
                # user is registering

                switch ($this->user->step) {
                    case  0:
                        if ($this->check('username', $text, $chat_id, $message_id, $cancel_button)) {
                            $this->user->step = null;
                            $this->user->username = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "تیم خود را انتخاب کنید", 'MarkDown', $message_id, $team_button);

                        }
                        break;
                    case  1:
                        if ($this->check('app_id', $text, $chat_id, $message_id, $cancel_button)) {
                            $this->user->step = 5;
                            $this->user->email = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "رمز عبور را وارد کنید\n(حداقل 6 حرف)", 'MarkDown', $message_id);

                        }
                        break;
                    //check email or phone later
                    case  5:
                        if ($this->check('password', $text, $chat_id, $message_id, $cancel_button)) {

                            $this->user->password = Hash::make($text);
                            $this->user->step = null;
                            $this->user->rank = User::count();
                            $this->user->save();
                            $this->createUserImage($this->user->telegram_id);
                            $this->sendMessage($chat_id, "✨با موفقیت ثبت نام شدی و 5 امتیاز گرفتی!\nمی تونی با اشتراک گذاری بنر خودت و یا دیدن ویدیو در اپلیکیشن و یا زدن دکمه جوایز، امتیازت روافزایش بدی.\n✨حالا از دکمه دریافت اپلیکیشن، اپلیکیشن تیمت رو دریافت کن و بهشون توی بردن مسابقه کمک کن!\n👇👇👇✨", 'MarkDown', $message_id, $button);

                            foreach ($this->logs as $log)
                                $this->sendMessage($log, "✨  کاربر [$first_name](tg://user?id=$from_id) در ربات ورتا ثبت نام کرد." . "\nApp ID:" . $this->user->app_id, 'MarkDown');
                            if ($this->user->app_id == 1)
                                $this->sendMessage('@esteghlalwallpapers', "💖💖 کاربر [$first_name](tg://user?id=$from_id) به ارتش استقلال اضافه شد.", 'MarkDown');
                            elseif ($this->user->app_id == 2)
                                $this->sendMessage('@perspoliswallpapers', "💙💙 کاربر [$first_name](tg://user?id=$from_id) به ارتش پرسپولیس اضافه شد.", 'MarkDown');

                            //suggest order
                            $order = Order::get()->first();
                            //add message to channel and users
                            $msg = "🚀" . PHP_EOL;
                            $msg .= "سفارش #$order->id" . PHP_EOL;
                            $msg .= "✨توی کانال زیر عضو شو و دکمه عضو شدم رو بزن و $order->follow_score ستاره برای خرید تصاویر داخل اپلیکیشن بگیر!" . PHP_EOL;
                            $limit = $order->follow_limit != 0 ? $order->follow_limit : 'ندارد';
                            $msg .= "🔔محدودیت عضویت: $limit" . PHP_EOL;
                            $date = Jalalian::forge($order->created_at, new \DateTimeZone('Asia/Tehran'));
                            $date = str_replace(':00', '', $date) . PHP_EOL;

                            $punish = $order->follow_score * 2;
                            $msg .= "✅پاداش عضویت: $order->follow_score" . PHP_EOL;
                            $msg .= "⛔جریمه لفت دادن: $punish" . PHP_EOL;
                            $msg .= "⏰تاریخ درج سفارش: $date" . PHP_EOL;
                            $msg .= "🚧پشتیبانی: @develowper" . PHP_EOL;
                            $msg .= "t.me/$order->chat_username" . PHP_EOL;
                            $banner_button = json_encode(['inline_keyboard' => [
                                [['text' => "📪 ورود 📪", 'url' => "t.me/$order->chat_username"]],
                                [['text' => "✅ عضو شدم ✅", 'callback_data' => "member_added$$order->id"]],
                            ], 'resize_keyboard' => true]);
                            $this->sendMessage($chat_id, $msg, null, null, $banner_button);

                            $ref = Invites::where('new_telegram_id', $from_id)->first();
                            if ($ref) {
                                $user = User::where('telegram_id', $ref->invited_by)->first();
                                if ($user) {
                                    $user->score += $this->ref_score;
                                    $user->save();
                                    $this->sendMessage($ref->invited_by, "✨  کاربر [$first_name](tg://user?id=$from_id)  با لینک دعوت شما ثبت نام کرد و $this->ref_score امتیاز به شما اضافه شد!   .", 'MarkDown', null, null);
                                }
                            }
                        }
                        break;
// edit section
                    case
                    6:
                        if ($this->check('username', $text, $chat_id, $message_id, $return_button)) {
                            $this->user->step = null;
                            $this->user->username = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                        }
                        break;
                    case  7:
                        if ($this->check('email', $text, $chat_id, $message_id, $return_button)) {
                            $this->user->step = null;
                            $this->user->email = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                        }
                        break;
                    case  8:
                        if ($this->check('password', $text, $chat_id, $message_id, $return_button)) {
                            $this->user->password = Hash::make($text);
                            $this->user->step = null;
                            $this->user->save();
                            $this->sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);
                        }
                        break;
                    //send to users
                    case  90:
//                        if (!in_array($from_id, $this->Dev))
//                    return;
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok$0"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendFile($from_id, json_encode($message), null, null);
                        $this->sendMessage($chat_id, "*از ارسال به کاربران اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //send to users
                    case  91:
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok$1"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendFile($from_id, json_encode($message), null, 1);
                        $this->sendMessage($chat_id, "*از ارسال به کاربران استقلال اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //send to users
                    case  92:
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok$2"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendFile($from_id, json_encode($message), null, 2);
                        $this->sendMessage($chat_id, "*از ارسال به کاربران پرسپولیس اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //send to users
                    case  93:
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok$3"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendFile($from_id, json_encode($message), null, 3);
                        $this->sendMessage($chat_id, "*از ارسال به کاربران بارسا اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //send to users
                    case  94:
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok$4"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendFile($from_id, json_encode($message), null, 4);
                        $this->sendMessage($chat_id, "*از ارسال به کاربران رئال اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;


                    //get banner button link
                    case  10:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && strlen($text) > 1000) {
                            $this->sendMessage($chat_id, "*طول پیام از 1000 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step++;
                        $this->user->save();
                        Storage::put("$from_id.txt", json_encode($message));
                        $this->sendMessage($chat_id, "لینک دکمه را وارد کنید", 'MarkDown', $message_id, $cancel_button);

                        break;
                    //get banner button name
                    case  11:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && strlen($text) > 50) {
                            $this->sendMessage($chat_id, "*طول لینک از 50 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step++;
                        $this->user->save();
                        $txt = Storage::get("$from_id.txt");
                        Storage::put("$from_id.txt", json_encode(['message' => $txt, 'link' => $text]));
                        $this->sendMessage($chat_id, "متن دکمه را وارد کنید", 'MarkDown', $message_id, $cancel_button);

                        break;
                    //send banner
                    case  12:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && strlen($text) > 50) {
                            $this->sendMessage($chat_id, "*متن دکمه از 50 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step = null;
                        $this->user->save();
                        $txt = json_decode(Storage::get("$from_id.txt"));
                        Storage::put("$from_id.txt", json_encode(['message' => $txt->message, 'link' => $txt->link, 'name' => $text,]));
                        $this->sendBanner($from_id, Storage::get("$from_id.txt"));
                        $this->sendMessage($chat_id, "با موفقیت تولید شد!", 'MarkDown', $message_id, $button);


                        break;
                }

            } else if ($text == 'بنر' || $Data == 'بنر' || $text == "📌 دریافت بنر دعوت 📌") {
                $this->user = User::where('telegram_id', $from_id)->first();
                if (!$this->user) {
                    $this->sendMessage($chat_id, "برای دریافت بنر ابتدا در ربات ثبت نام کنید\n $this->bot", null, $message_id, null);
                    return;
                }

                if ($tc == 'private') {
                    $this->sendMessage($from_id, "بنر زیر را فوروارد کنید و در صورت ثبت نام افراد دعوت شده, $this->ref_score امتیاز دریافت نمایید. ", "Markdown", null, null, true);

                }
                $ref_link = "https://t.me/" . str_replace("@", "", $this->bot) . "?start=" . base64_encode("$from_id");
                $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => Helper::$app_link]], [['text' => '👈 ورود به ربات 👉', 'url' => $ref_link]]];
                $this->sendMessage($chat_id, " 🔔 " . "📌 نبرد استقلال و پرسپولیس شروع شد! \n توی تیم محبوبت ثبت نام کن و با کمک هم تیمی ها برنده باش \n 🔵استقلال 👑 پرسپولیس🔴" . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . "  \n$ref_link \n\n" . "$this->bot", null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), false);

            }//connect to telegram account
            elseif ($user = User::where('step', 80)->where('telegram_id', 'like', "%$$from_id")->where('telegram_id', 'like', "c$%")->first()) {
                $id = explode('$', $user->telegram_id)[1];
                $user = User::where('id', $id)->first();
                if ($user)
                    if ($user && $phone_number /*&& password_verify($text, $user->password)*/) {
                        $user->telegram_id = $from_id;
                        $user->phone_number = $phone_number;
                        $user->telegram_username = '@' . $message->from->username;
                        $user->step = null;
                        $user->save();
                        $button = json_encode(['keyboard' => [
                            in_array($from_id, $this->Dev) ? [['text' => 'پنل مدیران🚧']] : [],

                            [['text' => "📱 دریافت اپلیکیشن 📱"]],
                            [['text' => "⭐ جوایز ⭐"]],
                            [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                            [['text' => "📌 دریافت بنر دعوت 📌"]],
                            [['text' => 'امتیاز من💰']],
                            [['text' => $user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                            [['text' => "📱 خرید شارژ 📱"], ['text' => "📱 خرید اینترنت 📱"]],
                            [['text' => 'درباره ربات🤖'], ['text' => "🙏 حمایت از ما 🙏"]],

                        ], 'resize_keyboard' => true]);
                        $this->sendMessage($from_id, "اکانت شما با موفقیت فعال شد!", 'MarkDown', null, $button);
                        $this->sendMessage($chat_id, " با زدن دکمه دریافت بنر دعوت و ارسال برای دوستات، امتیاز بگیر🙏" . PHP_EOL . "@vartastudiobot", null, $message_id, null, true);

                        foreach (Helper::$logs as $log)
                            $this->sendMessage($log, "یک اکانت متصل شد!" . " [$first_name](tg://user?id=$from_id)  ", 'MarkDown', null, null);

                    } else {
                        $this->sendMessage($from_id, "اطلاعات وارد شده اشتباه است", 'MarkDown', null, $cancel_button);
                    }

            } else if ($text == "/start$this->bot") {
                $this->DeleteMessage($chat_id, $message_id);
                $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];
                $this->sendMessage($chat_id, " $first_name " . "📌 با این ربات توی تیم محبوبت ثبت نام کن و اپلیکیشن تیمت رو دانلود کن و با کمک هم تیمی ها برنده باش \n 🔵استقلال 👑 پرسپولیس🔴", "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);

            }  //referral
            else if (strpos($text, "/start ") !== false) { // agar ebarate /start ersal shod
                $this->user = User::where('telegram_id', $from_id)->first();
                $button = json_encode(['keyboard' => [
                    in_array($from_id, $this->Dev) ? [['text' => 'پنل مدیران🚧']] : [],

                    [['text' => "📱 دریافت اپلیکیشن 📱"]],
                    [['text' => "⭐ جوایز ⭐"]],
                    [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                    [['text' => "📌 دریافت بنر دعوت 📌"]],
                    [['text' => 'امتیاز من💰']],
                    [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                    [['text' => "📱 خرید شارژ 📱"], ['text' => "📱 خرید اینترنت 📱"]],
                    [['text' => 'درباره ربات🤖'], ['text' => "🙏 حمایت از ما 🙏"]],
                ], 'resize_keyboard' => true]);


                foreach ($this->logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات ورتا را استارت کرد.", 'MarkDown');
                $code = substr($text, 7); // joda kardan id kasi ke rooye linke davatesh click shode
                if (!empty($code)) {
                    $id = base64_decode($code);

                    if (str_starts_with($id, 'c$')) {

                        $user = User::where('id', explode('$', $id)[1])/*->where('role', 'User')*/
                        ->first();

                        if ($this->user || $user->telegram_id != null)
                            $this->sendMessage($from_id, "این اکانت از قبل ثبت شده است.", "Markdown", null, $cancel_button, false);
                        else if ($user) {
//                            $this->sendMessage($from_id, "رمز اکانت خود را وارد کنید:", "Markdown", null, $cancel_button, false);
                            $contact_keyboard = json_encode(['keyboard' => [
                                [['text' => '☎️تایید اکانت', 'request_contact' => true]],
                                [['text' => "لغو ثبت نام❌"]],
                            ], 'resize_keyboard' => true]);
                            $this->sendMessage($from_id, "برای فعالسازی، دکمه تایید اکانت را بزنید:", "Markdown", null, $contact_keyboard, false);
                            $user->step = 80;
                            $user->telegram_id = "$id$$from_id";
                            $user->save();


                        } else {
                            $this->sendMessage($from_id, "🔔 کاربر یافت نشد!", "Markdown" . explode('$', $id)[1], null, null, false);

                        }
                        return;
                    }

                    Invites::updateOrCreate(['new_telegram_id' => $from_id], ['new_telegram_id' => $from_id, 'invited_by' => "$id"]);
                    $this->sendMessage($id, "\n🔔\nهم اکنون" . " [$first_name](tg://user?id=$from_id)  " . " با لینک دعوت شما وارد ربات شد. در صورت ثبت نام$this->ref_score امتیاز به شما اضافه خواهد شد   ", "Markdown", null, null, false);


                }
                if ($this->user) $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋\n\n■ چه کاری براتون انجام بدم؟ ", null, $message_id, $button);
                else $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋  ", null, $message_id, $button);
                $this->sendMessage($chat_id, " 🔔 " . "📌 با این ربات توی تیم محبوبت ثبت نام کن و اپلیکیشن تیمت رو دانلود کن و با کمک هم تیمی ها برنده باش \n 🔵استقلال 👑 پرسپولیس🔴" . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . " \n" . "$this->bot", null, null, $button, false);


            } else {
                $this->sendMessage($chat_id, "متوجه نشدم 😓", null, $message_id, null);

            }
        } elseif
        ($tc == 'channel') {
            return;
//            $this->sendMessage(Helper::$logs[0], file_get_contents('php://input'), 'MarkDown', null, null);


//            if (preg_match('/^\/?(add|نصب)$/ui', $text, $match)) {
//                if (!$this->Admin($chat_id, $from_id, $tc, $chat_username))
//                    return;
//                if ($chat_username == '@') {
//                    $this->sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد.\n 🔸روی نام کانال کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);
//                    return;
//                }
//
//                $this->user = User::where('channels', 'like', "%\"$chat_username\"%")->first();
//                if (!$this->user) {
//                    $this->sendMessage($chat_id, "🔸 ابتدا باید کانال را در ربات ثبت نمایید!\n🔸 *منوی اصلی ⬅ ثبت کانال💥* \n  $this->bot", 'Markdown', $message_id);
//                    return;
//                }
//
//                $this->sendMessage($chat_id, "■ ربات با موفقیت نصب شد !💫\n\n● برای تبلیغ کانال  به قسمت دیوار📈 سپس درج کانال/گروه در دیوار📌 مراجعه کنید\n \n آموزش ربات \n $this->tut_link  * $this->bot *    \n ", 'MarkDown', $message_id, $this->button);
//
//
//            }
        } elseif
        ($tc == 'supergroup' || $tc == 'group') {
            return;


//            if (preg_match('/^\/?(add|نصب)$/ui', $text, $match)) {
//
//                if (!$this->Admin($chat_id, $from_id, $tc, $chat_username))
//                    return;
//                if (!$this->Admin($chat_id, $this->bot_id, $tc, $chat_username)) {
//                    $this->sendMessage($chat_id, "🔹*ابتدا ربات را در گروه ادمین کنید و مجدد تلاش نمایید*", 'Markdown', $message_id);
//                    return;
//                }
//                if ($chat_username == '@') {
//                    $this->sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد.\n 🔸روی نام کانال کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);
//                    return;
//                }
//                $this->user = User::where('groups', 'like', "%\"$chat_username\"%")->first();
//                if (!$this->user) {
//                    $this->sendMessage($chat_id, "🔸 ابتدا باید گروه را در ربات ثبت نمایید!\n🔸 *منوی اصلی ⬅ ثبت گروه💥* \n  $this->bot", 'Markdown', $message_id);
//                    return;
//                }
//
//                $this->sendMessage($chat_id, "🔷 *ربات با موفقیت نصب شد. اکنون می توانید گروه خود را در دیوار ربات تبلیغ نمایید!* \n \n آموزش ربات \n $this->tut_link  $this->info", 'MarkDown', $message_id, $this->button);
//
//
//            }
            if ($new_chat_member && (/*$chat_username == "@lamassaba" ||*/
                    $chat_username == "@magnetgramsupport" || $chat_username == "@magnetgramadvs")) {
                $txt = "سلام $first_name\n";
                $link = "https://t.me/" . str_replace("@", "", $this->bot);
                $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => Helper::$app_link]], [['text' => '👈 ورود به ربات 👉', 'url' => $link]]];
                $txt .= " 🔔 " . "📌 نبرد استقلال و پرسپولیس شروع شد! \n توی تیم محبوبت ثبت نام کن و با کمک هم تیمی ها برنده باش \n 🔵استقلال 👑 پرسپولیس🔴" . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . "  \n$link \n\n";


                $this->DeleteMessage($chat_id, $message_id);
                $this->sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);


            }
//            elseif ($new_chat_members) {
//
//            } //
//            elseif ($left_chat_member) {
//
//
//            }
        }
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
//        unlink("error_log");
    }


    private
    function popupMessage2($data_id, $from_id, $message)
    {
        return $this->creator('CallbackQuery', [
            'id' => $data_id,
            'from' => $from_id,
            'message' => $message,

        ]);
    }

    private
    function popupMessage($data_id, $text)
    {
        return $this->creator('answerCallbackQuery', [
            'callback_query_id' => $data_id,
            'text' => $text,

            'show_alert' => true, # popup / notification
            'url' => null,# t.me/your_bot?start=XXXX,
            'cache_time' => null
        ]);
    }

    function sendMessage($chat_id, $text, $mode = null, $reply = null, $keyboard = null, $disable_notification = false)
    {
        return $this->creator('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
            'disable_notification' => $disable_notification,
        ]);
    }


    function creator($method, $datas = [])
    {

        return \Telegram::creator($method, $datas);
        $url = "https://qr-image-creator.com/wallpapers/api/dabel_telegram";
        $datas['cmnd'] = $method;

//        $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);

        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            return json_decode($res);
        }
    }

    private
    function inviteToChat($chat_id)
    {

        return $this->creator('exportChatInviteLink', ['chat_id' => $chat_id,]);

    }

    private
    function getChatMembersCount($chat_id)
    {
        $res = $this->creator('getChatMembersCount', ['chat_id' => $chat_id,])->result;
        if ($res)
            return (int)$res; else return 0;
    }

    private
    function getChatInfo($chat_id)
    {
        return $this->creator('getChat', ['chat_id' => $chat_id]);
    }

    private
    function Admin($chat_id, $from_id, $chat_type, $chat_username)
    {
        if ($chat_type == 'supergroup' || $chat_type == 'group') {
            $get = $this->creator('getChatMember', ['chat_id' => $chat_id, 'user_id' => $from_id]);
            $rank = $get->result->status;

            if ($rank == 'creator' || $rank == 'administrator') {
                return true;
            } else {
//                $this->sendMessage($chat_id, "■  کاربر غیر مجاز \n $this->bot  ", 'MarkDown', null);
                return false;
            }
        } else if ($chat_type == 'channel') {
            return true;
//            $admins = $this->creator('getChatAdministrators', ['chat_id' => $chat_id])->result;
//            $is_admin = false;
//
//            foreach ($admins as $admin) {
//                if ($from_id == $admin->user->id) {
//                    $is_admin = true;
//                }
//            }
//            return $from_id;

//            $this->user = User::whereIn('telegram_id', $admin_ids)->orWhere('channels', 'like', "%[$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username]%")->first();
//            if (!User::orWhere('channels', 'like', "%[$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username]%")->exists())
//                $this->sendMessage($chat_id, "■ ابتدا کانال را در ربات ثبت نمایید  \n📣$this->bot  ", 'MarkDown', null);


//            return $this->user ? true : false;
        }
    }

    private
    function get_chat_type($chat_id)
    {

        return $this->creator('getChat', [
            'chat_id' => $chat_id,

        ])->result->type;
    }

    private
    function user_in_chat($chat_id, $user_id, $chat_type = null)
    {
        return $this->creator('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id
        ])->result->status;
    }

    private
    function EditMessageText($chat_id, $message_id, $text, $mode = null, $keyboard = null)
    {
        $this->creator('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function EditKeyboard($chat_id, $message_id, $keyboard)
    {
        $this->creator('EditMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function DeleteMessage($chatid, $massege_id)
    {
        $this->creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    private
    function Kick($chatid, $fromid)
    {
        $this->creator('KickChatMember', [
            'chat_id' => $chatid,
            'user_id' => $fromid
        ]);
    }

    private
    function Forward($chatid, $from_id, $massege_id)
    {
        $this->creator('ForwardMessage', [
            'chat_id' => $chatid,
            'from_chat_id' => $from_id,
            'message_id' => $massege_id
        ]);
    }

    function MarkDown($string)
    {
        return str_replace(["`", "_", "*", "[", "]"], "\t", $string);
    }


    private
    function check($what, $text, $chat_id, $message_id, $cancel_button)
    {
        $message = null;
        if ($what == 'username') {
            if (strlen($text) < 6)
                $message = "نام کاربری  حداقل  ۶ حرف باشد";
            elseif (strlen($text) > 50)
                $message = "نام کاربری  حداکثر 50 حرف باشد";
            else if (!preg_match('/^[A-Za-z]+[A-Za-z0-9@_][A-Za-z0-9@]{1,28}$/', $text))
                $message = 'نام کاربری با حروف شروع شود و می تواند شامل عدد و _ و @ باشد';
            elseif (User::where("username", $text)->exists())
                $message = "نام کاربری تکراری است";
        } else if ($what == 'password') {
            if (strlen($text) < 6)
                $message = "طول گذرواژه حداقل ۶";
            elseif (strlen($text) > 50)
                $message = "طول گذرواژه حداکثر 50";

        } else if ($what == 'email') {

            if (!filter_var($text, FILTER_VALIDATE_EMAIL))
                $message = "ایمیل نامعتبر است";

            else if (User::where('email', $text)->exists())
                $message = "این ایمیل از قبل ثبت شده است!";


        } else if ($what == 'app_id') {

            if (!in_array($text, [1, 2]))
                $message = "شماره تیم نامعتبر است";


        } else if ($what == 'phone') {
            if (!starts_with($text, '09') || strlen($text) != 11)
                $message = "شماره موبایل باید با اعداد انگلیسی و 11 رقمی باشد و با 09 شروع شود";
        } else if ($what == 'donate') {
            if (!is_numeric($text) || $text < 1000)
                $message = "مبلغ باید بالاتر از 2000 ریال باشد";
        }

        if ($message) {

            $this->sendMessage($chat_id, $message, 'MarkDown', $message_id, $cancel_button);
            return false;
        } else {
            return true;
        }

    }

    public
    function request($request)
    {


        $http = new \GuzzleHttp\Client(['base_uri' => $request['url'],
        ]);

        try {
            $response = $http->post(/*route('passport.token'*/
                ''
                , [

                'headers' => ['cache-control' => 'no-cache',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => $request['params']
            ]);

            return json_decode($response->getBody()->getContents(), true)["result"]["status"];
        } catch (\Guzzlehttp\Exception\BadResponseException $e) {
            if ($e->getCode() == 400) {
                return json_decode($e->getResponse()->getBody()->getContents(), true)["description"];
            } else if ($e->getCode() == 401) {
                return response()->json($e->getMessage(), $e->getCode());
            }
            return response()->json($e->getMessage(), $e->getCode());

        }
    }

    private
    function sendFile($chat_id, $storage, $reply = null, $app_id = null)
    {


        $message = json_decode($storage);
        $message_id = $message->message_id;
        $from_chat_id = $message->chat->id;
        $poll = $message->poll;
        $text = $message->text;
        $sticker = $message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = $message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = $message->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = $message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = $message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = $message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = $message->voice; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = $message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = $message->caption . "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username;
        $media = $message->media;

        if ($text) {
            $adv_section = explode('banner=', $text); //banner=name=@id
            $text = $adv_section[0];
        } else if ($caption) {
            $adv_section = explode('banner=', $caption);
            $caption = $adv_section[0];
        }
        if (count($adv_section) == 2) {

            $link = explode('=', $adv_section[1]);
            $trueLink = $link[1];
            foreach ($link as $idx => $li) {
                if ($idx > 1)
                    $trueLink .= ('=' . $li);
            }
            $buttons = [[['text' => "👈 $link[0] 👉", 'url' => $trueLink]],
                $app_id == 1 || $app_id == null ? [['text' => '🔵 کانال ارتش استقلال 🔵', 'url' => "https://t.me/esteghlalwallpapers"]] : [],
                $app_id == 2 || $app_id == null ? [['text' => '🔴 کانال ارتش پرسپولیس 🔴', 'url' => "https://t.me/perspoliswallpapers"]] : [],
                [['text' => '👦 پشتیبانی 👦', 'url' => "https://t.me/develowper"]],
            ];
        } else {
//            if ($text) $text = $text ;  //. "\n\n" . $this->bot;
//            else if ($caption) $caption = $caption . "\n\n" . $this->bot;
            $buttons = [
                $app_id == 1 || $app_id == null ? [['text' => '🔵 کانال ارتش استقلال 🔵', 'url' => "https://t.me/esteghlalwallpapers"]] : [],
                $app_id == 2 || $app_id == null ? [['text' => '🔴 کانال ارتش پرسپولیس 🔴', 'url' => "https://t.me/perspoliswallpapers"]] : [],
                [['text' => '👦 پشتیبانی 👦', 'url' => "https://t.me/develowper"]],
            ];
        }
        $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);

        if ($text)
            $this->creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $text . "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username,   //. "\n $this->bot",
                'parse_mode' => null,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($photo)
            $this->creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($audio)
            $this->creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($document)
            $this->creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($video)
            $this->creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($animation)
            $this->creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($voice)
            $this->creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($video_note)
            $this->creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($sticker)
            $this->creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($poll)
//            $this->creator('forwardMessage', [
//                'chat_id' => $chat_id,
//                'from_chat_id' => $from_chat_id,
//                'message_id' => $message_id + 1
//            ]);
            $this->creator('sendPoll', [
                'chat_id' => $chat_id,
                'question' => $poll->question,
                'options' => json_encode(array_column($poll->options, 'text')),//  ,
                'type' => $poll->type,//quiz
                'allows_multiple_answers' => $poll->allows_multiple_answers,
                'is_anonymous' => $poll->is_anonymous,
                'correct_option_id' => $poll->correct_option_id, // index of correct answer for quiz
// //            'open_period' => 5-600,   this or close_date
// //            'close_date' => 5, 5 - 600,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        else if ($media)
            $this->creator('sendMediaGroup', [
                'chat_id' => $chat_id,
                'media' => $media/*[count($photo) - 1]->file_id*/,


                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
    }

    private
    function sendBanner($chat_id, $storage)
    {


        $storage = json_decode($storage);
        $message = json_decode($storage->message);
        $link = $storage->link;
        $name = $storage->name;
        $poll = $message->poll;
        $text = $message->text;
        $sticker = $message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = $message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = $message->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = $message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = $message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = $message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = $message->voice; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = $message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = $message->caption . "\n $this->bot";


        $buttons = [[['text' => "👈 $name 👉", 'url' => $link]]];

        $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);
        Storage::put("log.txt", $text);

        if ($text)
            $this->creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $text . "\n $this->bot",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($photo)
            $this->creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($audio)
            $this->creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($document)
            $this->creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video)
            $this->creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($animation)
            $this->creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($voice)
            $this->creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video_note)
            $this->creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($sticker)
            $this->creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($poll)
            $this->creator('sendPoll', [
                'chat_id' => $chat_id,
                'question' => "",
                'options' => json_encode(["1", "2", "3"]),
                'type' => "regular",//quiz
                'allows_multiple_answers' => false,
                'correct_option_id' => 0, // index of correct answer for quiz
//            'open_period' => 5-600,   this or close_date
//            'close_date' => 5, 5 - 600,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);

//        Storage::delete("$chat_id.txt");
    }

    private
    function createChatImage($photo, $chat_id)
    {
        if (!isset($photo) || !isset($photo->big_file_id)) return;
        $client = new \GuzzleHttp\Client();
        $res = $this->creator('getFile', [
            'file_id' => $photo->big_file_id,

        ])->result->file_path;

        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        Storage::put("public/chats/$chat_id.jpg", $client->get($image)->getBody());

    }

    private
    function createUserImage($user_id)
    {

        $client = new \GuzzleHttp\Client();
        $res = $this->creator('getUserProfilePhotos', [
            'user_id' => $user_id,

        ])->result->photos;
        // return json_encode($res);
        if (!isset($res) || count($res) == 0) return;
        $res = $this->creator('getFile', [
            'file_id' => $res[0][count($res[0]) - 1]->file_id,

        ])->result->file_path;

        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        Storage::put("public/users/$user_id.jpg", $client->get($image)->getBody());

    }

}
