<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'adminId'=>$this->adminId,
            'date'=>$this->date,
            'slot'=>[
                'id'=>$this->slot->id,
                'start'=>$this->slot->start,
                'end'=>$this->slot->end
            ]
        ];
    }
}
