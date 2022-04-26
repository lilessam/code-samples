<?php

namespace App\Ebay;

use App\Ebay\Services\GetInvoices;
use App\Ebay\Services\GetProducts;
use App\Ebay\Services\OAuth\OAuth;
use App\Ebay\Services\GetInventory;
use App\Ebay\Services\Auth\GetAuthToken;
use App\Ebay\Services\Auth\Authentication;
use App\Ebay\Services\OAuth\GetOAuthToken;
use App\Ebay\Services\OAuth\RefreshOAuthToken;

class Ebay
{
    /**
    * The services of EBay supported.
    *
    * @var array
    */
    public $services = [
        'generateAuthUri' => Authentication::class,
        'getAuthToken' => GetAuthToken::class,
        'refreshOAuthToken' => RefreshOAuthToken::class,
        'generateOAuthUri' => OAuth::class,
        'getOAuthToken' => GetOAuthToken::class,
        'getInventory' => GetInventory::class,
        'getProducts' => GetProducts::class,
        'getInvoices' => GetInvoices::class
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
