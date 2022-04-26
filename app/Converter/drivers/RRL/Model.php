<?php

namespace App\Converter\Drivers\RRL;

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
            'Date' => Carbon::parse($this->date)->format(config('converter.default_date_format')),
            'Inv#' => $this->invoice,
            'Product' => $this->product,
            'Qty' => $this->qty,
            'Unit $' => (float) $this->unit,
            'Distributor' => $this->distributor,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Sales' => (float) $this->sales,
            'Comm' => (float) $this->commission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => $this->qty,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
