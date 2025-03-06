<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\SlotController;
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
    Route::get('/admins',[SchedulesController::class,'getAdmins']);
    Route::get('/admins/{admin}',[SchedulesController::class,'getAdminsSchedules']);
    Route::post('/appointments',[AppointmentController::class,'store']);
    Route::get('/appointments',[AppointmentController::class,'index']);
    Route::put('/appointments/{appointment}',[AppointmentController::class,'update']);
    Route::get('/notifications',[NotificationController::class,'index']);
});