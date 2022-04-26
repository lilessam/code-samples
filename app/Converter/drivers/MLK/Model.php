<?php

namespace App\Converter\Drivers\MLK;

use Exception;
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
        try {
            $orderDate = $this->orderDate != '' && $this->orderDate != null ? Carbon::parse($this->orderDate)->format(config('converter.default_date_format')) : '';
        } catch (Exception $e) {
            $orderDate = '';
        }

        return [
            'Inv Date' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'Customer Name' => $this->customerName,
            'Invoice #' => $this->invoiceId,
            'Item' => $this->item,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Qty Sold' => $this->quantitySold,
            'Sales Amount' => $this->salesAmount,
            '%age' => config('converter.callbacks.commission_rate')($this->commissionRate),
            'Comm' => $this->commission ?: $this->salesAmount / 100 * $this->commissionRate,
            'Order Date' => $orderDate,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Zip Code' => $this->originalZipcode ?: null,
            'Original Sale Amount' => (float) $this->salesAmount,
            'Original Quantity' => (float) $this->quantitySold,
            'Original Net Commission' => $this->commission ?: $this->salesAmount / 100 * $this->commissionRate,
        ];
    }
}
