<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tasksTotal = $this->tasks_count ?? 0;
        $tasksCompleted = $this->tasks_completed ?? 0;
        $completionPercent = $tasksTotal > 0 ? round(($tasksCompleted / $tasksTotal) * 100) : 0;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'description' => $this->description,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'ai_generated' => $this->ai_generated,
            'last_replanned_at' => $this->last_replanned_at,
            'tasks_total' => $tasksTotal,
            'tasks_completed' => $tasksCompleted,
            'completion_percent' => $completionPercent,
            'schedule_blocks' => ScheduleBlockResource::collection($this->whenLoaded('scheduleBlocks')),
            'parameters' => $this->whenLoaded('parameters'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
