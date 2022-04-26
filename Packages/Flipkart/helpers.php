<?php

use App\Flipkart\Flipkart;

if (!function_exists('flipkart')) {
    /**
     * Return a new instance of Flipkart service.
     *
     * @return \App\Flipkart\Flipkart
     */
    function flipkart()
    {
        return app(Flipkart::class);
    }
}
