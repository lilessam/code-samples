<?php

namespace App\Configer\Drivers;

use App\Models\System\Config;
use App\Configer\Contracts\Driver;

class Database implements Driver
{
    /**
     * Get a setting from the driver.
     * @param  string  $key
     * @param  string|null $default
     * @return string
     */
    public function get(string $key, $default = null)
    {
        return Config::get($key, $default);
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
        return Config::getValue($key, $default);
    }

    /**
     * Set a new value in the driver.
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
        return Config::set($name, $key, $data_type, $default, $value, $options);
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
        return Config::updateSetting($key, $updates);
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
        return Config::getOptions($key);
    }

    /**
     * Get all configurations.
     *
     * @return array
     */
    public function getAll()
    {
        return Config::getAll();
    }
}
