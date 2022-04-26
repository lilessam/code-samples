<?php

namespace App\Converter\Drivers\SSS;

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
        if (!$this->description) {
            $productDescription = explode(',', $this->product);
            $product = isset($productDescription[0]) ? $productDescription[0] : $this->product;
            $description = isset($productDescription[1]) ? $productDescription[1] : '';
        } else {
            $product = $this->product;
            $description = $this->description;
        }

        return [
            'Inv Date' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'Customer Name' => $this->name,
            'SA City' => $this->city,
            'Ship to State' => $this->state,
            'Product' => $product,
            'Descrip' => $description,
            'Qty' => $this->quantity,
            'Unit $' => (float) $this->unitPrice,
            'Amt Total' => (float) $this->sales,
            'Comm' => (float) $this->sales / 100 * 8,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => $this->quantity,
            'Original Net Commission' => (float) $this->sales / 100 * 8,
        ];
    }
}
