<?php

use App\Livewire\HouseholdPlan;
use App\Models\Task;
use App\Models\User;
use App\Support\TaskBucket;
use Database\Seeders\HouseholdPlanSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(HouseholdPlanSeeder::class);
});

test('guests are redirected from the plan page', function () {
    $this->get(route('plan'))->assertRedirect();
});

test('authenticated users can view the household plan', function () {
    $user = User::query()->where('email', 'leon@opravko.local')->firstOrFail();

    $this->actingAs($user)
        ->get(route('plan'))
        ->assertOk()
        ->assertSee('Opravko', false)
        ->assertSee('Nova naloga')
        ->assertSee('Popraviti vrata oziroma okvir na baraki')
        ->assertSee('Zamujeno')
        ->assertSee('Ta teden', false);
});

test('a user can complete and reopen a task', function () {
    $user = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    Livewire::actingAs($user)
        ->test(HouseholdPlan::class)
        ->call('toggleTask', $task->id)
        ->assertSuccessful();

    expect($task->fresh()->isCompleted())->toBeTrue()
        ->and($task->fresh()->completed_by)->toBe($user->id);

    Livewire::actingAs($user)
        ->test(HouseholdPlan::class)
        ->call('toggleTask', $task->id)
        ->assertSuccessful();

    expect($task->fresh()->isCompleted())->toBeFalse();
});

test('a user can create and assign a task with due date', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->set('newTitle', 'Kupiti vijake za balkon')
        ->set('newDueDate', now()->addDays(7)->toDateString())
        ->set('newAssignedTo', $katja->id)
        ->call('saveTask')
        ->assertHasNoErrors();

    $task = Task::query()->where('title', 'Kupiti vijake za balkon')->first();

    expect($task)->not->toBeNull()
        ->and($task->assigned_to)->toBe($katja->id)
        ->and($task->created_by)->toBe($leon->id)
        ->and($task->due_date->toDateString())->toBe(now()->addDays(7)->toDateString());
});

test('partner can rate who finished the task', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    $task->markCompletedBy($leon);

    Livewire::actingAs($katja)
        ->test(HouseholdPlan::class)
        ->call('openRating', $task->id)
        ->set('ratingScore', 4)
        ->set('ratingNote', 'Super narejeno')
        ->call('saveRating')
        ->assertHasNoErrors();

    expect($task->ratings()->first())
        ->score->toBe(4)
        ->rater_id->toBe($katja->id)
        ->ratee_id->toBe($leon->id);
});

test('completer can rate the assignee when executor was someone else', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    $task->update(['assigned_to' => $katja->id]);
    $task->markCompletedBy($leon);

    expect($task->fresh()->canBeRatedBy($leon))->toBeTrue()
        ->and($task->fresh()->rateeFor($leon)?->id)->toBe($katja->id);

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->call('openRating', $task->id)
        ->set('ratingScore', 5)
        ->set('ratingNote', 'Katja je opravila odlično')
        ->call('saveRating')
        ->assertHasNoErrors();

    expect($task->ratings()->first())
        ->rater_id->toBe($leon->id)
        ->ratee_id->toBe($katja->id)
        ->score->toBe(5);
});

test('completer cannot rate themselves', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    $task->update(['assigned_to' => $leon->id]);
    $task->markCompletedBy($leon);

    expect($task->fresh()->canBeRatedBy($leon))->toBeFalse();
});

test('a user can edit a task', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->call('openEditForm', $task->id)
        ->set('newTitle', 'Položiti nove ploščice na balkonu')
        ->set('newDueDate', '2026-08-20')
        ->set('newAssignedTo', $katja->id)
        ->call('saveTask')
        ->assertHasNoErrors();

    expect($task->fresh())
        ->title->toBe('Položiti nove ploščice na balkonu')
        ->assigned_to->toBe($katja->id)
        ->and($task->fresh()->due_date->toDateString())->toBe('2026-08-20');
});

test('a user can delete a task', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();
    $taskId = $task->id;

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->call('deleteTask', $taskId)
        ->assertSuccessful();

    expect(Task::query()->find($taskId))->toBeNull();
});

test('task groups can be collapsed and expanded', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->call('toggleGroup', 'overdue')
        ->assertSet('collapsedGroups', fn ($groups) => in_array('overdue', $groups, true))
        ->call('toggleGroup', 'overdue')
        ->assertSet('collapsedGroups', fn ($groups) => ! in_array('overdue', $groups, true));
});

test('task assignment persists across remount and reseeding', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();
    $task = Task::query()->where('slug', 'balkon')->firstOrFail();

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->call('assignTask', $task->id, $katja->id)
        ->assertSuccessful();

    expect($task->fresh()->assigned_to)->toBe($katja->id);

    // Simulacija ponovnega seeda — assignee mora ostati.
    $this->seed(HouseholdPlanSeeder::class);

    expect($task->fresh()->assigned_to)->toBe($katja->id);

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->assertSee('Za:')
        ->assertSee('Katja');
});

test('tasks are bucketed by due date', function () {
    $now = now()->timezone(config('app.timezone'))->startOfDay();

    $overdue = Task::query()->make(['due_date' => $now->copy()->subDay(), 'completed_at' => null]);
    $thisWeek = Task::query()->make(['due_date' => $now->copy()->addDays(2), 'completed_at' => null]);
    $later = Task::query()->make(['due_date' => $now->copy()->addMonths(2), 'completed_at' => null]);
    $none = Task::query()->make(['due_date' => null, 'completed_at' => null]);

    expect(TaskBucket::keyFor($overdue, $now))->toBe('overdue')
        ->and(TaskBucket::keyFor($thisWeek, $now))->toBe('this_week')
        ->and(TaskBucket::keyFor($later, $now))->toBe('later')
        ->and(TaskBucket::keyFor($none, $now))->toBe('none');
});
