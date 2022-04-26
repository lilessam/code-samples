<?php

namespace App\Flipkart\Services;

use App\Flipkart\Contracts\Service;

class GetProducts implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.flipkart.net/sellers/listings/v3/' . implode(',', $args[1]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'Accept: */*',
                'Authorization: Bearer ' . $args[0]['access_token'],
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Host: api.flipkart.net',
                'accept-encoding: gzip, deflate',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $json_decode = json_decode($response);

        return $json_decode;
    }
}
