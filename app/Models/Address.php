<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['address', 'city', 'state', 'zip'];
}
