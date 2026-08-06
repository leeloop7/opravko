<?php

namespace App\Livewire;

use App\Models\Rating;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::plan')]
#[Title('Hišni plan 2026')]
class HouseholdPlan extends Component
{
    public string $filter = 'all';

    public bool $showCreateForm = false;

    public string $newTitle = '';

    public ?int $newGroupId = null;

    public ?int $newAssignedTo = null;

    public ?int $ratingTaskId = null;

    public int $ratingScore = 5;

    public string $ratingNote = '';

    public function mount(): void
    {
        $this->newGroupId = TaskGroup::query()->orderBy('sort_order')->value('id');
    }

    public function setFilter(string $filter): void
    {
        if (! in_array($filter, ['all', 'open', 'done', 'mine'], true)) {
            return;
        }

        $this->filter = $filter;
    }

    public function toggleTask(int $taskId): void
    {
        $task = Task::query()->findOrFail($taskId);
        $user = Auth::user();

        if ($task->isCompleted()) {
            $task->markOpen();
        } else {
            $task->markCompletedBy($user);
        }

        unset($this->groups, $this->totals);
    }

    public function assignTask(int $taskId, mixed $userId = null): void
    {
        $task = Task::query()->findOrFail($taskId);

        $userId = $userId === '' || $userId === null ? null : (int) $userId;

        if ($userId !== null) {
            User::query()->findOrFail($userId);
        }

        $task->update(['assigned_to' => $userId]);

        unset($this->groups, $this->totals);
    }

    public function openCreateForm(): void
    {
        $this->showCreateForm = true;
        $this->resetValidation();
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->reset('newTitle', 'newAssignedTo');
        $this->resetValidation();
    }

    public function createTask(): void
    {
        $this->validate([
            'newTitle' => 'required|string|min:3|max:255',
            'newGroupId' => 'required|exists:task_groups,id',
            'newAssignedTo' => 'nullable|exists:users,id',
        ]);

        $sortOrder = (int) Task::query()
            ->where('task_group_id', $this->newGroupId)
            ->max('sort_order') + 1;

        $baseSlug = Str::slug(Str::limit($this->newTitle, 60, ''));
        $slug = $baseSlug !== '' ? $baseSlug : 'naloga';
        $original = $slug;
        $i = 2;

        while (Task::query()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i;
            $i++;
        }

        Task::query()->create([
            'task_group_id' => $this->newGroupId,
            'title' => trim($this->newTitle),
            'slug' => $slug,
            'assigned_to' => $this->newAssignedTo,
            'created_by' => Auth::id(),
            'sort_order' => $sortOrder,
        ]);

        $this->closeCreateForm();
        unset($this->groups, $this->totals);
    }

    public function openRating(int $taskId): void
    {
        $task = Task::query()->with('completer')->findOrFail($taskId);

        abort_unless($task->isCompleted(), 403);
        abort_unless($task->completed_by !== Auth::id(), 403);

        $existing = Rating::query()
            ->where('task_id', $task->id)
            ->where('rater_id', Auth::id())
            ->first();

        $this->ratingTaskId = $task->id;
        $this->ratingScore = $existing?->score ?? 5;
        $this->ratingNote = $existing?->note ?? '';
        $this->resetValidation();
    }

    public function closeRating(): void
    {
        $this->ratingTaskId = null;
        $this->ratingScore = 5;
        $this->ratingNote = '';
        $this->resetValidation();
    }

    public function saveRating(): void
    {
        $this->validate([
            'ratingTaskId' => 'required|exists:tasks,id',
            'ratingScore' => 'required|integer|min:1|max:5',
            'ratingNote' => 'nullable|string|max:255',
        ]);

        $task = Task::query()->findOrFail($this->ratingTaskId);

        abort_unless($task->isCompleted() && $task->completed_by, 403);
        abort_unless($task->completed_by !== Auth::id(), 403);

        Rating::query()->updateOrCreate(
            [
                'task_id' => $task->id,
                'rater_id' => Auth::id(),
            ],
            [
                'ratee_id' => $task->completed_by,
                'score' => $this->ratingScore,
                'note' => trim($this->ratingNote) !== '' ? trim($this->ratingNote) : null,
            ],
        );

        $this->closeRating();
        unset($this->groups, $this->totals);
    }

    #[Computed]
    public function users(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    #[Computed]
    public function groupOptions(): Collection
    {
        return TaskGroup::query()->orderBy('sort_order')->get();
    }

    #[Computed]
    public function groups(): Collection
    {
        $userId = Auth::id();

        return TaskGroup::query()
            ->with([
                'tasks.assignee',
                'tasks.completer',
                'tasks.ratings.rater',
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function (TaskGroup $group) use ($userId) {
                $tasks = $group->tasks->filter(function (Task $task) use ($userId) {
                    return match ($this->filter) {
                        'open' => ! $task->isCompleted(),
                        'done' => $task->isCompleted(),
                        'mine' => $task->assigned_to === $userId,
                        default => true,
                    };
                })->values();

                $done = $group->tasks->filter->isCompleted()->count();
                $total = $group->tasks->count();

                return [
                    'model' => $group,
                    'tasks' => $tasks,
                    'done' => $done,
                    'total' => $total,
                    'tone' => $group->due_date->isPast() && $done < $total ? 'overdue' : $group->tone,
                ];
            });
    }

    #[Computed]
    public function totals(): array
    {
        $tasks = Task::query()->get();

        return [
            'done' => $tasks->filter->isCompleted()->count(),
            'total' => $tasks->count(),
        ];
    }

    #[Computed]
    public function ratingTask(): ?Task
    {
        if (! $this->ratingTaskId) {
            return null;
        }

        return Task::query()->with('completer')->find($this->ratingTaskId);
    }

    public function render()
    {
        return view('livewire.household-plan');
    }
}
