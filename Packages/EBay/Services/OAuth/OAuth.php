<?php

namespace App\Ebay\Services\OAuth;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\OAuth\Services\OAuthService;

class OAuth implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $service = new OAuthService(config('ebay.authentication'));
        $state = uniqid();
        $url = $service->redirectUrlForUser([
            'state' => $state,
            'scope' => config('ebay.scopes')
        ]);

        return $url;
    }
}
