<?php

namespace App\Ebay\Services;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\Inventory\Services\InventoryService;
use DTS\eBaySDK\Inventory\Types\GetInventoryItemRestRequest;

class GetInventory implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $service = new InventoryService([
            'authorization' => $args[0]
        ]);
        /**
         * Create the request object.
         */
        $request = new GetInventoryItemRestRequest();

        if (isset($args[1])) {
            foreach ($args[1] as $key => $value) {
                $request->$key = $value;
            }
        }

        /**
         * Send the request.
         */
        $response = $service->getInventoryItem($request);

        return $response;
    }
}
