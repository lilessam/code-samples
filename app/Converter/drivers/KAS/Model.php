<?php

namespace App\Converter\Drivers\KAS;

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
        $commissionRate = $this->sales == 0 ? '0.00%' : config('converter.callbacks.commission_rate')((float) $this->commission / (float) $this->sales * 100);

        if ($this->dontCalculateCommissionRate) {
            $commissionRate = null;
        }

        return [
            'Date' => $this->date ? Carbon::parse($this->date)->format(config('converter.default_date_format')) : null,
            'Invoice #' => $this->invoice,
            'PO #' => $this->po,
            'Bill To' => $this->name,
            'SA State' => $this->state,
            'SA City' => $this->city,
            'Sale' => (float) $this->sales,
            '%age' => $commissionRate,
            'Comm' => $this->commission,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->sales,
            'Original Quantity' => 0,
            'Original Net Commission' => (float) $this->commission,
        ];
    }
}
