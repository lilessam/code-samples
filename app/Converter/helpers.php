<?php

use App\Converter\Converter;

if (!function_exists('converter')) {
    /**
     * Create an instance of the converter.
     *
     * @return \App\Converter\Converter
     */
    function converter()
    {
        return app(Converter::class);
    }
}
