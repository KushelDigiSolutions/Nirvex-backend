<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            // Example validation logic for phone numbers
            return preg_match('/^\+?[0-9]{10,15}$/', $value);
        }, 'The :attribute field must be a valid phone number.'); 
    }
}
