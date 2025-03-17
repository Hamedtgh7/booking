<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Analytic\PopularSlotsResource;
use App\Http\Resources\Analytic\CancelClientsResource;
use App\Http\Resources\Analytic\UserActivityResource;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use App\Models\UserActivity;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;


class AnalyticController extends Controller
{
    use ApiResponse;

    public function popularSlots() :JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $query=Schedule::query()->where('isBooked',true);

        $totalReservedSlot=$query->count();

        $slots = $query
        ->selectRaw('slot_id, COUNT(*) as count')
        ->groupBy('slot_id')
        ->orderByDesc('count')
        ->limit(5)
        ->with('slot:id,start,end')
        ->get();
        

        return $this->successResponse('Popular slots retrieved successfully.',
                PopularSlotsResource::collection($slots->map(fn($slot)=>new PopularSlotsResource($slot,$totalReservedSlot)))
                ,Response::HTTP_OK);
    }

    public function userActivity():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $activities= UserActivity::query()->with('user')
        ->orderByDesc('created_at')
        ->paginate(10);

        return $this->successResponse(
            'User Activity retrieved successfully.',
            [
                'data' => UserActivityResource::collection($activities),
                'meta' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'total' => $activities->total(),
                    'per_page' => $activities->perPage(),
                ]
            ],
            Response::HTTP_OK
        );
    }

    public function cancelRate():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $appointments=Appointment::query();
        $allAppointment=$appointments->count();
        $cancelAppointment=$appointments->where('status','canceled')->count();
        $rate=$allAppointment >0 ? ($cancelAppointment/$allAppointment)*100 :0;
        
        return $this->successResponse('Cancellation rate retrieved successfully.',[
            'rate'=>$rate,
            'cancelAppointments'=>$cancelAppointment,
            'allAppointments'=>$allAppointment
        ],Response::HTTP_OK);
    } 
    
    public function topReservationClients():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $clients= User::query()
        ->whereHas('appointments')
        ->withCount('appointments')
        ->orderByDesc('appointments_count')
        ->limit(5)
        ->get(['id','name']);
        
        return $this->successResponse('Top reservation clients retrieved successfully.',$clients,Response::HTTP_OK);
    }

    public function dailyBookingRate():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $rates= Schedule::query()->selectRaw("DATE_FORMAT(date, '%d %M %Y') as formatted_date,
                                            COUNT(*) as booked_slot ,
                                            (SELECT COUNT(*) FROM slots) as total_slots,
                                            (COUNT(*)/(SELECT COUNT(*) FROM slots))*100 as fillRate")
        ->where('isBooked',true)
        ->groupBy('date')
        ->orderByDesc('date')
        ->limit(30)
        ->get();

        return $this->successResponse('Daily booking rates retrieved successfully.',$rates,Response::HTTP_OK);
    }

    public function topCancelReserveClients():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $clients= Appointment::query()
        ->with('client:id,name')
        ->where('status','canceled')
        ->selectRaw('client_id,COUNT(*) as count')
        ->groupBy('client_id')
        ->orderByDesc('count')
        ->limit(10)
        ->get();

        return $this->successResponse('Top canceled reservation clients retrieved successfully.',CancelClientsResource::collection($clients),Response::HTTP_OK);
    }

    public function inactiveUsers():JsonResponse
    {
        if(!Gate::allows('is-admin')){
            $this->errorResponse('Access denied',[],Response::HTTP_FORBIDDEN);
        }

        $users = User::query()
        ->whereDoesntHave('activities')
        ->orWhereHas('activities', function ($query) {
            $query->where('created_at', '<', now()->subDays(30));
        })
        ->select(['id', 'name'])
        ->selectSub(
            UserActivity::query()
               ->whereColumn('user_id', 'users.id')
                ->latest('created_at')
                ->limit(1)
                ->select('created_at'),
                'lastActivity'
        )
        ->paginate(5)
        ->through(function ($user) {
            $user->lastActivity = $user->lastActivity ? Carbon::parse($user->lastActivity)->diffForHumans() : 'Never';
            return $user;
        });

        return $this->successResponse('Inactive users retrieved successfully.', $users, Response::HTTP_OK);
    }
}
