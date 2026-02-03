<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->category->slug,
            'name' => $this->name,
            'description' => $this->description,

            'schedule' => $this->schedule,
            'cost' => $this->cost,
            'capacity' => $this->capacity,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'photos' => $this->photos->pluck('url_source')
        ];
    }
}
