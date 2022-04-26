<?php

namespace App\Converter;

use App\Converter\Drivers\JBC;
use App\Converter\Drivers\BSS;
use App\Converter\Drivers\IRS;
use App\Converter\Drivers\KAS;
use App\Converter\Drivers\REM;
use App\Converter\Drivers\SSS;
use App\Converter\Drivers\COR;
use App\Converter\Drivers\RRL;
use App\Converter\Drivers\FTC;
use App\Converter\Drivers\KAP;
use App\Converter\Drivers\MLK;
use App\Converter\Drivers\SLI;
use App\Converter\Drivers\SWS;
use App\Converter\Drivers\TDE;
use Illuminate\Support\Manager;

class Converter extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['converter.default'] ?? 'jbc';
    }

    /**
     * Guess the driver of the input file.
     *
     * @param string $path
     * @return mixed
     */
    public function guessDriver(string $path)
    {
        $fileName = pathinfo(basename($path), PATHINFO_FILENAME);
        return $this->driver(strtolower(substr($fileName, 0, 3)));
    }

    /**
     * Create an instance of JBC driver.
     *
     * @return \App\Converter\Drivers\JBC
     */
    public function createJbcDriver()
    {
        return new JBC;
    }

    /**
     * Create an instance of BSS driver.
     *
     * @return \App\Converter\Drivers\BSS
     */
    public function createBssDriver()
    {
        return new BSS;
    }

    /**
     * Create an instance of IRS driver.
     *
     * @return \App\Converter\Drivers\IRS
     */
    public function createIrsDriver()
    {
        return new IRS;
    }

    /**
     * Create an instance of SSS driver.
     *
     * @return \App\Converter\Drivers\SSS
     */
    public function createSssDriver()
    {
        return new SSS;
    }

    /**
     * Create an instance of REM driver.
     *
     * @return \App\Converter\Drivers\SSS
     */
    public function createRemDriver()
    {
        return new REM;
    }

    /**
     * Create an instance of KAS driver.
     *
     * @return \App\Converter\Drivers\KAS
     */
    public function createKasDriver()
    {
        return new KAS;
    }

    /**
     * Create an instance of COR driver.
     *
     * @return \App\Converter\Drivers\COR
     */
    public function createCorDriver()
    {
        return new COR;
    }

    /**
     * Create an instance of RRL driver.
     *
     * @return \App\Converter\Drivers\RRL
     */
    public function createRrlDriver()
    {
        return new RRL;
    }

    /**
     * Create an instance of FTC driver.
     *
     * @return \App\Converter\Drivers\FTC
     */
    public function createFtcDriver()
    {
        return new FTC;
    }

    /**
     * Create an instance of TDE driver.
     *
     * @return \App\Converter\Drivers\TDE
     */
    public function createTdeDriver()
    {
        return new TDE;
    }

    /**
     * Create an instance of KAP driver.
     *
     * @return \App\Converter\Drivers\KAP
     */
    public function createKapDriver()
    {
        return new KAP;
    }

    /**
     * Create an instance of SWS driver.
     *
     * @return \App\Converter\Drivers\SWS
     */
    public function createSwsDriver()
    {
        return new SWS;
    }

    /**
     * Create an instance of SLI driver.
     *
     * @return \App\Converter\Drivers\SLI
     */
    public function createSliDriver()
    {
        return new SLI;
    }

    /**
     * Create an instance of MLK driver.
     *
     * @return \App\Converter\Drivers\MLK
     */
    public function createMlkDriver()
    {
        return new MLK;
    }
}
