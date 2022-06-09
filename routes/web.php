<?php

use App\Models\County;
use App\Models\Coupon;
use App\Models\Setting;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Faker\Factory as Faker;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test', function () {
//    Artisan::call('db:seed');
//    Artisan::call('server:optimize');

});

Route::get('event/search', [App\Http\Controllers\EventController::class, 'search'])->name('event.search');
Route::get('club/search', [App\Http\Controllers\ClubController::class, 'search'])->name('club.search');
Route::get('coach/search', [App\Http\Controllers\CoachController::class, 'search'])->name('coach.search');
Route::get('player/search', [App\Http\Controllers\PlayerController::class, 'search'])->name('player.search');
Route::get('shop/search', [App\Http\Controllers\ShopController::class, 'search'])->name('shop.search');
Route::get('product/search', [App\Http\Controllers\ProductController::class, 'search'])->name('product.search');
Route::get('blog/search', [App\Http\Controllers\BlogController::class, 'search'])->name('blog.search');
Route::get('table/search', [App\Http\Controllers\TableController::class, 'search'])->name('table.search');


Route::get('/', function () {
    return view('home');
})->name('/');

Route::get('events', function () {
    return view('events');
})->name('events.view');
Route::get('event/{id}', function ($id) {
    return view('event', ['id' => $id]);
});

Route::get('players', function () {
    return view('players');
})->name('players.view');
Route::get('player/{id}', function ($id) {
    return view('player', ['id' => $id]);
});

Route::get('coaches', function () {
    return view('coaches');
})->name('coaches.view');
Route::get('coach/{id}', function ($id) {
    return view('coach', ['id' => $id]);
});

Route::get('clubs', function () {
    return view('clubs');
})->name('clubs.view');
Route::get('club/{id}', function ($id) {
    return view('club', ['id' => $id]);
});

Route::get('shops', function () {
    return view('shops');
})->name('shops.view');
Route::get('shop/{id}', function ($id) {
    return view('shop', ['id' => $id]);
});

Route::get('products', function () {
    return view('products');
})->name('products.view');
Route::get('product/{id}', function ($id) {
    return view('product', ['id' => $id]);
});

Route::get('table/{id}/{title}', function ($id, $title) {
    return view('table', ['id' => $id]);
})->name('table.view');
Route::get('tables', function () {
    return view('tables');
})->name('tables.view');

Route::get('blog/{id}/{title}', function ($id, $title) {
    return view('blog', ['id' => $id]);
})->name('blog.view');
Route::get('blogs', function () {
    return view('blogs');
})->name('blogs.view');


Auth::routes();
Route::get('/verifyemail/{token}/{from}', [App\Http\Controllers\Auth\RegisterController::class, 'verifyEmail'])->name('verification.mail');
Route::get('/resendemail/{token}', [App\Http\Controllers\Auth\RegisterController::class, 'resendEmail'])->name('resend.mail');

Route::get('register-player', function () {
    return view('auth.player-create');
});
Route::get('register-coach', function () {
    return view('auth.coach-create');
});
Route::get('register-club', function () {
    return view('auth.club-create');
});
Route::get('register-shop', function () {
    return view('auth.shop-create');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth'])->prefix('panel')->group(function () {


    Route::get('', function () {

        return view('panel');
    })->name('panel.view');


//    Route::get('my-orders', [App\Http\Controllers\OrderController::class, 'groups'])->name('panel.my-orders');

    Route::prefix('{route?}')->group(function ($route) {


        Route::get('', function ($route) {

            return view('panel');
        })->name('panel.panel');


        Route::prefix('')->middleware('can:createItem,' . App\Models\User::class . ',' . 'route' . ',' . false)->group(function ($route1) {
            Route::get('', function ($route1) {
                return view('panel', ['param' => $route1]);
            });
        });


        Route::prefix('create')->middleware('can:createItem,' . App\Models\User::class . ',' . 'route' . ',' . false)->group(function ($route1) {
            Route::get('{route1?}', function ($route1) {
                return view('panel', ['param' => $route1]);
            });
        });


        Route::prefix('edit')->middleware('can:ownItem,' . App\Models\User::class . ',' . 'route' . ',' . false . ',' . 'route1')->group(function ($route2) {


            Route::get('{route1?}', function ($route, $route1) {

                return view('panel', ['param' => $route1]);
            })->name('edit.view');


//
//            Route::get('{route3?}/{route4?}', function ($route3, $route4, $route5) {
//
//                return view('panel', ['param' => $route5]);
//            })->name('panel.view');
////
//

        });
    });
});

