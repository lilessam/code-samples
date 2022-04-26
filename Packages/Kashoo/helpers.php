<?php

use App\Kashoo\Kashoo;

if (!function_exists('kashoo')) {
    /**
     * Return a new instance of Kashoo service.
     *
     * @return \App\Kashoo\Kashoo
     */
    function kashoo()
    {
        return app(Kashoo::class);
    }
}
