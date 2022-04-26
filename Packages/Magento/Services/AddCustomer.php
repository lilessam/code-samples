<?php

namespace App\Magento\Services;

use App\Magento\Contracts\Service;

class AddCustomer implements Service
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
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['customer' => $args[2]]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
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
            return ['error' => false, 'response' => $response];
        }
    }
}
