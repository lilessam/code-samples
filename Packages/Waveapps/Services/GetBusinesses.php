<?php

namespace App\Waveapps\Services;

use App\Waveapps\Contracts\Service;
use function GuzzleHttp\json_decode;

class GetBusinesses implements Service
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
            CURLOPT_URL => 'https://gql.waveapps.com/graphql/public',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{ "query": "query { businesses { edges { node { id name } } } }" }',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer qQMvAFeEU6A9t8ReE3agRAA0E719Be',
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['error' => true];
        } else {
            return ['error' => false, 'response' => json_decode($response)];
        }
    }
}
