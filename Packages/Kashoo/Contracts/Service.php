<?php

namespace App\Kashoo\Contracts;

interface Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args);
}
