<?php

namespace App\Kashoo\Services;

use App\Kashoo\Contracts\Service;

class GetBills implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        return $args[0]->listBills();
    }
}
