<?php

namespace App\Shiprocket;

use App\Shiprocket\Shiprocket;
use Illuminate\Support\ServiceProvider;

class ShiprocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Shiprocket::class, function () {
            return new Shiprocket($this->app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
