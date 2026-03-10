<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Jobs\CallClaudeForProjectJob;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $projects = auth()->user()->projects()
            ->withCount([
                'tasks',
                'tasks as tasks_completed' => fn ($q) => $q->whereNotNull('completed_at'),
            ])
            ->latest()
            ->get();

        return ProjectResource::collection($projects)->response();
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project = auth()->user()->projects()->create($request->validated());

        return (new ProjectResource($project))->response()->setStatusCode(201);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $project->load([
            'scheduleBlocks' => function ($q) {
                $q->withCount([
                    'tasks',
                    'tasks as tasks_completed' => fn ($q) => $q->whereNotNull('completed_at'),
                ])->with(['tasks' => fn ($q) => $q->orderBy('order')])->orderBy('order');
            },
            'parameters',
        ]);

        return (new ProjectResource($project))->response();
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return (new ProjectResource($project))->response();
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'scope' => ['nullable', 'string'],
            'constraints' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'hours_per_day' => ['nullable', 'numeric', 'min:0.5', 'max:24'],
        ]);

        $project = DB::transaction(function () use ($validated) {
            $project = auth()->user()->projects()->create([
                'title' => $validated['title'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'status' => 'generating',
                'ai_generated' => true,
            ]);

            $parameterFields = ['description', 'scope', 'constraints', 'deadline', 'hours_per_day'];
            foreach ($parameterFields as $field) {
                if (isset($validated[$field]) && $validated[$field] !== null) {
                    $project->parameters()->create([
                        'key' => $field,
                        'value' => (string) $validated[$field],
                    ]);
                }
            }

            return $project;
        });

        CallClaudeForProjectJob::dispatch($project)->onQueue('ai');

        return (new ProjectResource($project))->response()->setStatusCode(202);
    }

    public function replan(Request $request, Project $project): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
