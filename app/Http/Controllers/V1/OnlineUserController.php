<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OnlineUserService;
use Illuminate\Http\Request;

class OnlineUserController extends Controller
{
    public function index()
    {
        return response()->json(OnlineUserService::all());
    }
}
