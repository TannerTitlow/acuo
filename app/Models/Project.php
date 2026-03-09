<?php

namespace App\Models;

use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ProjectObserver::class])]
class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'subcategory',
        'description',
        'deadline',
        'status',
        'ai_generated',
        'last_replanned_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'ai_generated' => 'boolean',
            'last_replanned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(ProjectParameter::class);
    }

    public function scheduleBlocks(): HasMany
    {
        return $this->hasMany(ScheduleBlock::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function brainDumps(): HasMany
    {
        return $this->hasMany(BrainDump::class, 'linked_project_id');
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class, 'linked_project_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function replanHistories(): HasMany
    {
        return $this->hasMany(ReplanHistory::class);
    }
}
