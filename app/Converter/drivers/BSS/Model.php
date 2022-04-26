<?php

namespace App\Converter\Drivers\BSS;

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
            'Inv/CM Num' => $this->invoiceNum,
            'Doc Date' => Carbon::parse($this->docDate)->format(config('converter.default_date_format')),
            'Item Code' => $this->itemCode,
            'Item Description' => $this->itemDescription,
            'Qty' => (float) trim($this->qty) ?: 0,
            'Price' => (float) trim($this->price) ? (float) $this->price : 0,
            'Cust Name' => $this->customerName,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Product Total' => (float) trim($this->productTotal) ? (float) $this->productTotal : 0,
            'Comm' => (float) $this->commission,
            '   ' => '   ',
            'Original Address' => $this->originalAddress ?: null,
            'Original Sale Amount' => (float) $this->productTotal,
            'Original Quantity' => (float) trim($this->qty),
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
