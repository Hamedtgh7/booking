<?php

namespace App\Http\Controllers;

use App\Http\Resources\SlotResource;
use App\Models\Slot;

class SlotController extends Controller
{
    public function index()
    {
        return SlotResource::collection(Slot::all());
    }
}
