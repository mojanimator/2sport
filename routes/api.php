<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/bot/getupdates', [\App\Http\Controllers\BotController::class, 'getupdates']);
Route::post('/bot/sendmessage', [\App\Http\Controllers\BotController::class, 'sendmessage']);
Route::get('/bot/getme', [\App\Http\Controllers\BotController::class, 'myInfo']);


Route::middleware('auth:api')->group(function () {

    Route::get('user/get', [App\Http\Controllers\UserController::class, 'get'])->name('user.get');

    Route::post('user/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::post('club/edit', [App\Http\Controllers\ClubController::class, 'edit'])->name('club.edit');
    Route::post('coach/edit', [App\Http\Controllers\CoachController::class, 'edit'])->name('coach.edit');
    Route::post('player/edit', [App\Http\Controllers\PlayerController::class, 'edit'])->name('player.edit');
    Route::post('shop/edit', [App\Http\Controllers\ShopController::class, 'edit'])->name('shop.edit');
    Route::post('product/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('product.edit');
    Route::post('blog/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blog.edit');

    Route::post('player/create', [App\Http\Controllers\PlayerController::class, 'create'])->name('player.create');
    Route::post('coach/create', [App\Http\Controllers\CoachController::class, 'create'])->name('coach.create');
    Route::post('club/create', [App\Http\Controllers\ClubController::class, 'create'])->name('club.create');
    Route::post('shop/create', [App\Http\Controllers\ShopController::class, 'create'])->name('shop.create');
    Route::post('product/create', [App\Http\Controllers\ProductController::class, 'create'])->name('product.create');

    Route::post('product/remove', [App\Http\Controllers\ProductController::class, 'remove'])->name('product.remove');

    Route::get('blog/find', [App\Http\Controllers\BlogController::class, 'find'])->name('blog.find');

    Route::post('payment/create', [App\Http\Controllers\PaymentController::class, 'IAPPurchase'])->name('payment.create');
    Route::get('tournament/search', [App\Http\Controllers\TournamentController::class, 'search'])->name('tournament.search');
    Route::get('payment', [App\Http\Controllers\PaymentController::class, 'confirmPay'])->name('payment');

});
Route::post('coupon/calculate', [App\Http\Controllers\CouponController::class, 'calculate'])->name('coupon.calculate');


Route::get('login', [App\Http\Controllers\UserController::class, 'login'])->name('user.login');

Route::get('table/search', [App\Http\Controllers\TableController::class, 'search'])->name('table.search');

Route::get('club/search', [App\Http\Controllers\ClubController::class, 'search'])->name('club.search');
Route::get('coach/search', [App\Http\Controllers\CoachController::class, 'search'])->name('coach.search');
Route::get('player/search', [App\Http\Controllers\PlayerController::class, 'search'])->name('player.search');
Route::get('shop/search', [App\Http\Controllers\ShopController::class, 'search'])->name('shop.search');
Route::get('product/search', [App\Http\Controllers\ProductController::class, 'search'])->name('product.search');
Route::get('blog/search', [App\Http\Controllers\BlogController::class, 'search'])->name('blog.search');
Route::get('table/search', [App\Http\Controllers\TableController::class, 'search'])->name('table.search');
Route::get('event/search', [App\Http\Controllers\EventController::class, 'search'])->name('event.search');
Route::get('video/search', [App\Http\Controllers\VideoController::class, 'search'])->name('video.search');

Route::get('latest', [App\Http\Controllers\Controller::class, 'latest'])->name('latest');
Route::get('settings', [App\Http\Controllers\Controller::class, 'settings'])->name('settings');

Route::post('error', [App\Http\Controllers\Controller::class, 'sendError'])->name('error.log');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('getactivationcode', function () {

    return (new SMS())->sendActivationSMS(request()->phone);
})->middleware('throttle:sms_limit');

Route::post('dabel_telegram', function (Request $request) {

    $method = $request->cmnd;
    $datas = $request->all();

    $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    $res = curl_exec($ch);

//        echo $res;
    $res = json_decode($res);
    if (curl_error($ch)) {

        curl_close($ch);
        return null;
    } else {
        curl_close($ch);
        return $res;
    }

});
Route::post('dabelchin_telegram', function (Request $request) {

    $method = $request->cmnd;
    $datas = $request->all();

    $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN_DABELCHIN', 'YOUR-BOT-TOKEN') . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    $res = curl_exec($ch);

//        echo $res;
    $res = json_decode($res);
    if (curl_error($ch)) {

        curl_close($ch);
        return null;
    } else {
        curl_close($ch);
        return $res;
    }

});
function sendToDabelAdl($datas=[]){
    $url = "https://dabeladl.com/api/2sport";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    $res = curl_exec($ch);
    if (curl_error($ch)) {

        curl_close($ch);
        return null;
    } else {
        curl_close($ch);
        return $res;
    }
}
