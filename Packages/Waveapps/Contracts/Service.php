<?php

namespace App\Waveapps\Contracts;

interface Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args);
}
