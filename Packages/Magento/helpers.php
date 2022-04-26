<?php

use App\Magento\Magento;

if (!function_exists('magento')) {
    /**
     * Create a new Magento service.
     *
     * @return \App\Magento\Magento
     */
    function magento()
    {
        return app(Magento::class);
    }
}
