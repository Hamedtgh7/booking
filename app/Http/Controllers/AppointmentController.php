<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        if(Auth::user()->role!=='client'){
            return response()->json([
                'message'=>'Accsee denied.'
            ],403);
        }

        $data=$request->validate([
            'scheduleId'=>'required|exists:schedules,id'
        ]);

        $schedule=Schedule::query()->find($data['scheduleId'],'id');

        if($schedule->isBooked){
            return response()->json(['message'=>'This time is booked']);
        }

        $appointment=Appointment::query()->create([
            'clientId'=>Auth::id(),
            'scheduleId'=>$schedule->id,
        ]);

        $schedule->update([
            'isBooked'=>true
        ]);

        return response()->json(['message'=>'Appointment created','appointment'=>$appointment]);
    }

    public function index(Request $request)
    {
        if (Auth::user()->role==='admin'){
            $appointments = Appointment::query()
                ->select('appointments.*')
                ->join('schedules', 'appointments.scheduleId', '=', 'schedules.id')
                ->join('slots', 'schedules.slotId', '=', 'slots.id')
                ->with(['schedule', 'schedule.slot', 'client'])
                ->where('schedules.adminId', Auth::id())
                ->orderBy('schedules.date', 'asc')
                ->orderBy('slots.start', 'asc')
                ->paginate(10);
        } else {
            $appointments = Appointment::query()
                ->select('appointments.*')
                ->join('schedules', 'appointments.scheduleId', '=', 'schedules.id')
                ->join('slots', 'schedules.slotId', '=', 'slots.id')
                ->with(['schedule', 'schedule.slot', 'schedule.admin']) 
                ->where('appointments.clientId', Auth::id()) 
                ->orderBy('schedules.date', 'asc')
                ->orderBy('slots.start', 'asc')
                ->paginate(10);
            }

        return AppointmentResource::collection($appointments);
    }

    public function update(Request $request,Appointment $appointment)
    {
        if (Auth::user()->role!=='admin'){
            return response()->json(['message'=>'Access denied'],403);
        }

        $data=$request->validate([
            'status'=>'required|in:pending,confirmed,canceled'
        ]);

        $appointment->update([
            'status'=>$data['status']
        ]);

        Notification::query()->create([
            'userId'=>$appointment->clientId,
            'title'=>'Appointment status updated.',
            'message'=>"Your Appointment status has been updated: {$appointment->status}"
        ]);

        return response()->json(['message'=>'Updated successfully.','appointment'=>$appointment]);
    }
}
