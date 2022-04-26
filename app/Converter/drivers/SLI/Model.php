<?php

namespace App\Converter\Drivers\SLI;

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
            'Month' => Carbon::parse($this->date)->format('M Y'),
            'Customer Name' => $this->name,
            'SA City' => $this->city,
            'Ship to State' => $this->state,
            'Item' => $this->item,
            'Memo' => $this->memo,
            'Quantity' => $this->quantity,
            'Unit Price' => $this->unit ? (float) $this->unit : (float) ((float) $this->amount / (float) $this->quantity),
            'Amount' => (float) $this->amount,
            '%age' => '7%',
            'Comm' => (float) $this->amount / 100 * 7,
            '   ' => '   ',
            'Original City' => $this->originalCity ?: null,
            'Original State' => $this->originalState ?: null,
            'Original Sale Amount' => (float) $this->amount,
            'Original Quantity' => $this->quantity,
            'Original Net Commission' => (float) $this->amount / 100 * 7,
        ];
    }
}
