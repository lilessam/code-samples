<?php

namespace App\Authorize;

use App\Authorize\Services\ChargeCard;
use net\authorize\api\contract\v1 as AnetAPI;

class Authorize
{
    /**
     * Authentication object.
     *
     * @var AnetAPI\MerchantAuthenticationType
     */
    private $authentication;

    /**
    * The services of Authorize supported except authentication.
    *
    * @var array
    */
    public $services = [
        'chargeCard' => ChargeCard::class,
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
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType;
        $merchantAuthentication->setName($args[0]);
        $merchantAuthentication->setTransactionKey($args[1]);
        $this->authentication = $merchantAuthentication;

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
            return (new $this->services[$method])->execute($this->authentication, ...$parameters);
        }

        return $this;
    }
}
