<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchedulesRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SchedulesController extends Controller
{
    use ApiResponse;

    public function store(SchedulesRequest $request):JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $schedules = collect($request->validated()['slots'])
        ->filter(fn($slot) => !Schedule::query()
            ->where('admin_id', Auth::id())
            ->where('date', $request->validated()['date'])
            ->where('slot_id', $slot)
            ->exists())
        ->map(fn($slot) => [
            'admin_id' => Auth::id(),
            'date' => $request->validated()['date'],
            'slot_id' => $slot,
            'created_at' => now(),
            'updated_at' => now()
        ])
        ->toArray();

        Schedule::query()->insert($schedules);

        return $this->successResponse('Schedule created successfully.',$schedules,Response::HTTP_CREATED);
    }

    public function index(Request $request):JsonResponse 
    {
        $date=$request->validate([
            'date'=>'required|date'
        ]);

        $schedules=Schedule::query()->where('admin_id',Auth::id())
            ->where('date',$date)
            ->where('isBooked',false)
            ->with('slot')->get();

        return $this->successResponse('Schedules retrived successfully',ScheduleResource::collection($schedules),Response::HTTP_OK);
    }

    public function destroy(Schedule $schedule):JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }
        
        $schedule->delete();

        return $this->successResponse('Schedule deleted successfully.',new ScheduleResource($schedule),Response::HTTP_OK);
    }
}
