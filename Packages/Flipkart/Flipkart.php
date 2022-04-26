<?php

namespace App\Flipkart;

use App\Flipkart\Services\GetOrders;
use App\Flipkart\Services\GetProducts;

class Flipkart
{
    /**
    * The services of Flipkart supported.
    *
    * @var array
    */
    public $services = [
        'getOrders' => GetOrders::class,
        'getProducts' => GetProducts::class
    ];

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The access token array.
     *
     * @var arrry
     */
    protected $token;

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
     * Set the access token array.
     *
     * @param array $token
     * @return self
     */
    public function auth($token)
    {
        $this->token = $token;

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
            return (new $this->services[$method])->execute($this->token, ...$parameters);
        }

        return $this;
    }
}
