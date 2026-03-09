<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\BrainDumpController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScheduleBlockController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UnlockableController;
use App\Http\Controllers\XpController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Illuminate\Http\Request $r) => $r->user());

    Route::post('projects/generate', [ProjectController::class, 'generate']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects.blocks', ScheduleBlockController::class)->shallow();
    Route::apiResource('blocks.tasks', TaskController::class)->shallow();
    Route::apiResource('habits', HabitController::class);
    Route::apiResource('brain-dumps', BrainDumpController::class);

    Route::post('projects/{project}/replan', [ProjectController::class, 'replan']);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete']);
    Route::patch('tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::patch('blocks/{block}/snooze', [ScheduleBlockController::class, 'snooze']);
    Route::post('habits/{habit}/log', [HabitController::class, 'log']);
    Route::post('brain-dumps/{brainDump}/process', [BrainDumpController::class, 'process']);

    Route::get('xp/summary', [XpController::class, 'summary']);
    Route::get('achievements', [AchievementController::class, 'index']);
    Route::patch('achievements/{userAchievement}/seen', [AchievementController::class, 'markSeen']);
    Route::get('unlockables', [UnlockableController::class, 'index']);
    Route::post('unlockables/{unlockable}/activate', [UnlockableController::class, 'activate']);
    Route::get('stats/overview', [StatsController::class, 'overview']);
    Route::get('stats/planning-score', [StatsController::class, 'planningScore']);
});
