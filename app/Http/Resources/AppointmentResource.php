<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            return [
                'id'=>$this->id,
                'status'=>$this->status,
                'start'=>$this->schedule->slot->start,
                'end'=>$this->schedule->slot->end,
                'date'=>$this->schedule->date,
                'name'=>Auth::user()->role==='admin'?$this->client->name:$this->schedule->admin->name
            ];
        

        return [];       
    }
}
