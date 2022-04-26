<?php

namespace App\Checkout\Services;

use Twocheckout_Error;
use Twocheckout_Charge;
use App\Checkout\Contracts\Service;

class Charge implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        try {
            $charge = Twocheckout_Charge::auth([
                "merchantOrderId" => '123',
                "token"      => $args[0],
                "currency"   => 'USD',
                "total"      => $args[1],
                "billingAddr" => $args[2]
            ]);

            if ($charge['response']['responseCode'] == 'APPROVED') {
                return true;
            } else {
                return false;
            }
        } catch (Twocheckout_Error $e) {
            return false;
        }
    }
}