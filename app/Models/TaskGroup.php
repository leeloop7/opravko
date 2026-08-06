<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property Carbon $due_date
 * @property string $tone
 * @property int $sort_order
 */
class TaskGroup extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'due_date',
        'tone',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('sort_order');
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->tasks()->whereNull('completed_at')->exists();
    }
}
