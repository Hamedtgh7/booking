<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    protected $fillable=[
        'isAvailable',
        'date',
        'time'
    ];
}
