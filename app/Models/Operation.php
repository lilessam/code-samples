<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['input_file', 'output_file', 'client_id', 'user_id', 'input_file_name'];

    /**
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * @var array
     */
    protected $appends = ['created'];

    /**
     * @return string
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
