<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Validator::extend('has_lowercase', function ($attribute, $value) {
        return preg_match('/[a-z]/', $value);
        });

        Validator::extend('has_uppercase', function ($attribute, $value) {
            return preg_match('/[A-Z]/', $value);
        });

        Validator::extend('has_digit', function ($attribute, $value) {
            return preg_match('/[0-9]/', $value);
        });

        Validator::extend('has_special', function ($attribute, $value) {
            return preg_match('/[@$!%*#?&]/', $value);
        });
    }
}
