<?php

namespace App\Logger\Contracts;

interface Model
{
    /**
     * Get the name of the model to be used in the logger.
     *
     * @return string
     */
    public function getName();
}
