<?php

namespace App\Ebay\Services\OAuth;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest;

class GetOAuthToken implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $service = new OAuthService(config('ebay.authentication'));
        $api = $service->getUserToken(new GetUserTokenRestRequest([
            'code' => $args[0]
        ]));

        return [
            'state' => $args[1],
            'code' => $args[0],
            'statusCode' => $api->getStatusCode(),
            'accessToken' => $api->access_token,
            'tokenType' => $api->token_type,
            'expiresIn' => $api->expires_in,
            'refreshToken' => $api->refresh_token,
            'error' => $api->error,
            'errorDescription' => $api->error_description
        ];
    }
}
