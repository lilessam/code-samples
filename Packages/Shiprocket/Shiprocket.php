<?php

namespace App\Shiprocket;

use App\Shiprocket\Services\CreateOrder;
use App\Shiprocket\Services\GetShipment;
use App\Shiprocket\Services\ForwardShipment;
use App\Shiprocket\Services\GETAWBTrackingDetails;
use App\Shiprocket\Exceptions\AuthenticationFailed;
use App\Shiprocket\Services\GetShipmentTrackingDetails;

class Shiprocket
{
    /**
     * Base URL of the API.
     *
     * @var string
     */
    public $url = 'https://apiv2.shiprocket.in/v1/external/';

    /**
     * The authentication successful response.
     *
     * @var array
     */
    private $authenticationResponse;

    /**
    * The services of Shiprocket supported except authentication.
    *
    * @var array
    */
    public $services = [
        'getShipment' => GetShipment::class,
        'createOrder' => CreateOrder::class,
        'forwardShipment' => ForwardShipment::class,
        'getAWBTrackingDetails' => GETAWBTrackingDetails::class,
        'getShipmentTrackingDetails' => GetShipmentTrackingDetails::class,
    ];

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Authenticate with the API.
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function auth($username, $password)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_TIMEOUT, 300);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url . 'auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "email": "' . $username . '",
                "password": "' . $password . '"
            }',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        if (isset($response['id'])) {
            $this->authenticationResponse = $response;

            return $this;
        } else {
            throw new AuthenticationFailed;
        }
    }

    /**
     * Dynamically call the service instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, array_keys($this->services))) {
            return (new $this->services[$method])->execute($this->authenticationResponse, ...$parameters);
        }

        return $this;
    }
}
