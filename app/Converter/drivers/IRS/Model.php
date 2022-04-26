<?php

namespace App\Converter\Drivers\IRS;

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
            'Invoice' => Carbon::parse($this->invoiceDate)->format(config('converter.default_date_format')),
            'Company' => $this->companyName,
            'SA City' => $this->city,
            'SA State' => $this->state,
            'Zip' => $this->zip,
            'Sales Total' => (float) $this->salesTotal,
            'Comm %' => config('converter.callbacks.commission_rate')($this->commissionRate),
            'Commission' => (float) $this->commission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->salesTotal,
            'Original Quantity' => 0,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
