<?php

use App\Configer\Configer;

if (!function_exists('config_get')) {
    /**
     * Get a configuration variable.
     *
     * @param string $key
     * @param string|null $default
     * @return array|null
     */
    function config_get($key, $default = null)
    {
        return app(Configer::class)->get($key, $default);
    }
}

if (!function_exists('config_get_value')) {
    /**
      * Get a setting value.
      *
      * @param  string  $key
      * @param  string|null  $default
      * @return string
      */
    function config_get_value($key, $default = null)
    {
        return app(Configer::class)->getValue($key, $default);
    }
}

if (!function_exists('config_update')) {
    /**
     * Update a setting.
     *
     * @param string $key
     * @param array $updates
     * @return string|null
     */
    function config_update($key, $updates)
    {
        return app(Configer::class)->update($key, $updates);
    }
}

if (!function_exists('config_set')) {
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
    function config_set(string $name, string $key, string $data_type, $default, $value, $options = [])
    {
        return app(Configer::class)->set($name, $key, $data_type, $default, $value, $options);
    }
}

if (!function_exists('config_options')) {
    /**
     * Get array of options for a setting.
     *
     * @param string $key
     *
     * @return array|null
     */
    function config_options($key)
    {
        return app(Configer::class)->options($key);
    }
}

if (!function_exists('config_all')) {
    /**
     * Get all configurations.
     *
     * @return array
     */
    function config_all()
    {
        return app(Configer::class)->getAll();
    }
}
