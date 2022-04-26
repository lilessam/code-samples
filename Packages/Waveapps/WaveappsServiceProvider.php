<?php

namespace App\Waveapps;

use Illuminate\Support\ServiceProvider;

class WaveappsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';

        $this->app->singleton(Waveapps::class, function () {
            return new Waveapps($this->app);
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
