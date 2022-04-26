<?php

namespace App\Magento;

use App\Magento\Services\AddOrder;
use App\Magento\Services\AddProduct;
use App\Magento\Services\AddCustomer;

class Magento
{
    /**
    * The services of Magento supported except authentication.
    *
    * @var array
    */
    public $services = [
        'addProduct' => AddProduct::class,
        'addCustomer' => AddCustomer::class,
        'addOrder' => AddOrder::class
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
