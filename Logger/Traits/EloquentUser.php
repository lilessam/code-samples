<?php

namespace App\Logger\Traits;

trait EloquentUser
{
    /**
     * Get the name of the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
