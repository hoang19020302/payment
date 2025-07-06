<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Hashing\ArgonHasher;
use Illuminate\Support\Facades\Hash;

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
        // The default password hasher
        Hash::extend('argon2id', function () {
            return new ArgonHasher();
        });

        Hash::swap(new \Illuminate\Hashing\HashManager($this->app));
    }
}
