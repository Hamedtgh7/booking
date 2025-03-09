<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SlotResource;
use App\Models\Slot;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SlotController extends Controller
{
    use ApiResponse;

    public function index():JsonResponse
    {
        return $this->successResponse('Slots retrieved successfully',SlotResource::collection(Slot::all()),Response::HTTP_OK);
    }
}
