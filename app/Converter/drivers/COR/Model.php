<?php

namespace App\Converter\Drivers\COR;

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
        if (substr($this->amount, -1) == '-') {
            $this->amount = '-' . $this->amount;
            $this->commission = '-' . $this->commission;
        }

        return [
            'Inv Date' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'Inv#' => $this->invoiceNumber,
            'Part' => $this->part,
            'Customer' => $this->customerName,
            'D-Macs City' => $this->city,
            'Ship State' => $this->state,
            'Amount' => (float) str_replace(',', '', $this->amount),
            '%age' => config('converter.callbacks.commission_rate')(str_replace(',', '', $this->commissionRate)),
            'Comm' => (float) str_replace(',', '', $this->commission),
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) str_replace(',', '', $this->amount),
            'Original Quantity' => 0,
            'Original Net Commission' => (float) str_replace(',', '', $this->commission),
        ];
    }
}
