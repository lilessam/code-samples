<?php

namespace App\Kashoo;

use App\Kashoo\Lib\Kashoo as Lib;
use App\Kashoo\Services\GetBills;
use App\Kashoo\Services\GetRecords;
use App\Kashoo\Services\GetVendors;
use App\Kashoo\Services\GetAccounts;
use App\Kashoo\Services\GetContacts;
use App\Kashoo\Services\GetInvoices;
use App\Kashoo\Services\GetCustomers;
use App\Kashoo\Services\GetBusinesses;
use App\Kashoo\Services\GetBillPayments;
use App\Kashoo\Services\CreateInvoice;

class Kashoo
{
    /**
     * The Kashoo client instance.
     *
     * @var \App\Kashoo\Lib\Kashoo
     */
    protected $client;

    /**
    * The services of Kashoo supported except authentication.
    *
    * @var array
    */
    public $services = [
        'getBusinesses' => GetBusinesses::class,
        'getAccounts' => GetAccounts::class,
        'getBillPayments' => GetBillPayments::class,
        'getBills' => GetBills::class,
        'getContacts' => GetContacts::class,
        'getCustomers' => GetCustomers::class,
        'getInvoices' => GetInvoices::class,
        'getRecords' => GetRecords::class,
        'getVendors' => GetVendors::class,
        'createInvoice' => CreateInvoice::class
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
        // Set this constant for the library used.
        //define('DEBUG', false);

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
        $kashoo = new Lib();
        $kashoo->createApiToken($args[0], $args[1]);
        $this->client = $kashoo;

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
            if ($method != 'getBusinesses') {
                $this->client->businessId = $parameters[0];
            }

            return (new $this->services[$method])->execute($this->client, ...$parameters);
        }

        return $this;
    }
}
