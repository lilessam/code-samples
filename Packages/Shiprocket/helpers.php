<?php

use App\Shiprocket\Shiprocket;

if (!function_exists('shiprocket')) {
    /**
     * Return a new instance of Shiprocket service.
     *
     * @return \App\Shiprocket\Shiprocket
     */
    function shiprocket()
    {
        return app(Shiprocket::class);
    }
}
