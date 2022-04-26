<?php

namespace App\Kashoo\Services;
use App\Kashoo\Lib\Kashoo as Lib;

use App\Kashoo\Contracts\Service;

class CreateInvoice implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
    	$kashoo = new Lib();
  
        $kashoo->createInvoice($args[1], $args[2]);
    	
    	$this->client = $kashoo;

    	return $this;
       
    }
}
