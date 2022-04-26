<?php

namespace App\Kashoo;

use Illuminate\Support\ServiceProvider;

class KashooServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Kashoo::class, function () {
            return new Kashoo($this->app);
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
