<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable=[
        'user_id','action','requestData','responseStatus','url','method','description'
    ];

    protected $casts=[
        'requestData'=>'array'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
