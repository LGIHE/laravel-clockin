<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'department' => $this->user->department ? [
                    'id' => $this->user->department->id,
                    'name' => $this->user->department->name,
                ] : null,
                'designation' => $this->user->designation ? [
                    'id' => $this->user->designation->id,
                    'name' => $this->user->designation->name,
                ] : null,
            ],
            'in_time' => $this->in_time?->toISOString(),
            'in_message' => $this->in_message,
            'out_time' => $this->out_time?->toISOString(),
            'out_message' => $this->out_message,
            'worked' => $this->worked,
            'worked_hours' => $this->worked_hours,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
