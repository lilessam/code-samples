<?php

use App\Ebay\Ebay;

if (!function_exists('ebay')) {
    /**
     * Return a new instance of Ebay service.
     *
     * @return \App\Ebay\Ebay
     */
    function ebay()
    {
        return app(Ebay::class);
    }
}
