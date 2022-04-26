<?php

namespace App\Converter;

use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__ . '/helpers.php';
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/converter.php',
            'converter'
        );

        $this->app->singleton(Converter::class, function () {
            return new Converter($this->app);
        });
    }
}
