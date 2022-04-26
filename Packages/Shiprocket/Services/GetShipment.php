<?php

namespace App\Shiprocket\Services;

use App\Shiprocket\Contracts\Service;

class GetShipment implements Service
{
    /**
     * Base URL of the API.
     *
     * @var string
     */
    public $url = 'https://apiv2.shiprocket.in/v1/external/';

    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $token = $args[0]['token'];

        $url = $this->url . 'shipments/' . $args[1];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization:Bearer ' . $token,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
