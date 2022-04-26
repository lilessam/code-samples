<?php

namespace App\Configer\Contracts;

interface Driver
{
    /**
     * Get a setting from the driver.
     * @param  string  $key
     * @param  string|null  $default
     * @return array
     */
    public function get(string $key, $default = null);

    /**
     * Get a setting value.
     *
     * @param  string  $key
     * @param  string|null  $default
     * @return string
     */
    public function getValue(string $key, $default = null);

    /**
     * Set a value in the driver.
     * @param  string $name
     * @param  string $key
     * @param  string $data_type
     * @param  mixed $default
     * @param  mixed $value
     * @param  array|null $options
     * @return bool
     */
    public function set(string $name, string $key, string $data_type, $default, $value, $options = []) : bool;

    /**
     * Update a setting.
     *
     * @param string $key
     * @param array $updates
     * @return string|null
     */
    public function update(string $key, $updates);

    /**
     * Get array of options for a setting.
     *
     * @param string $key
     * @return array|null
     */
    public function options(string $key);

    /**
     * Get all configurations.
     *
     * @return array
     */
    public function getAll();
}
