<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        // In a service provider boot() method:
        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Za-z0-9_]{3,20}$/', $value);
        });

        Validator::replacer('username', function ($message, $attribute, $rule, $parameters) {
            return 'The ' . $attribute . ' may only contain letters, numbers, and underscores, and must be 3–20 characters long.';
        });
    }
}