Route::post('player/create', [App\Http\Controllers\PlayerController::class, 'create'])->name('player.create');
Route::post('coach/create', [App\Http\Controllers\CoachController::class, 'create'])->name('coach.create');
Route::post('club/create', [App\Http\Controllers\ClubController::class, 'create'])->name('club.create');
Route::post('shop/create', [App\Http\Controllers\ShopController::class, 'create'])->name('shop.create');
Route::middleware(['auth'])->group(function () {

    Route::post('event/create', [App\Http\Controllers\EventController::class, 'create'])->name('event.create');
    Route::post('coupon/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupon.create');
    Route::post('product/create', [App\Http\Controllers\ProductController::class, 'create'])->name('product.create');
    Route::post('blog/create', [App\Http\Controllers\BlogController::class, 'create'])->name('blog.create');
    Route::post('table/create', [App\Http\Controllers\TableController::class, 'create'])->name('table.create');
    Route::post('system-setting/create', [App\Http\Controllers\SettingController::class, 'create'])->name('system-setting.create');

    Route::post('event/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('event.edit');
    Route::post('club/edit', [App\Http\Controllers\ClubController::class, 'edit'])->name('club.edit');
    Route::post('coach/edit', [App\Http\Controllers\CoachController::class, 'edit'])->name('coach.edit');
    Route::post('player/edit', [App\Http\Controllers\PlayerController::class, 'edit'])->name('player.edit');
    Route::post('shop/edit', [App\Http\Controllers\ShopController::class, 'edit'])->name('shop.edit');
    Route::post('user/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::post('product/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('product.edit');
    Route::post('blog/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blog.edit');
    Route::post('table/edit', [App\Http\Controllers\TableController::class, 'edit'])->name('table.edit');
    Route::post('system-setting/edit', [App\Http\Controllers\SettingController::class, 'edit'])->name('system-setting.edit');

    Route::post('event/remove', [App\Http\Controllers\EventController::class, 'remove'])->name('event.remove');
    Route::post('coupon/remove', [App\Http\Controllers\CouponController::class, 'remove'])->name('coupon.remove');
    Route::post('club/remove', [App\Http\Controllers\ClubController::class, 'remove'])->name('club.remove');
    Route::post('coach/remove', [App\Http\Controllers\CoachController::class, 'remove'])->name('coach.remove');
    Route::post('player/remove', [App\Http\Controllers\PlayerController::class, 'remove'])->name('player.remove');
    Route::post('shop/remove', [App\Http\Controllers\ShopController::class, 'remove'])->name('shop.remove');
    Route::post('product/remove', [App\Http\Controllers\ProductController::class, 'remove'])->name('product.remove');
    Route::post('blog/remove', [App\Http\Controllers\BlogController::class, 'remove'])->name('blog.remove');
    Route::post('table/remove', [App\Http\Controllers\TableController::class, 'remove'])->name('table.remove');
    Route::post('system-setting/remove', [App\Http\Controllers\SettingController::class, 'remove'])->name('system-setting.remove');
    Route::post('user/remove', [App\Http\Controllers\UserController::class, 'remove'])->name('user.remove');
    Route::get('system-logs/search', [App\Http\Controllers\SettingController::class, 'log'])->name('system-logs.search');

    Route::post('ref/tasvie', [App\Http\Controllers\RefController::class, 'tasvie'])->name('ref.tasvie');
    Route::get('ref/search', [App\Http\Controllers\RefController::class, 'search'])->name('ref.search');

    Route::get('user/search', [App\Http\Controllers\UserController::class, 'search'])->name('user.search');

});
Route::get('latest', [App\Http\Controllers\Controller::class, 'latest'])->name('latest');


Route::get('payment', [App\Http\Controllers\PaymentController::class, 'confirmPay'])->name('payment');

Route::post('payment/create', [App\Http\Controllers\PaymentController::class, 'makePay'])->name('payment.create');

Route::post('coupon/calculate', [App\Http\Controllers\CouponController::class, 'calculate'])->name('coupon.calculate');

