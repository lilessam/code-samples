<?php

namespace App\Converter\Drivers\TDE;

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
            'INV.#' => $this->invoice,
            'INV. DATE' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'PART#' => trim($this->part),
            'PART DESC.' => trim($this->partDesc),
            'Ship QTY' => (float) $this->quantity,
            'UNIT$' => (float) $this->unitPrice,
            'CUSTOMER NAME' => $this->name,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Total Cost' => (float) $this->sales,
            '%age' => config('converter.callbacks.commission_rate')($this->commissionRate),
            'Comm' => (float) $this->commission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Ship To' => $this->originalShipTo ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => $this->quantity,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
