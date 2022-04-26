<?php

namespace App\Checkout;

use Twocheckout;
use App\Checkout\Services\Charge;

class Checkout
{
    /**
     * The Checkout client instance.
     *
     * @var \App\Checkout\Lib\Checkout
     */
    protected $client;

    /**
    * The services of 2checkout supported except authentication.
    *
    * @var array
    */
    public $services = [
        'charge' => Charge::class
    ];

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new manager instance.
     * d
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Authenticate the user.
     *
     * @param array ...$args
     * @return void
     */
    public function auth(...$args)
    {
        Twocheckout::privateKey($args[0]);
        Twocheckout::sellerId($args[1]);
        if (isset($args[2])) {
            Twocheckout::sandbox($args[2]);
        } else {
            Twocheckout::sandbox(false);
        }
        return $this;
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
            //

            return (new $this->services[$method])->execute($this->client, ...$parameters);
        }

        return $this;
    }
}
