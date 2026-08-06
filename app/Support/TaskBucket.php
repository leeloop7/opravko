<?php

namespace App\Support;

use App\Models\Task;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TaskBucket
{
    /**
     * @param  Collection<int, Task>  $tasks
     * @return list<array{key: string, title: string, tone: string, tasks: Collection<int, Task>}>
     */
    public static function group(Collection $tasks, ?CarbonInterface $now = null): array
    {
        $now = ($now ?? now())->timezone(config('app.timezone'))->startOfDay();
        $endOfWeek = $now->copy()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $startNextWeek = $endOfWeek->copy()->addDay();
        $endNextWeek = $startNextWeek->copy()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();
        $endOfMonth = $now->copy()->endOfMonth()->startOfDay();

        $meta = [
            'overdue' => ['title' => 'Zamujeno', 'tone' => 'overdue'],
            'this_week' => ['title' => 'Ta teden', 'tone' => 'september'],
            'next_week' => ['title' => 'Naslednji teden', 'tone' => 'august'],
            'this_month' => ['title' => 'Ta mesec', 'tone' => 'december'],
            'later' => ['title' => 'Kasneje', 'tone' => 'later'],
            'none' => ['title' => 'Brez roka', 'tone' => 'none'],
        ];

        $bucketTasks = [
            'overdue' => [],
            'this_week' => [],
            'next_week' => [],
            'this_month' => [],
            'later' => [],
            'none' => [],
        ];

        foreach ($tasks as $task) {
            $key = self::keyFor($task, $now, $endOfWeek, $endNextWeek, $endOfMonth);
            $bucketTasks[$key][] = $task;
        }

        $grouped = [];

        foreach ($bucketTasks as $key => $taskList) {
            if ($taskList === []) {
                continue;
            }

            $sorted = (new Collection($taskList))
                ->sortBy(fn (Task $task) => [
                    $task->isCompleted() ? 1 : 0,
                    $task->due_date === null ? PHP_INT_MAX : $task->due_date->getTimestamp(),
                    $task->sort_order,
                    $task->id,
                ])
                ->values();

            $grouped[] = [
                'key' => $key,
                'title' => $meta[$key]['title'],
                'tone' => $meta[$key]['tone'],
                'tasks' => $sorted,
            ];
        }

        return $grouped;
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
