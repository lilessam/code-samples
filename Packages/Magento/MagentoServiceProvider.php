<?php

namespace App\Magento;

use Illuminate\Support\ServiceProvider;

class MagentoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Magento::class, function () {
            return new Magento($this->app);
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
