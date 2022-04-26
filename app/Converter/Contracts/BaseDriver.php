<?php

namespace App\Converter\Contracts;

use App\Models\Operation;

interface BaseDriver
{
    /**
     * Set the user ID who took the action.
     *
     * @param integer $user_id
     *
     * @return self
     */
    public function setUser(int $user_id);

    /**
     * Set the input file name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setInputFileName(string $name);

    /**
     * Save the output file and get its path.
     * @return string
     */
    public function output() : string;

    /**
     * Log the operation into the database.
     *
     * @return \App\Models\Operation
     */
    public function log() : Operation;
}
