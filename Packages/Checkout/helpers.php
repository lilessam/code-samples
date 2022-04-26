<?php

use App\Checkout\Checkout;

if (!function_exists('twocheckout')) {
    /**
     * Return a new instance of Checkout service.
     *
     * @return \App\Checkout\Checkout
     */
    function twocheckout()
    {
        return app(Checkout::class);
    }
}
