<?php

namespace App\Converter\Drivers\JBC;

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
            'Invoice Date' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'Customer Name' => $this->customerName,
            'D-Macs City' => $this->city,
            'SA State' => $this->state,
            'D-Macs Sales' => (float) $this->sales,
            'Commission' => (float) ($this->commissionRate * $this->sales),
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Freight' => (float) $this->freight,
            'Original Quantity' => 0,
            'Original %age' => (float) number_format($this->commissionRate*100, 2) . '%',
            'Original Net Commission' => (float) ($this->commissionRate * $this->sales),
        ];
    }
}
