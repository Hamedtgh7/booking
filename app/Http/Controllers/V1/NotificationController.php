<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request):JsonResponse
    {
        $notifications=Auth::user()->notifications()->orderByDesc('created_at')->paginate(10);

        return $this->successResponse('Notification retrieved successfully',NotificationResource::collection($notifications),Response::HTTP_OK);
    }

    public function update(Notification $notification)
    {
        $notification->markAsRead();

        return $this->successResponse('Notification marked as read.',[],Response::HTTP_OK);
    }
}
