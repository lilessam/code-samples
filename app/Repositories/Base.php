<?php

namespace App\Repositories;

abstract class Base
{
    /**
     * Determines the model.
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * Creates a new instance of the mode.
     *
     * @return mixed
     */
    protected function newInstance()
    {
        $class = $this->model();
        return (new $class);
    }
}
