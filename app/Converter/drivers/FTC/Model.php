<?php

namespace App\Converter\Drivers\FTC;

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
            'Doc Date' => $this->documentDate ? Carbon::parse($this->documentDate)->format(config('converter.default_date_format')) : null,
            'Customer Name' => $this->customerName,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Comm Sale Amt' => (float) $this->salesAmount,
            '%age Used' => config('converter.callbacks.commission_rate')((float) $this->commissionRate * 100),
            'Net Comm' => (float) $this->netCommission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->salesAmount,
            'Original Quantity' => $this->quantity ?: null,
            'Original Net Commission' => (float) $this->netCommission,
        ];
    }
}
