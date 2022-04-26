<?php

namespace App\Waveapps\Services;

use App\Waveapps\Contracts\Service;

class FindCustomer implements Service
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
            CURLOPT_POSTFIELDS => '{ "query": " query { business(id: \"' . $args[1] . '\") { customer(id: \"' . $args[2] . '\") { id name firstName lastName email mobile phone fax tollFree website createdAt modifiedAt } } } " }',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[0],
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
