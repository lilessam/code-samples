<?php

namespace App\Waveapps;

use App\Waveapps\Services\GetUser;
use App\Waveapps\Services\FindCustomer;
use App\Waveapps\Services\GetCustomers;
use App\Waveapps\Services\GetBusinesses;
use App\Waveapps\Services\CreateCustomer;

class Waveapps
{
    /**
    * The services of Waveapps supported except authentication.
    *
    * @var array
    */
    public $services = [
        'getUser' => GetUser::class,
        'getBusinesses' => GetBusinesses::class,
        'getCustomers' => GetCustomers::class,
        'findCustomer' => FindCustomer::class,
        'createCustomer' => CreateCustomer::class
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
     * Dynamically call the service instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, array_keys($this->services))) {
            return (new $this->services[$method])->execute(...$parameters);
        }

        return $this;
    }
}
