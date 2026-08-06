<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $task_group_id
 * @property string $title
 * @property string $slug
 * @property int|null $assigned_to
 * @property int|null $created_by
 * @property int|null $completed_by
 * @property Carbon|null $completed_at
 * @property int $sort_order
 */
class Task extends Model
{
    protected $fillable = [
        'task_group_id',
        'title',
        'slug',
        'assigned_to',
        'created_by',
        'completed_by',
        'completed_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'task_group_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function ratingBy(User $user): HasOne
    {
        return $this->hasOne(Rating::class)->where('rater_id', $user->id);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function markCompletedBy(User $user): void
    {
        $this->forceFill([
            'completed_at' => now(),
            'completed_by' => $user->id,
        ])->save();
    }

    public function markOpen(): void
    {
        $this->forceFill([
            'completed_at' => null,
            'completed_by' => null,
        ])->save();

        $this->ratings()->delete();
    }
}
