<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use PHPUnit\TextUI\Help;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        $this->app->bind('path.public', function () {
//            return base_path() . '/../public_html';
//        });

        Paginator::useBootstrap();
        Validator::extend('base64_image', function ($attribute, $value, $parameters, $validator) {
            return validate_base64($value, ['png', 'jpg', 'jpeg']);
        });
        Validator::extend('base64_size', function ($attribute, $value, $parameters, $validator) {
            return strlen(base64_decode($value)) / 1024 < $parameters[0];

        });
        Schema::defaultStringLength(191);

        Relation::morphMap(\Helper::$morphsMap);
//        Relation::enforceMorphMap(\Helper::$morphsMap);
    }
}
