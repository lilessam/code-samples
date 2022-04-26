<?php

namespace App\Authorize\Contracts;

interface Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args);
}
