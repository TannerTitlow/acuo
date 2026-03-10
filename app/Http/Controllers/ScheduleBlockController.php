<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleBlockRequest;
use App\Http\Requests\UpdateScheduleBlockRequest;
use App\Http\Resources\ScheduleBlockResource;
use App\Models\Project;
use App\Models\ScheduleBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleBlockController extends Controller
{
    private function authorizeBlock(ScheduleBlock $block): void
    {
        abort_if($block->project->user_id !== auth()->id(), 403);
    }

    public function index(Project $project): JsonResponse
    {
        abort_if($project->user_id !== auth()->id(), 403);

        $blocks = $project->scheduleBlocks()
            ->withCount([
                'tasks',
                'tasks as tasks_completed' => fn ($q) => $q->whereNotNull('completed_at'),
            ])
            ->orderBy('order')
            ->get();

        return ScheduleBlockResource::collection($blocks)->response();
    }

    public function store(StoreScheduleBlockRequest $request, Project $project): JsonResponse
    {
        abort_if($project->user_id !== auth()->id(), 403);

        $block = $project->scheduleBlocks()->create($request->validated());

        return (new ScheduleBlockResource($block))->response()->setStatusCode(201);
    }

    public function show(ScheduleBlock $block): JsonResponse
    {
        $this->authorizeBlock($block);

        $block->loadCount([
            'tasks',
            'tasks as tasks_completed' => fn ($q) => $q->whereNotNull('completed_at'),
        ])->load(['tasks' => fn ($q) => $q->orderBy('order')]);

        return (new ScheduleBlockResource($block))->response();
    }

    public function update(UpdateScheduleBlockRequest $request, ScheduleBlock $block): JsonResponse
    {
        $this->authorizeBlock($block);

        $block->update($request->validated());

        return (new ScheduleBlockResource($block))->response();
    }

    public function destroy(ScheduleBlock $block): JsonResponse
    {
        $this->authorizeBlock($block);

        $block->delete();

        return response()->json(null, 204);
    }

    public function snooze(Request $request, ScheduleBlock $block): JsonResponse
    {
        $this->authorizeBlock($block);

        $request->validate(['snoozed_to' => ['required', 'date']]);

        $block->update(['snoozed_to' => $request->snoozed_to]);

        return (new ScheduleBlockResource($block))->response();
    }
}
