<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchedulesRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchedulesController extends Controller
{
    public function store(SchedulesRequest $request)
    {
        if(Auth::user()->role!=='admin'){
            return response()->json([
                'message'=>'Accsee denied.'
            ],403);
        }

        $schedules=[];
        $existingSchedules=Schedule::query()->where('adminId',Auth::id())
                            ->where('date',$request->validated()['date'])
                            ->whereIn('slotId',$request->validated()['slots'])
                            ->pluck('slotId')
                            ->toArray();


        foreach ($request->validated()['slots'] as $slot){
            if(!in_array($slot,$existingSchedules)){
                $schedules[]=[
                    'adminId'=>Auth::id(),
                    'date'=>$request->validated()['date'],
                    'slotId'=>$slot,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
            }
        }

        Schedule::query()->insert($schedules);

        return response()->json([
            'message'=>'Schedule created.',
            'data'=>$schedules
        ],201);
    }

    public function index(Request $request){

        $date=$request->query('date');

        if(!$date||!strtotime($date)){
            return response()->json([
                'message'=>'Invalid date'
            ],400);
        }

        return Schedule::query()->where('adminId',Auth::id())
            ->where('date',$date)->paginate(10); 
    }

    public function destroy(int $id)
    {
        if (Auth::user()->role!=='admin'){
            return response()->json(['message'=>'Access denied'],403);
        }
        
        $schedule=Schedule::where('adminId',Auth::id())->find($id);

        if(!$schedule){
            return response()->json(['message'=>'Schedule not found.'],404);
        }

        $schedule->delete();

        return response()->json(['message'=>'Schedule deleted.']);
    }

    public function getAdmins()
    {
       return  response()->json(User::query()->where('role','admin')->get(['id','name']));
    }

    public function getAdminsSchedules(Request $request,User $admin)
    {
        $date=$request->validate([
            'date'=>'required|date'
        ]);

        $schedules=Schedule::query()->where('adminId',$admin->id)
            ->whereDate('date',$date)
            ->where('isBooked',false)
            ->with('slot')
            ->get();

        return ScheduleResource::collection($schedules);
    }
}
