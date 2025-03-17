<?php

use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\AnalyticController;
use App\Http\Controllers\V1\AppointmentController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\SchedulesController;
use App\Http\Controllers\V1\SlotController;
use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function (){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
});

Route::middleware(['auth:sanctum',LogActivityMiddleware::class])->group(function(){

    Route::post('/logout',[AuthController::class,'logout']);

    Route::apiResource('slots',SlotController::class)->only('index');

    Route::apiResource('schedules',SchedulesController::class)->except(['update','show']);

    Route::apiResource('appointments',AppointmentController::class)->except(['destroy','show']);

    Route::apiResource('notifications',NotificationController::class)->only(['index','update']);
    
    Route::prefix('users')->group(Function(){
        Route::get('get-admins-list',[AdminController::class,'getAdmins']);
        Route::get('get-admins-list/{admin}/schedule',[AdminController::class,'getAdminsSchedules']);
    });

});

Route::middleware('auth:sanctum')->prefix('analytics')->group(function() {
    Route::get('/popular-slots', [AnalyticController::class, 'popularSlots']);
    Route::get('/user-activities', [AnalyticController::class, 'userActivity']);
    Route::get('/cancel-rate', [AnalyticController::class, 'cancelRate']);
    Route::get('/top-reservation-clients', [AnalyticController::class, 'topReservationClients']);
    Route::get('/daily-booking-rate', [AnalyticController::class, 'dailyBookingRate']);
    Route::get('/top-cancel-reserve-clients', [AnalyticController::class, 'topCancelReserveClients']);
    Route::get('/inactive-users', [AnalyticController::class, 'inactiveUsers']);
});