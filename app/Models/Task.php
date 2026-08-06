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
 * @property Carbon|null $due_date
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
        'due_date',
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
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<TaskGroup, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'task_group_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /** @return HasMany<Rating, $this> */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /** @return HasOne<Rating, $this> */
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
            // Če ni bilo assigneeja, postane zaključnik tudi izvajalec.
            'assigned_to' => $this->assigned_to ?? $user->id,
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

    /**
     * Koga lahko oceni $rater:
     * - zaključnik oceni assigneeja, če je bil izvajalec kdo drug
     * - partner oceni tistega, ki je nalogo zaključil
     */
    public function rateeFor(User $rater): ?User
    {
        if (! $this->isCompleted()) {
            return null;
        }

        if (
            $this->completed_by === $rater->id
            && $this->assigned_to
            && $this->assigned_to !== $rater->id
        ) {
            return $this->assignee;
        }

        if ($this->completed_by && $this->completed_by !== $rater->id) {
            return $this->completer;
        }

        return null;
    }

    public function canBeRatedBy(User $user): bool
    {
        return $this->rateeFor($user) !== null;
    }
}
