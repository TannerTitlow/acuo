<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReplanHistory extends Model
{
    use HasFactory, HasUuids, MassPrunable;

    const UPDATED_AT = null;

    protected $fillable = [
        'project_id',
        'triggered_by',
        'original_blocks',
        'new_blocks',
        'tasks_moved',
        'tasks_completed_at_time',
    ];

    protected function casts(): array
    {
        return [
            'original_blocks' => 'array',
            'new_blocks' => 'array',
        ];
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
