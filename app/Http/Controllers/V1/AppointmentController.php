<?php

namespace App\Http\Controllers\V1;

use App\Enums\StatusEnum;
use App\Events\AppointmentEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\AppointmentStatusNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function store(Request $request):JsonResponse
    {
        if(!Gate::allows('is-client')){
            return $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $data=$request->validate([
            'scheduleId'=>'required|exists:schedules,id'
        ]);

        $schedule=Schedule::query()->find($data['scheduleId']);

        if($schedule->isBooked){
            return $this->errorResponse('This time is already booked,',[],Response::HTTP_BAD_REQUEST);
        }

        $appointment=null;

        DB::transaction(function () use($schedule,&$appointment){
            $appointment=Appointment::query()->create([
                'client_id'=>Auth::id(),
                'schedule_id'=>$schedule->id,
            ]);

            $schedule->update([
                'isBooked'=>true
            ]);
            // $schedule->admin->notify(new AppointmentStatusNotification($appointment));

            broadcast(new AppointmentEvent($appointment));
        });

        return $this->successResponse('Appointment created successfully.',$appointment,Response::HTTP_CREATED);
    }

    public function index(Request $request):JsonResponse
    {
            $appointments = Appointment::query()
                ->select('appointments.*')
                ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
                ->join('slots', 'schedules.slot_id', '=', 'slots.id')
                ->with(['schedule', 'schedule.slot'])
                ->when(Auth::user()->isAdmin(),function($query){
                    $query->with('client')
                    ->where('schedules.admin_id',Auth::id());
                })
                ->when(Auth::user()->isClient(),function($query){
                    $query->with('schedule.admin')
                    ->where('appointments.client_id',Auth::id());
                })
                ->orderBy('schedules.date', 'asc')
                ->orderBy('slots.start', 'asc')
                ->paginate(10);
        

        return $this->successResponse('Appoitnment retreived successfully',AppointmentResource::collection($appointments),Response::HTTP_OK);
    }

    public function update(Request $request,Appointment $appointment):JsonResponse
    {
        $data=$request->validate([
            'status'=>['required',Rule::in(StatusEnum::values())]
        ]);

        $appointment->update([
            'status'=>$data['status']
        ]);

        if (Auth::user()->isAdmin()){
            $appointment->client->notify(new AppointmentStatusNotification($appointment));
        }else{
            $appointment->schedule->admin->notify(new AppointmentStatusNotification($appointment));
        }

        return $this->successResponse('Appoitnment successfully updated.',$appointment,Response::HTTP_OK);
    }
}
