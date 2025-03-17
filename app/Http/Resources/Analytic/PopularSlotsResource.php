<?php

namespace App\Http\Resources\Analytic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopularSlotsResource extends JsonResource
{

    public function __construct($resource,protected int $totalReservedSlot)
    {
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->slot_id,
            'startTime'=>$this->slot->start,
            'endTime'=>$this->slot->end,
            'totalBooking'=>$this->count,
            'popularity_percentage'=>$this->totalReservedSlot>0 ? round(($this->count/$this->totalReservedSlot)*100,2) : 0
        ];
    }
}
