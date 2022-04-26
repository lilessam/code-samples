<?php

namespace App\Converter\Drivers\SWS;

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
            'Inv#' => $this->invoice,
            'Cust PO#' => $this->customerPo,
            'Item Code' => $this->itemCode,
            'Description' => $this->description,
            'Unit Price' => (float) $this->unitPrice,
            'Qty' => $this->quantity,
            'SA Customer' => $this->name,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Sales' => (float) $this->sales,
            'Comm Rate' => config('converter.callbacks.commission_rate')((float) ($this->commissionRate * 100)),
            'Comm' => (float) $this->commission,
            '   ' => '   ',
            'Original Ship To' => $this->originalShipTo ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => $this->quantity,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
