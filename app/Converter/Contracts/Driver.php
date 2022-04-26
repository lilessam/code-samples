<?php

namespace App\Converter\Contracts;

use App\Models\Operation;

interface Driver
{
    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path);

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process();
}
