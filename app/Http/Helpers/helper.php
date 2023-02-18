<?php


use App\Models\Setting;

require_once 'SMS.php';
require_once 'Telegram.php';
require_once 'NextPay.php';


class Helper
{

    static $PACKAGE_NAME = 'com.dabel.dabel_sport';

    static $APP_VERSION = 1;
    static $TELEGRAM_BOT_ID = '5049830226';
    static $TELEGRAM_GROUP_ID = -1001572506441;
    static $TELEGRAM_CHANNEL_ID = '@doublesport';
    static $admin_phone = "09018945844";
    static $logs = [72534783, /*225594412*/];
    static $initScore = 0;
    static $club_image_limit = 3;
    static $product_image_limit = 3;
    static $cropsRatio = ['license' => 0.75, 'video' => 0.75, 'profile' => 0.75, 'club' => 1.25, 'product' => 1.0, 'logo' => 1.0, 'tournament' => 1.25, 'videos' => 1.25,];
    static $eventStatus = ['لغو', 'تعویق', 'درحال برگزاری', 'نامشخص'];

    static $roles = [
        'us' => 'کاربر معمولی',
        'bl' => 'خبرنگار',
        'aa' => 'ادمین نمایندگی',
        'ag' => 'مالک نمایندگی',
        'ad' => 'ادمین کل',
        'go' => 'مالک کل',
    ];
    static $refMap = [
        1 => 'player',
        2 => 'coach',
        3 => 'club',
        4 => 'shop',


    ];
    static $typesMap = [
        'players' => 'pl',
        'coaches' => 'co',
        'clubs' => 'cl',
        'shops' => 'sh',
        'products' => 'pr',
        'blogs' => 'bl',
        'agencies' => 'ag',



    ];
    static $morphsMap = [
        'pl' => 'App\Models\Player',
        'co' => 'App\Models\Coach',
        'cl' => 'App\Models\Club',
        'sh' => 'App\Models\Shop',

    ];
    //from doc-types Table
    static $docsMap = [
        'profile' => 1,
        'license' => 2,
        'club' => 3,
        'product' => 4,
        'video' => 5,
        'logo' => 6,
        'blog' => 7,
        'tournament' => 8,
        'videos' => 9,

    ];
    static $categoryType = [
        'blog' => 1,
        'event' => 2,
        'videos' => 3,


    ];
    static $tableType = [
        'لیگ' => 1,
//        'کنداکتور' => 2,
        'تورنومنت' => 2,


    ];
    static $labelsMap = [
        'players' => 'بازیکن',
        'coaches' => 'مربی',
        'clubs' => 'باشگاه',
        'shops' => 'فروشگاه',
        'products' => 'محصول',
        'blogs' => 'خبر',
        'videos' => 'ویدیو',


    ];

}

function validate_base64($base64data, array $allowedMime)
{


    // strip out data uri scheme information (see RFC 2397)
    if (strpos($base64data, ';base64') !== false) {
        list(, $base64data) = explode(';', $base64data);
        list(, $base64data) = explode(',', $base64data);
    }

    // strict mode filters for non-base64 alphabet characters
    if (base64_decode($base64data, true) === false) {
        return false;
    }

    // decoding and then reeconding should not change the data
    if (base64_encode(base64_decode($base64data)) !== $base64data) {
        return false;
    }

    $binaryData = base64_decode($base64data);

    // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder

    $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
//    $tmpFile = tmpfile();
//    $path = stream_get_meta_data($tmpFile)['uri'];
    file_put_contents($tmpFile, $binaryData);

    // guard Against Invalid MimeType
    $allowedMime = array_flatten($allowedMime);

    // no allowedMimeTypes, then any type would be ok
    if (empty($allowedMime)) {
        return true;
    }

    // Check the MimeTypes
    $validation = Illuminate\Support\Facades\Validator::make(
        ['file' => new Illuminate\Http\File($tmpFile)],
        ['file' => 'mimes:' . implode(',', $allowedMime)]
    );

    $res = !$validation->fails();

    unlink($tmpFile);

    return $res;
}

function e2f($str)
{
    $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return str_replace($eng, $per, $str);
}

function f2e($str)
{
    $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return str_replace($per, $eng, $str);
}

function myLog($log)
{
    $path = dirname(__FILE__, 2) . 'debug.log';
    $path = storage_path('logs/debug.log');
    error_log(print_r($log, true) . PHP_EOL, 3, $path);

}

function mRequest($url, $type = 'GET', $params = [], $headers = [])
{
    if (!str_contains(request()->url(), '.ir'))
    return;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => '',
//        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $type,
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => $headers
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response != null ? json_decode($response) : null;
}
