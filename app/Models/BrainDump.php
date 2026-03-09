<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrainDump extends Model
{
    use HasFactory, HasUuids, MassPrunable;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'content',
        'processed',
        'linked_project_id',
    ];

    protected function casts(): array
    {
        return [
            'processed' => 'boolean',
        ];
    }

    public function prunable(): Builder
    {
        return static::where('processed', true)->where('created_at', '<=', now()->subDays(30));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'linked_project_id');
    }
}
