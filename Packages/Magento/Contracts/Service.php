<?php

namespace App\Magento\Contracts;

interface Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args);
}
