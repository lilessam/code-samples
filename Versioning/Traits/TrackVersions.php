<?php

namespace App\Versioning\Traits;

use App\Models\System\Version;

trait TrackVersions
{
    /**
     * Add a new version whenever there's an update
     *
     * @return void
     */
    public static function bootTrackVersions()
    {
        static::updated(function ($model) {
            $original = $model->getOriginal();
            $reflect = new \ReflectionClass($model);
            config('versioning.model')::add(strtolower($reflect->getShortName()), $model->id, $original);
        });
    }
}
