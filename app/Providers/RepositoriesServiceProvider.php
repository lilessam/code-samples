<?php

namespace App\Providers;

use App\Repositories\Accounts;
use App\Repositories\Matches;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Matches::class, function () {
            return new Matches;
        });

        $this->app->singleton(Accounts::class, function () {
            return new Accounts($this->app->make(Matches::class));
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
