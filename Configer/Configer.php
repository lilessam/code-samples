<?php

namespace App\Configer;

use App\Configer\Contracts\Driver;

class Configer implements Driver
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of drivers.
     *
     * @var array
     */
    protected $drivers;

    /**
     * The getter driver.
     *
     * @var string
     */
    protected $getter;

    /**
     * The getter instance.
     *
     * @var \App\Configer\Drivers\File
     */
    protected $getterInstance;

    /**
     * Create a new Configer instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->drivers = $app['config']['configer.drivers'];
        $this->getter = $app['config']['configer.getter'];

        $getter = __NAMESPACE__ . '\\Drivers\\' . ucfirst($this->getter);
        $this->getterInstance = new $getter;
    }

    /**
     * Get a setting from the driver.
     * @param  string  $key
     * @param  string|null $default
     * @return array
     */
    public function get(string $key, $default = null)
    {
        return $this->getterInstance->get($key, $default);
    }

    /**
     * Get a setting value.
     *
     * @param  string  $key
     * @param  string|null  $default
     * @return string
     */
    public function getValue(string $key, $default = null)
    {
        return $this->getterInstance->getValue($key, $default);
    }

    /**
     * Set a new value in all driver.
     * @param  string $name
     * @param  string $key
     * @param  string $data_type
     * @param  mixed $default
     * @param  mixed $value
     * @param  array|null $options
     * @return bool
     */
    public function set(string $name, string $key, string $data_type, $default, $value, $options = []) : bool
    {
        foreach ($this->drivers as $driver) {
            $driver = __NAMESPACE__ . '\\Drivers\\' . ucfirst($driver);
            $instance = new $driver;
            $instance->set($name, $key, $data_type, $default, $value, $options);
        }

        return true;
    }

    /**
     * Update a setting.
     *
     * @param string $key
     * @param array $updates
     * @return string|null
     */
    public function update(string $key, $updates)
    {
        foreach ($this->drivers as $driver) {
            $driver = __NAMESPACE__ . '\\Drivers\\' . ucfirst($driver);
            $instance = new $driver;
            $instance->update($key, $updates);
        }

        return true;
    }

    /**
     * Get array of options for a setting.
     *
     * @param string $key
     *
     * @return array|null
     */
    public function options(string $key)
    {
        return $this->getterInstance->options($key);
    }

    /**
     * Get all configurations.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->getterInstance->getAll();
    }
}
