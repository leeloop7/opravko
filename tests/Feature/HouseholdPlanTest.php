<?php

use App\Livewire\HouseholdPlan;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(\Database\Seeders\HouseholdPlanSeeder::class);
});

test('guests are redirected from the plan page', function () {
    $this->get(route('plan'))->assertRedirect();
});

test('authenticated users can view the household plan', function () {
    $user = User::query()->where('email', 'leon@opravko.local')->firstOrFail();

    $this->actingAs($user)
        ->get(route('plan'))
        ->assertOk()
        ->assertSee('Hišni plan 2026')
        ->assertSee('Popraviti vrata oziroma okvir na baraki');
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

test('a user can create and assign a task', function () {
    $leon = User::query()->where('email', 'leon@opravko.local')->firstOrFail();
    $katja = User::query()->where('email', 'katja@opravko.local')->firstOrFail();
    $groupId = Task::query()->firstOrFail()->task_group_id;

    Livewire::actingAs($leon)
        ->test(HouseholdPlan::class)
        ->set('newTitle', 'Kupiti vijake za balkon')
        ->set('newGroupId', $groupId)
        ->set('newAssignedTo', $katja->id)
        ->call('createTask')
        ->assertHasNoErrors();

    $task = Task::query()->where('title', 'Kupiti vijake za balkon')->first();

    expect($task)->not->toBeNull()
        ->and($task->assigned_to)->toBe($katja->id)
        ->and($task->created_by)->toBe($leon->id);
});

test('partner can rate a completed task', function () {
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
