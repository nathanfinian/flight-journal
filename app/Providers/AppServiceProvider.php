<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

        Blade::if('role', function (...$roles) {
            $user = Auth::user();

            if (! $user || ! $user->role) {
                return false;
            }

            return in_array($user->role->name, $roles);
        });
    }
}
