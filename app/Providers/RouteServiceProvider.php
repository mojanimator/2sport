<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Cache\Repository as Cache;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    const ROOT = '/';
    const HOME = '/';
    const PANEL = '/panel';
    const LOGIN = '/login';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for ('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for ('sms_limit', function (Request $request) {

            $key = 'sms' . optional($request->user())->id ?: $request->ip();

            return Limit::perHour(10)->by($key)->response(function ($request, $headers) {
                $seconds = $headers['Retry-After'] ? intval($headers['Retry-After'] / 60) : 60;

                return response()->json(['errors' => ['phone' => ["در خواست بیش از حد مجاز. لطفا $seconds دقیقه دیگر اقدام کنید"]]], 429);
            });
        });


    }
}
