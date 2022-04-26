<?php

namespace App\Ebay\Services\Auth;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\Trading\Services;
use DTS\eBaySDK\Trading\Types\GetSessionIDRequestType;

class Authentication implements Service
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

        // Generate the SessionID using getSessionID like so =>
        $getsessionid = $service->getSessionID(
                new GetSessionIDRequestType([
                    'RuName' => config('ebay.authentication.ruName')
                ])
        );
        // then store it in a SESSION :
        \Session::put('ebay_auth_id', $getsessionid->SessionID);

        // Finally when you get redirected after clicking 'Agree' in eBay's authorization page you can fetch the Token with this simple way :
        
        //return 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?SignIn&runame=' . config('ebay.authentication.ruName') . '&SessID=' . $getsessionid->SessionID;

        return 'https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&runame=' . config('ebay.authentication.ruName') . '&SessID=' . $getsessionid->SessionID;
    }
}
