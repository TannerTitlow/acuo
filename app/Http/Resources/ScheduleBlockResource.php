<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tasksTotal = $this->tasks_count ?? 0;
        $tasksCompleted = $this->tasks_completed ?? 0;
        $completionPercent = $tasksTotal > 0 ? round(($tasksCompleted / $tasksTotal) * 100) : 0;

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'scheduled_date' => $this->scheduled_date,
            'order' => $this->order,
            'estimated_minutes' => $this->estimated_minutes,
            'actual_minutes' => $this->actual_minutes,
            'completed_at' => $this->completed_at,
            'snoozed_to' => $this->snoozed_to,
            'tasks_total' => $tasksTotal,
            'tasks_completed' => $tasksCompleted,
            'completion_percent' => $completionPercent,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
