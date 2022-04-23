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

Route::get('club/search', [App\Http\Controllers\ClubController::class, 'search'])->name('club.search');
Route::get('coach/search', [App\Http\Controllers\CoachController::class, 'search'])->name('coach.search');
Route::get('player/search', [App\Http\Controllers\PlayerController::class, 'search'])->name('player.search');
Route::get('shop/search', [App\Http\Controllers\ShopController::class, 'search'])->name('shop.search');
Route::get('product/search', [App\Http\Controllers\ProductController::class, 'search'])->name('product.search');
Route::get('blog/search', [App\Http\Controllers\BlogController::class, 'search'])->name('blog.search');
Route::get('table/search', [App\Http\Controllers\TableController::class, 'search'])->name('table.search');

Route::get('latest', [App\Http\Controllers\Controller::class, 'latest'])->name('latest');
Route::get('settings', [App\Http\Controllers\Controller::class, 'settings'])->name('settings');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('getactivationcode', function () {

    return (new SMS())->sendActivationSMS(request()->phone);
});