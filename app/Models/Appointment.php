<?php

namespace App\Models;

use app\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable=[
        'client_id',
        'schedule_id',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class,'client_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class,'schedule_id');
    }
}
