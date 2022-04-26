<?php

namespace App\Logger\Traits;

use Auth;
use App\Logger\Logger;

trait Loggable
{
    /**
     * Returning an array of eloquent models events
     * that will be auto logged.
     *
     * @return array
     */
    abstract public static function loggable() : array;

    /**
     * Fire log event whenever an event of the trackable
     * events are executed.
     *
     * @return void
     */
    public static function bootLoggable()
    {
        if (!(strpos(php_sapi_name(), 'cli') !== false)) {
            foreach (static::loggable() as $event) {
                static::$event(function ($model) use ($event) {
                    $reflect = new \ReflectionClass($model);
                    app(Logger::class)->user_id(Auth::user()->id)->entity($reflect->getShortName())
                    ->entity_id($model->id)->action(substr($event, 0, -1))->log();
                });
            }
        }
    }
}
