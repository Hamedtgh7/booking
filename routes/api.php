<?php

use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\AppointmentController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\SchedulesController;
use App\Http\Controllers\V1\SlotController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function (){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/slots',[SlotController::class,'index']);

    Route::post('/logout',[AuthController::class,'logout']);
    
    Route::post('/schedules',[SchedulesController::class,'store']);
    Route::get('/schedules',[SchedulesController::class,'index']);
    Route::delete('/schedules/{schedule}',[SchedulesController::class,'destroy']);

    Route::get('/admins',[AdminController::class,'getAdmins']);
    Route::get('/admins/{admin}',[AdminController::class,'getAdminsSchedules']);

    Route::post('/appointments',[AppointmentController::class,'store']);
    Route::get('/appointments',[AppointmentController::class,'index']);
    Route::put('/appointments/{appointment}',[AppointmentController::class,'update']);
    
    Route::get('/notifications',[NotificationController::class,'index']);
});