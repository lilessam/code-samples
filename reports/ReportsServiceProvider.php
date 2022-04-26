<?php

namespace Reports;

use Illuminate\Support\ServiceProvider;
use HaydenPierce\ClassFinder\ClassFinder;

class ReportsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $classes = collect(ClassFinder::getClassesInNamespace('Reports\Reports', ClassFinder::RECURSIVE_MODE))->filter(function ($class) {
            return $class != 'Reports\ReportsServiceProvider' && $class != 'Reports\Reports\Report';
        });

        foreach ($classes as $class) {
            $class_namespace = explode('\\', $class)[3];
            $this->loadViewsFrom(__DIR__ . '/Reports/' . $class_namespace, $class_namespace);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(Report::class, function () {
            return new Report($this->app);
        });
    }
}
