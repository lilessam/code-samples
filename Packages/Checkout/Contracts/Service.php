<?php

namespace App\Checkout\Contracts;

interface Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args);
}
