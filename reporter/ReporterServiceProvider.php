<?php

namespace Reporter;

use Illuminate\Support\ServiceProvider;

class ReporterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(Generator::class, function () {
            return new Generator($this->app);
        });
    }
}
