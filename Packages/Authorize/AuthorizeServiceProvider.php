<?php

namespace App\Authorize;

use Illuminate\Support\ServiceProvider;

class AuthorizeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';
        $this->app->singleton(Authorize::class, function() {
            return new Authorize($this->app);
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
