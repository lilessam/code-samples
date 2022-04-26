<?php

namespace Reporter;

use Reporter\Drivers\PDF;
use Reporter\Drivers\Word2007;
use Illuminate\Support\Manager;

class Generator extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['reporter.default'] ?? 'PDF';
    }

    /**
     * Create an instance of PDF driver.
     *
     * @return \Reporter\Drivers\PDF
     */
    public function createPDFDriver()
    {
        return new PDF;
    }

    /**
     * Create an instance of Word2007 driver.
     *
     * @return \Reporter\Drivers\Word2007
     */
    public function createWord2007Driver()
    {
        return new Word2007($this->app['view']);
    }
}
