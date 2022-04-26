<?php

namespace App\Ebay\Services\Auth;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\Trading\Services;
use DTS\eBaySDK\Trading\Types\FetchTokenRequestType;

class GetAuthToken implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $service = new Services\TradingService([
            'credentials' => config('ebay.authentication.credentials'),
            'sandbox' => false,
            'siteId' => \DTS\eBaySDK\Constants\SiteIds::US
        ]);
        $token = $service->fetchToken(
            new FetchTokenRequestType([
                'SessionID' => \Session::get('ebay_auth_id'),
            ])
        );

        return $token;
    }
}
