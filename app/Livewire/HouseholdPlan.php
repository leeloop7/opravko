<?php

namespace App\Livewire;

use App\Models\Rating;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\User;
use App\Support\TaskBucket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::plan')]
#[Title('Opravko')]
class HouseholdPlan extends Component
{
    public string $filter = 'all';

    public bool $showCreateForm = false;

    public ?int $editingTaskId = null;

    public string $newTitle = '';

    public ?string $newDueDate = null;

    public ?int $newAssignedTo = null;

    public ?int $ratingTaskId = null;

    public int $ratingScore = 5;

    public string $ratingNote = '';

    /** @var array<int, string> */
    public array $collapsedGroups = [];

    public function mount(): void
    {
        $this->newDueDate = now()->addDays(7)->toDateString();
        $this->syncCollapsedDefaults();
    }

    public function setFilter(string $filter): void
    {
        if (! in_array($filter, ['all', 'open', 'done', 'mine'], true)) {
            return;
        }

        $this->filter = $filter;
    }

    public function toggleGroup(string $groupKey): void
    {
        if (in_array($groupKey, $this->collapsedGroups, true)) {
            $this->collapsedGroups = array_values(array_filter(
                $this->collapsedGroups,
                fn (string $key) => $key !== $groupKey,
            ));

            return;
        }

        $this->collapsedGroups[] = $groupKey;
    }

    public function isGroupOpen(string $groupKey): bool
    {
        return ! in_array($groupKey, $this->collapsedGroups, true);
    }

    public function setDuePreset(string $preset): void
    {
        $today = now()->timezone(config('app.timezone'))->startOfDay();

        $this->newDueDate = match ($preset) {
            '7' => $today->copy()->addDays(7)->toDateString(),
            '14' => $today->copy()->addDays(14)->toDateString(),
            'week' => $today->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            'month' => $today->copy()->endOfMonth()->toDateString(),
            'none' => null,
            default => $this->newDueDate,
        };
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
        $this->syncCollapsedDefaults(keepManual: true);
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
        $this->editingTaskId = null;
        $this->showCreateForm = true;
        $this->ratingTaskId = null;
        $this->newTitle = '';
        $this->newAssignedTo = null;
        $this->newDueDate = now()->addDays(7)->toDateString();
        $this->resetValidation();
    }

    public function openEditForm(int $taskId): void
    {
        $task = Task::query()->findOrFail($taskId);

        $this->editingTaskId = $task->id;
        $this->showCreateForm = true;
        $this->ratingTaskId = null;
        $this->newTitle = $task->title;
        $this->newDueDate = $task->due_date?->toDateString();
        $this->newAssignedTo = $task->assigned_to;
        $this->resetValidation();
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->editingTaskId = null;
        $this->reset('newTitle', 'newAssignedTo');
        $this->newDueDate = now()->addDays(7)->toDateString();
        $this->resetValidation();
    }

    public function saveTask(): void
    {
        $this->validate([
            'newTitle' => 'required|string|min:3|max:255',
            'newDueDate' => 'nullable|date',
            'newAssignedTo' => 'nullable|exists:users,id',
        ]);

        $inboxGroupId = TaskGroup::query()->orderBy('sort_order')->value('id')
            ?? TaskGroup::query()->create([
                'title' => 'Opravila',
                'slug' => 'opravila',
                'due_date' => now()->toDateString(),
                'tone' => 'september',
                'sort_order' => 1,
            ])->id;

        if ($this->editingTaskId) {
            $task = Task::query()->findOrFail($this->editingTaskId);

            $task->update([
                'title' => trim($this->newTitle),
                'due_date' => $this->newDueDate,
                'assigned_to' => $this->newAssignedTo,
            ]);
        } else {
            $sortOrder = (int) Task::query()->max('sort_order') + 1;

            $baseSlug = Str::slug(Str::limit($this->newTitle, 60, ''));
            $slug = $baseSlug !== '' ? $baseSlug : 'naloga';
            $original = $slug;
            $i = 2;

            while (Task::query()->where('slug', $slug)->exists()) {
                $slug = $original.'-'.$i;
                $i++;
            }

            Task::query()->create([
                'task_group_id' => $inboxGroupId,
                'title' => trim($this->newTitle),
                'due_date' => $this->newDueDate,
                'slug' => $slug,
                'assigned_to' => $this->newAssignedTo,
                'created_by' => Auth::id(),
                'sort_order' => $sortOrder,
            ]);
        }

        $this->closeCreateForm();
        unset($this->groups, $this->totals);
        $this->syncCollapsedDefaults(keepManual: true);
    }

    public function deleteTask(int $taskId): void
    {
        Task::query()->findOrFail($taskId)->delete();

        if ($this->editingTaskId === $taskId) {
            $this->closeCreateForm();
        }

        if ($this->ratingTaskId === $taskId) {
            $this->closeRating();
        }

        unset($this->groups, $this->totals);
    }

    public function openRating(int $taskId): void
    {
        $task = Task::query()->with(['assignee', 'completer'])->findOrFail($taskId);
        $user = Auth::user();

        abort_unless($task->canBeRatedBy($user), 403);

        $existing = Rating::query()
            ->where('task_id', $task->id)
            ->where('rater_id', $user->id)
            ->first();

        $this->showCreateForm = false;
        $this->editingTaskId = null;
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

        $task = Task::query()->with(['assignee', 'completer'])->findOrFail($this->ratingTaskId);
        $user = Auth::user();
        $ratee = $task->rateeFor($user);

        abort_unless($ratee !== null, 403);

        Rating::query()->updateOrCreate(
            [
                'task_id' => $task->id,
                'rater_id' => $user->id,
            ],
            [
                'ratee_id' => $ratee->id,
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
    public function groups(): Collection
    {
        $userId = Auth::id();

        $tasks = Task::query()
            ->with(['assignee', 'completer', 'ratings.rater', 'ratings.ratee'])
            ->orderBy('sort_order')
            ->get()
            ->filter(function (Task $task) use ($userId) {
                return match ($this->filter) {
                    'open' => ! $task->isCompleted(),
                    'done' => $task->isCompleted(),
                    'mine' => $task->assigned_to === $userId,
                    default => true,
                };
            })
            ->values();

        return collect(TaskBucket::group($tasks))->map(function (array $bucket) {
            $done = $bucket['tasks']->filter->isCompleted()->count();
            $total = $bucket['tasks']->count();

            return [
                'key' => $bucket['key'],
                'title' => $bucket['title'],
                'tone' => $bucket['tone'],
                'tasks' => $bucket['tasks'],
                'done' => $done,
                'total' => $total,
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

        return Task::query()->with(['assignee', 'completer'])->find($this->ratingTaskId);
    }

    #[Computed]
    public function ratingRatee(): ?User
    {
        $task = $this->ratingTask;

        if (! $task) {
            return null;
        }

        return $task->rateeFor(Auth::user());
    }

    public function render()
    {
        return view('livewire.household-plan');
    }

    protected function syncCollapsedDefaults(bool $keepManual = false): void
    {
        if ($keepManual && $this->collapsedGroups !== []) {
            return;
        }

        // Privzeto strni "Kasneje" in "Brez roka", če obstajata.
        $this->collapsedGroups = collect($this->groups)
            ->pluck('key')
            ->filter(fn (string $key) => in_array($key, ['later', 'none'], true))
            ->values()
            ->all();
    }
}
