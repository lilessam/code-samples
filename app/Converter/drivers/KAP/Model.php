<?php

namespace App\Converter\Drivers\KAP;

use ArrayAccess;
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
            'Product' => $this->product,
            'Qty' => (float) $this->qty,
            'Unit' => (float) $this->unit,
            'Distributor' => $this->distributor,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Dollars' => (float) $this->sales,
            '%age Used' => config('converter.callbacks.commission_rate')((float) $this->commissionRate * 100),
            'Comm' => (float) $this->commission,
            '   ' => '   ',
            'Original Address' => $this->originalAddress ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => (float) $this->qty,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
