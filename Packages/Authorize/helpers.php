<?php

use App\Authorize\Authorize;

if (!function_exists('authorize')) {
    /**
     * Return a new instance of Authorize service.
     *
     * @return \App\Authorize\Authorize
     */
    function authorize()
    {
        return app(Authorize::class);
    }
}
