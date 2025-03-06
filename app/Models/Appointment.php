<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable=[
        'clientId',
        'scheduleId',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class,'clientId');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class,'scheduleId');
    }
}
