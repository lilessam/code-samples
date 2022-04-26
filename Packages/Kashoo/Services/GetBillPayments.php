<?php

namespace App\Kashoo\Services;

use App\Kashoo\Contracts\Service;

class GetBillPayments implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        return $args[0]->listBillPayments();
    }
}
