<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "action" => $this->action_type,
            "payload" => $this->payload,
            "status" => $this->status,
            "user" => [
                "id" => $this->user->id,
                "name" => $this->user->name,
                "email" => $this->user->email
            ],
            "place" => [
                "id" => optional($this->place)->id,
                "name" => optional($this->place)->name,
                "category" => optional(optional($this->place)->category)->name
            ],
            "admin_comment" => $this->admin_comment,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}
