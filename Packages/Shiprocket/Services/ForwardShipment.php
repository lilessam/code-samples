<?php

namespace App\Shiprocket\Services;

use App\Shiprocket\Contracts\Service;
use function GuzzleHttp\json_encode;

class ForwardShipment implements Service
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

        $url = $this->url . 'shipments/create/forward-shipment';

        $curl = curl_init();
        $passedData = json_encode($args[1]);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $passedData);
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
