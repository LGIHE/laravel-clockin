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
            // Legacy single project/task (for backward compatibility)
            'project_id' => $this->project_id,
            'project' => $this->whenLoaded('project', function () {
                return $this->project ? [
                    'id' => $this->project->id,
                    'name' => $this->project->name,
                ] : null;
            }),
            'task_id' => $this->task_id,
            'task' => $this->whenLoaded('task', function () {
                return $this->task ? [
                    'id' => $this->task->id,
                    'title' => $this->task->title,
                    'status' => $this->task->status,
                ] : null;
            }),
            'task_status' => $this->task_status,
            // New multi-select relationships
            'projects' => $this->whenLoaded('projects', function () {
                return $this->projects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'description' => $project->description,
                        'status' => $project->status,
                    ];
                });
            }),
            'tasks' => $this->whenLoaded('tasks', function () {
                return $this->tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'attendance_status' => $task->pivot->status ?? null, // Status for this attendance session
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
