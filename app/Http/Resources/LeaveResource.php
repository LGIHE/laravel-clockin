<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
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
            ],
            'leave_category_id' => $this->leave_category_id,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'max_in_year' => $this->category->max_in_year,
            ],
            'leave_status_id' => $this->leave_status_id,
            'status' => [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ],
            'date' => $this->date->format('Y-m-d'),
            'description' => $this->description,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

