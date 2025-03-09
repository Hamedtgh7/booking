<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable=[
        'start',
        'end'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class,'slot_id');
    }
}
