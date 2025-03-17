<?php

namespace App\Http\Resources\Analytic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CancelClientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'client_id'=>$this->client_id,
            'name'=>$this->client->name ?? 'Unknown',
            'cacnceledCount'=>$this->count
        ];
    }
}
