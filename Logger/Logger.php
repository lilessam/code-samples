<?php

namespace App\Logger;

use Illuminate\Support\Manager;
use App\Logger\Drivers\Database;

class Logger extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['logger.default'] ?? 'database';
    }

    /**
     * Create an instance of Database driver.
     *
     * @return \App\Logger\Drivers\Database
     */
    public function createDatabaseDriver()
    {
        return new Database;
    }
}
