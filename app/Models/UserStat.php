<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStat extends Model
{
    use HasFactory, HasUuids;

    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'total_projects_completed',
        'total_tasks_completed',
        'total_habits_logged',
        'current_task_streak',
        'avg_estimate_accuracy',
        'planning_score',
    ];

    protected function casts(): array
    {
        return [
            'avg_estimate_accuracy' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
