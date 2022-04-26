<?php

use App\Waveapps\Waveapps;

if (!function_exists('waveapps')) {
    /**
     * Return a new instance of Waveapps service.
     *
     * @return \App\Wavapps\Waveapps
     */
    function waveapps()
    {
        return app(Waveapps::class);
    }
}
