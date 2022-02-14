<?php

use App\Models\County;
use App\Models\Province;
use App\Models\Sport;
use Morilog\Jalali\Jalalian;

class Telegram
{

    static function sendMessage($chat_id, $text, $mode = null, $reply = null, $keyboard = null, $disable_notification = false, $app_id = null)
    {

        return self::creator('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
            'disable_notification' => $disable_notification,
        ]);


    }

    static function deleteMessage($chatid, $massege_id)
    {
        return self::creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    static function sendPhoto($chat_id, $photo, $caption, $reply = null, $keyboard = null)
    {


        return self::creator('sendPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);

    }


    static function sendMediaGroup($chat_id, $media, $keyboard = null, $reply = null)
    {
//2 to 10 media can be send

        return self::creator('sendMediaGroup', [
            'chat_id' => $chat_id,
            'media' => json_encode($media),
            'reply_to_message_id' => $reply,

        ]);

    }

    static function sendSticker($chat_id, $file_id, $keyboard, $reply = null, $set_name = null)
    {
        return self::creator('sendSticker', [
            'chat_id' => $chat_id,
            'sticker' => $file_id,
            "set_name" => $set_name,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }


    static function logAdmins($msg, $mode = null)
    {
        foreach (Helper::$logs as $log)
            self::sendMessage($log, $msg, $mode);

    }

    static function creator($method, $datas = [])
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

        $res = curl_exec($ch);
        echo $res;
        $res = json_decode($res);
//        if ($res && $res->ok == false)
//            self::sendMessage(Helper::$logs[0], /*"[" . $datas['chat_id'] . "](tg://user?id=" . $datas['chat_id'] . ") \n" .*/
//                json_encode($datas) . "\n" . $res->description, null, null, null);

//        Helper::sendMessage(Helper::$logs[0], ..$res->description, null, null, null);
        if (curl_error($ch)) {
            self::sendMessage(Helper::$logs[0], 'curl error' . PHP_EOL . json_encode(curl_error($ch)), null, null, null);
            var_dump(curl_error($ch));
            return null;
        } else {
            return $res;
        }
    }

    static function MarkDown($string)
    {
        $string = str_replace(["_",], '\_', $string);
        $string = str_replace(["`",], '\`', $string);
        $string = str_replace(["*",], '\*', $string);
        $string = str_replace(["~",], '\~', $string);


        return $string;
    }

    static function log($to, $type, $data)
    {
//        try {
        if (str_contains(url('/'), 'localhost'))
            return;
        $us = auth()->user();
        $admin = ($us->role == 'ad' || $us->role == 'go' ? ($us->name ? "$us->name $us->family" : $us->username) : false);
        $now = Jalalian::forge('now', new DateTimeZone('Asia/Tehran'));
        $time = $now->format('%A, %d %B %Y ⏰ H:i');
        $msg = $time . PHP_EOL;
        $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        switch ($type) {
            case 'user_created':
                $msg .= " 🟢 " . "یک کاربر ساخته شد" . PHP_EOL;
                $msg .= " 👤 " . "نام کاربری" . PHP_EOL;
                $msg .= $data->username . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس" . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                break;

            case 'player_created':
                $msg .= " 🟡 " . "یک بازیکن ساخته شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . ' ' . $data->family . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🧬 " . "جنسیت: " . ($data->is_man ? 'مرد' : 'زن') . PHP_EOL;
                $msg .= " 📅 " . "تاریخ تولد: " . Jalalian::fromDateTime($data->born_at)->format('%Y/%m/%d') . PHP_EOL;
                $msg .= " 📏 " . "قد: " . $data->height . PHP_EOL;
                $msg .= " ⚓ " . "وزن: " . $data->weight . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . Sport::firstOrNew(['id' => $data->sport_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 📱 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'coach_created':
                $msg .= " 🟠 " . "یک مربی ساخته شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . ' ' . $data->family . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🧬 " . "جنسیت: " . ($data->is_man ? 'مرد' : 'زن') . PHP_EOL;
                $msg .= " 📅 " . "تاریخ تولد: " . Jalalian::fromDateTime($data->born_at)->format('%Y/%m/%d') . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . Sport::firstOrNew(['id' => $data->sport_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 📱 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'club_created':
                $msg .= " 🔵 " . "یک باشگاه ساخته شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . implode(', ', collect(json_decode($data->times))->map(function ($el) {
                        return Sport::firstOrNew(['id' => $el->id])->name;
                    })->toArray()) . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "آدرس: " . $data->address . PHP_EOL;
                $msg .= " 📱 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'shop_created':
                $msg .= " 🟣 " . "یک فروشگاه ساخته شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "آدرس: " . $data->address . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'product_created':
                $shop = \App\Models\Shop::firstOrNew(['id' => $data->shop_id]);
                $msg .= " ⚫️ " . "یک محصول ساخته شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📈 " . "قیمت اصلی: " . PHP_EOL;
                $msg .= $data->price . PHP_EOL;
                $msg .= " 📉 " . "قیمت با تخفیف: " . PHP_EOL;
                $msg .= $data->discount_price . PHP_EOL;
                $msg .= " 📊 " . "تعداد: " . PHP_EOL;
                $msg .= $data->count . PHP_EOL;
                $msg .= " 🚩 " . "دسته بندی: " . Sport::firstOrNew(['id' => $data->group_id])->name . PHP_EOL;
                $msg .= " 🛒 " . "فروشگاه: " . PHP_EOL;
                $msg .= $shop->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $shop->phone . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'blog_created':
                $user = \App\Models\User::firstOrNew(['id' => $data->user_id]);
                $msg .= " 🟤 " . "یک خبر اضافه شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نویسنده: " . PHP_EOL;
                $msg .= ($user->name ? "$user->name $user->family" : "$user->username") . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $user->phone . PHP_EOL;
                $msg .= " 📌 " . url('blog') . "/$data->id/" . str_replace(' ', '-', $data->title) . PHP_EOL;

                break;

            case 'payment':
                $user = \App\Models\User::firstOrNew(['id' => $data->user_id]);
                $msg .= " ✔️ " . "یک پرداخت انجام شد" . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شماره سفارش: " . PHP_EOL . $data->order_id . PHP_EOL;
                $msg .= " 💸 " . "مبلغ(ت): " . PHP_EOL . $data->amount . PHP_EOL;
                $msg .= " 👤 " . "کاربر: " . PHP_EOL;
                $msg .= ($user->name ? "$user->name $user->family" : "$user->username") . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $user->phone . PHP_EOL;
                $msg .= " 🧾 " . "پیگیری شاپرک: " . PHP_EOL;
                $msg .= $data->Shaparak_Ref_Id . PHP_EOL;
                $msg .= " 📦 " . "محصول: " . PHP_EOL;
                $msg .= $data->pay_for . PHP_EOL;

                break;
            case 'user_edited':
                $msg .= " 🟥 " . ($admin ? "ادمین *$admin* یک کاربر را ویرایش کرد" : "یک کاربر ویرایش شد") . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . ' ' . $data->family . PHP_EOL;
                $msg .= " 📧 " . "ایمیل: " . PHP_EOL;
                $msg .= $data->email . PHP_EOL;
                $msg .= " ⚙️ " . "نام کاربری" . PHP_EOL;
                $msg .= $data->username . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس" . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                break;

            case 'player_edited':
                $msg .= " 🟧 " . ($admin ? "ادمین *$admin* یک بازیکن را ویرایش کرد" : "یک بازیکن ویرایش شد") . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . ' ' . $data->family . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🧬 " . "جنسیت: " . ($data->is_man ? 'مرد' : 'زن') . PHP_EOL;
                $msg .= " 📅 " . "تاریخ تولد: " . Jalalian::fromDateTime($data->born_at)->format('%Y/%m/%d') . PHP_EOL;
                $msg .= " 📏 " . "قد: " . $data->height . PHP_EOL;
                $msg .= " ⚓ " . "وزن: " . $data->weight . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . Sport::firstOrNew(['id' => $data->sport_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'coach_edited':
                $msg .= " 🟨 " . ($admin ? "ادمین *$admin* یک مربی را ویرایش کرد" : "یک مربی ویرایش شد") . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . ' ' . $data->family . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🧬 " . "جنسیت: " . ($data->is_man ? 'مرد' : 'زن') . PHP_EOL;
                $msg .= " 📅 " . "تاریخ تولد: " . Jalalian::fromDateTime($data->born_at)->format('%Y/%m/%d') . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . Sport::firstOrNew(['id' => $data->sport_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'club_edited':
                $msg .= " 🟩 " . ($admin ? "ادمین *$admin* یک باشگاه را ویرایش کرد" : "یک باشگاه ویرایش شد") . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " ⭐ " . "رشته ورزشی: " . implode(', ', collect(json_decode($data->times))->map(function ($el) {
                        return Sport::firstOrNew(['id' => $el->id])->name;
                    })->toArray()) . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "آدرس: " . $data->address . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'shop_edited':
                $msg .= " 🟦 " . ($admin ? "ادمین *$admin* یک فروشگاه را ویرایش کرد" : "یک فروشگاه ویرایش شد") . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $data->phone . PHP_EOL;
                $msg .= " 🚩 " . "استان: " . Province::firstOrNew(['id' => $data->province_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "شهر: " . County::firstOrNew(['id' => $data->county_id])->name . PHP_EOL;
                $msg .= " 🚩 " . "آدرس: " . $data->address . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

            case 'product_edited':
                $shop = \App\Models\Shop::firstOrNew(['id' => $data->shop_id]);
                $msg .= " 🟪 " . ($admin ? "ادمین *$admin* یک محصول را ویرایش کرد" : "یک محصول ویرایش شد") . PHP_EOL;
                $msg .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $msg .= " 🆔 " . "شناسه: " . $data->id . PHP_EOL;
                $msg .= " 👤 " . "نام: " . PHP_EOL;
                $msg .= $data->name . PHP_EOL;
                $msg .= " 📈 " . "قیمت اصلی: " . PHP_EOL;
                $msg .= $data->price . PHP_EOL;
                $msg .= " 📉 " . "قیمت با تخفیف: " . PHP_EOL;
                $msg .= $data->discount_price . PHP_EOL;
                $msg .= " 📊 " . "تعداد: " . PHP_EOL;
                $msg .= $data->count . PHP_EOL;
                $msg .= " 🚩 " . "دسته بندی: " . Sport::firstOrNew(['id' => $data->group_id])->name . PHP_EOL;
                $msg .= " 🛒 " . "فروشگاه: " . PHP_EOL;
                $msg .= $shop->name . PHP_EOL;
                $msg .= " 📱 " . "شماره تماس: " . PHP_EOL;
                $msg .= $shop->phone . PHP_EOL;
                $msg .= " 📃 " . "توضیحات: " . $data->description . PHP_EOL;

                break;

        }
        self::sendMessage($to, $msg, null);

//        } catch (\Exception $e) {
//            self::sendMessage($to, $e->getMessage(), null);
//
//        }
    }
}
