<?php

namespace App\Services;

class BaseService
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === "set") {
            $attribute = snake_case(str_replace('set', '', $name));
            $this->$attribute = $arguments[0];
            return $this;
        }

        return $this->{$name}(...$arguments);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return in_array($name, array_keys($this->attributes)) ? $this->attributes[$name] : null;
    }

    /**
     * @param string $attribute
     * @return boolean
     */
    public function has($attribute)
    {
        return in_array($attribute, array_keys($this->attributes));
    }
}
