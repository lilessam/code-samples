<?php

namespace App\Ebay\Services\OAuth;

use App\Ebay\Contracts\Service;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest;

class RefreshOAuthToken implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $service = new OAuthService(config('ebay.authentication'));
        $request = new RefreshUserTokenRestRequest();
        $request->refresh_token = $args[0];
        $request->scope = config('ebay.scopes');
        $response = $service->refreshUserToken($request);
        if ($response->getStatusCode() !== 200) {
            return [
                $response->error => $response->error_description
            ];
        } else {
            $token = [
                'accessToken' => $response->access_token,
                'tokenType' => $response->token_type,
                'expiresIn' => $response->expires_in,
                'refreshToken' => $response->refresh_token
            ];

            if ($token['refreshToken'] == null) {
                return;
            }

            // Update token
            $user = \App\User::find(1);
            $user->ebay_token = $token;
            $user->save();

            return $token;
        }
    }
}
