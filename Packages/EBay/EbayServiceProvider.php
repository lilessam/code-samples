<?php

namespace App\Ebay;

use Illuminate\Support\ServiceProvider;

class EbayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Ebay::class, function () {
            return new Ebay($this->app);
        });

        $this->mergeConfigFrom(
            __DIR__ . '/config/ebay.php',
            'ebay'
        );
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
