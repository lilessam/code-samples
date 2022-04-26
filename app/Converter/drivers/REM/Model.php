<?php

namespace App\Converter\Drivers\REM;

use ArrayAccess;
use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Contracts\Support\Arrayable;

class Model extends Fluent implements ArrayAccess, Arrayable
{
    /**
     * Return the model.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'Month' => $this->month,
            'Date' => Carbon::parse($this->date)->format(config('converter.default_date_format')),
            'INV' => $this->invoice,
            'Customer' => $this->customer,
            'SA City' => $this->city,
            'Ship To' => $this->state,
            'Sales' => (float) $this->sales,
            '%age' => config('converter.callbacks.commission_rate')((float) ($this->commissionRate)),
            'Comm Paid' => (float) $this->commission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
