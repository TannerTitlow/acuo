<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\ScheduleBlock;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(ScheduleBlock $block): JsonResponse
    {
        abort_if($block->project->user_id !== auth()->id(), 403);

        $tasks = $block->tasks()->orderBy('order')->get();

        return TaskResource::collection($tasks)->response();
    }

    public function store(StoreTaskRequest $request, ScheduleBlock $block): JsonResponse
    {
        abort_if($block->project->user_id !== auth()->id(), 403);

        $task = $block->tasks()->create(array_merge(
            $request->validated(),
            ['project_id' => $block->project_id]
        ));

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function show(Task $task): JsonResponse
    {
        abort_if($task->project->user_id !== auth()->id(), 403);

        return (new TaskResource($task))->response();
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        abort_if($task->project->user_id !== auth()->id(), 403);

        $task->update($request->validated());

        return (new TaskResource($task))->response();
    }

    public function destroy(Task $task): JsonResponse
    {
        abort_if($task->project->user_id !== auth()->id(), 403);

        $task->delete();

        return response()->json(null, 204);
    }

    public function complete(Task $task): JsonResponse
    {
        abort_if($task->project->user_id !== auth()->id(), 403);

        $task->completed_at = $task->completed_at === null ? now() : null;
        $task->save();

        return (new TaskResource($task))->response();
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        abort_if($task->project->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'schedule_block_id' => ['nullable', 'uuid'],
        ]);

        $task->update($validated);

        return (new TaskResource($task))->response();
    }
}
