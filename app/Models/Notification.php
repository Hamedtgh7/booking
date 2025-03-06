<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable=[
        'userId',
        'title',
        'message',
        'isRead'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
