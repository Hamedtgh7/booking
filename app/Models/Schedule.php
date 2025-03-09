<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable=[
        'date',
        'isBooked'
    ];

    public function slot(){
        return $this->belongsTo(Slot::class,'slot_id');
    }

    public function admin(){
        return $this->belongsTo(User::class,'admin_id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class,'schedule_id');
    }

    protected $casts=[
        'date'=>'date',
        'isBooked'=>'boolean'
    ];
}
