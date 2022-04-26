<?php

namespace App\Configer\Drivers;

use App\Configer\Contracts\Driver;
use Illuminate\Support\Facades\Storage;

class File implements Driver
{
    /**
     * The config array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Make a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Storage::disk('local')->exists('config.txt')) {
            $this->saveConfig();
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $file = $storagePath . '/config.txt';
        $this->config = include $file;
    }

    /**
     * Save the configuration array in the file.
     *
     * @return void
     */
    public function saveConfig()
    {
        Storage::disk('local')->put('config.txt', '<?php return ' . var_export($this->config, true) . ';');
    }

    /**
     * Get a setting from the driver.
     * @param  string  $key
     * @param  string|null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->config[$key]) {
            return $this->config[$key];
        }

        return $default;
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
        if ($this->config[$key]) {
            if ($this->config[$key]['value']) {
                return $this->config[$key]['value'];
            } else {
                return $this->config[$key]['default'];
            }
        }

        return $default;
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
        try {
            $this->config[$key] = [
                'name' => $name,
                'key' => $key,
                'data_type' => $data_type,
                'default' => $default,
                'value' => $value,
                'options' => $options
            ];

            $this->saveConfig();

            return true;
        } catch (\Exceptions $e) {
            return false;
        }
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
        try {
            if (isset($this->config[$key])) {
                foreach ($updates as $update_key => $update_value) {
                    $this->config[$key][$update_key] = $update_value;
                }
            }

            $this->saveConfig();

            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        return $this->config[$key]['options'];
    }

    /**
     * Get all configurations.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }
}
