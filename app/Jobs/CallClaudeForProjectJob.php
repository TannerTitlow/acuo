<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallClaudeForProjectJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Project $project) {}

    public function handle(): void
    {
        $this->project->refresh();

        if ($this->project->status !== 'generating') {
            return;
        }

        $this->project->load('parameters');

        try {
            $parameters = $this->project->parameters->pluck('value', 'key')->toArray();

            $userPrompt = $this->buildPrompt($parameters);

            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => config('services.anthropic.model'),
                'max_tokens' => 4096,
                'system' => 'You are a project planning assistant for people with ADHD. Create a detailed, structured project plan broken into focused work sessions. Return ONLY valid JSON with no additional text, markdown, or code blocks.',
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if ($response->failed()) {
                throw new \RuntimeException('Claude API request failed: '.$response->body());
            }

            $content = $response->json('content.0.text');
            $parsed = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($parsed['blocks'])) {
                throw new \RuntimeException('Invalid JSON response from Claude: '.$content);
            }

            DB::transaction(function () use ($parsed) {
                foreach ($parsed['blocks'] as $blockIndex => $blockData) {
                    $block = $this->project->scheduleBlocks()->create([
                        'title' => $blockData['title'],
                        'description' => $blockData['description'] ?? null,
                        'scheduled_date' => $blockData['scheduled_date'],
                        'order' => $blockData['order'] ?? $blockIndex,
                        'estimated_minutes' => $blockData['estimated_minutes'] ?? 60,
                    ]);

                    foreach ($blockData['tasks'] ?? [] as $taskIndex => $taskData) {
                        $this->project->tasks()->create([
                            'schedule_block_id' => $block->id,
                            'title' => $taskData['title'],
                            'order' => $taskData['order'] ?? $taskIndex,
                            'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                        ]);
                    }
                }

                $this->project->update(['status' => 'active']);
            });
        } catch (\Throwable $e) {
            Log::error('CallClaudeForProjectJob failed', [
                'project_id' => $this->project->id,
                'error' => $e->getMessage(),
            ]);

            $this->project->update(['status' => 'active']);
        }
    }

    private function buildPrompt(array $parameters): string
    {
        $title = $this->project->title;
        $category = $this->project->category;
        $description = $parameters['description'] ?? $this->project->description ?? '';
        $scope = $parameters['scope'] ?? null;
        $constraints = $parameters['constraints'] ?? null;
        $deadline = $parameters['deadline'] ?? ($this->project->deadline?->format('Y-m-d'));
        $hoursPerDay = $parameters['hours_per_day'] ?? 2;

        $prompt = "Create a project plan for:\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Category: {$category}\n";
        $prompt .= "Description: {$description}\n";

        if ($scope) {
            $prompt .= "Scope: {$scope}\n";
        }

        if ($constraints) {
            $prompt .= "Constraints: {$constraints}\n";
        }

        if ($deadline) {
            $prompt .= "Deadline: {$deadline}\n";
        }

        $prompt .= "Available hours per day: {$hoursPerDay}\n";
        $prompt .= 'Today\'s date: '.now()->format('Y-m-d')."\n\n";
        $prompt .= 'Return a JSON object with a "blocks" array. Each block is a focused work session with: title (string), description (string), scheduled_date (YYYY-MM-DD), order (integer from 0), estimated_minutes (integer), tasks (array). Each task has: title (string), order (integer from 0), estimated_minutes (integer or null). Keep sessions 30-90 minutes with specific, concrete tasks suited for ADHD focus.';

        return $prompt;
    }
}
