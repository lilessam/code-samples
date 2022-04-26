<?php

namespace App\Services;

use App\Services\Contracts\Service;

class Sorter extends BaseService implements Service
{
    /**
     * @return void
     */
    public function process()
    {
        //
        $sortedRows = collect($this->rows)->sort(function ($a, $b) {
            if ($this->city != null) {
                return
                    strcmp($a[$this->customer], $b[$this->customer]) ?:
                    strcmp($a[$this->state], $b[$this->state]) ?:
                    strcmp($a[$this->city], $b[$this->city]);
            } else {
                return
                strcmp($a[$this->customer], $b[$this->customer]) ?:
                strcmp($a[$this->state], $b[$this->state]);
            }
        });

        return $sortedRows;
    }
}
