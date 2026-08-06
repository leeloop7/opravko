<?php

namespace App\Support;

use App\Models\Task;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TaskBucket
{
    /**
     * @return list<array{key: string, title: string, tone: string, tasks: Collection<int, Task>}>
     */
    public static function group(Collection $tasks, ?CarbonInterface $now = null): array
    {
        $now = ($now ?? now())->timezone(config('app.timezone'))->startOfDay();
        $endOfWeek = $now->copy()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $startNextWeek = $endOfWeek->copy()->addDay();
        $endNextWeek = $startNextWeek->copy()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $endOfMonth = $now->copy()->endOfMonth()->startOfDay();

        $buckets = [
            'overdue' => ['title' => 'Zamujeno', 'tone' => 'overdue', 'tasks' => collect()],
            'this_week' => ['title' => 'Ta teden', 'tone' => 'september', 'tasks' => collect()],
            'next_week' => ['title' => 'Naslednji teden', 'tone' => 'august', 'tasks' => collect()],
            'this_month' => ['title' => 'Ta mesec', 'tone' => 'december', 'tasks' => collect()],
            'later' => ['title' => 'Kasneje', 'tone' => 'later', 'tasks' => collect()],
            'none' => ['title' => 'Brez roka', 'tone' => 'none', 'tasks' => collect()],
        ];

        foreach ($tasks as $task) {
            $key = self::keyFor($task, $now, $endOfWeek, $endNextWeek, $endOfMonth);
            $buckets[$key]['tasks']->push($task);
        }

        foreach ($buckets as $key => $bucket) {
            $buckets[$key]['tasks'] = $bucket['tasks']
                ->sortBy(fn (Task $task) => [
                    $task->isCompleted() ? 1 : 0,
                    $task->due_date?->timestamp ?? PHP_INT_MAX,
                    $task->sort_order,
                    $task->id,
                ])
                ->values();
        }

        return collect($buckets)
            ->map(fn (array $bucket, string $key) => [
                'key' => $key,
                'title' => $bucket['title'],
                'tone' => $bucket['tone'],
                'tasks' => $bucket['tasks'],
            ])
            ->filter(fn (array $bucket) => $bucket['tasks']->isNotEmpty())
            ->values()
            ->all();
    }

    public static function keyFor(
        Task $task,
        ?CarbonInterface $now = null,
        ?CarbonInterface $endOfWeek = null,
        ?CarbonInterface $endNextWeek = null,
        ?CarbonInterface $endOfMonth = null,
    ): string {
        $now = ($now ?? now())->timezone(config('app.timezone'))->startOfDay();
        $endOfWeek ??= $now->copy()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $endNextWeek ??= $endOfWeek->copy()->addDay()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $endOfMonth ??= $now->copy()->endOfMonth()->startOfDay();

        if ($task->due_date === null) {
            return 'none';
        }

        $due = $task->due_date->timezone(config('app.timezone'))->startOfDay();

        if ($due->lt($now) && ! $task->isCompleted()) {
            return 'overdue';
        }

        if ($due->lt($now) && $task->isCompleted()) {
            // Končane zamujene naloge držimo v "ta teden"/preteklosti blizu —
            // da ne nabrekajo v "Zamujeno".
            return 'this_week';
        }

        if ($due->lte($endOfWeek)) {
            return 'this_week';
        }

        if ($due->lte($endNextWeek)) {
            return 'next_week';
        }

        if ($due->lte($endOfMonth)) {
            return 'this_month';
        }

        return 'later';
    }
}
