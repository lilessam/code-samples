<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'driver',
        'name',
        'city',
        'state',
        'zipcode'
    ];

    /**
     * @return mixed
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
