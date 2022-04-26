<?php

namespace App\Flipkart;

use Illuminate\Support\ServiceProvider;

class FlipkartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Flipkart::class, function () {
            return new Flipkart($this->app);
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
