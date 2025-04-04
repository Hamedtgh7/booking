<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('appointments.admin.{adminId}',function($user,$adminId){
    return true;
});

Broadcast::channel('online.users',function($user){
    Log::info('problen');
    return ['id'=>$user->id,'name'=>$user->name];
});