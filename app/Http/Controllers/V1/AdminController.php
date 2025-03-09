<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    use ApiResponse;

    public function getAdmins():JsonResponse
    {  
        $users=User::query()->where('role','admin')->select(['id','name'])->paginate(10);
        return  $this->successResponse('Admins retrieved successfully.',$users,Response::HTTP_OK);
    }

    public function getAdminsSchedules(Request $request,User $admin):JsonResponse
    {
        $date=$request->validate([
            'date'=>'required|date'
        ]);

        $schedules=Schedule::query()->where('admin_id',$admin->id)
            ->whereDate('date','>=',now())
            ->whereDate('date',$date)
            ->where('isBooked',false)
            ->with('slot')
            ->get();

        return $this->successResponse('Schedules retireved successfully',ScheduleResource::collection($schedules),Response::HTTP_OK);
    }
}
