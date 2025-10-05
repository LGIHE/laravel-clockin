<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'role' => $this->role,
            'user_level' => [
                'id' => $this->userLevel?->id,
                'name' => $this->userLevel?->name,
            ],
            'department' => $this->when($this->department, [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
            ]),
            'designation' => $this->when($this->designation, [
                'id' => $this->designation?->id,
                'name' => $this->designation?->name,
            ]),
            'project_ids' => $this->project_id ? json_decode($this->project_id, true) : [],
            'last_in_time' => $this->last_in_time?->format('H:i:s'),
            'auto_punch_out_time' => $this->auto_punch_out_time?->format('H:i:s'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

