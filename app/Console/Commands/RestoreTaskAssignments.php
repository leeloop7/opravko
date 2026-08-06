<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class RestoreTaskAssignments extends Command
{
    protected $signature = 'opravko:restore-assignments';

    protected $description = 'Restore Leon/Katja task assignments and completion state from the known household snapshot';

    public function handle(): int
    {
        $leon = User::query()->where('email', 'leon@opravko.local')->first();
        $katja = User::query()->where('email', 'katja@opravko.local')->first();

        if (! $leon || ! $katja) {
            $this->error('Leon/Katja users are missing. Seed users first.');

            return self::FAILURE;
        }

        $snapshot = [
            'baraka-vrata' => ['assigned' => $leon->id],
            'zar' => ['assigned' => $leon->id, 'completed_by' => $leon->id, 'completed_at' => now()->subDays(3)],
            'kuhinjske-police' => ['assigned' => $katja->id],
            'fotografije' => ['assigned' => $leon->id],
            'peskovnik-mreza' => ['assigned' => $leon->id, 'completed_by' => $leon->id, 'completed_at' => now()->subDays(3)],
            'vizualizacija-sten' => ['assigned' => $leon->id],
            'luc-dnevna' => ['assigned' => $leon->id, 'completed_by' => $leon->id, 'completed_at' => now()->subDays(3)],
            'stekla-avtomobila' => ['assigned' => $katja->id],
            'okvircek-vhodna-vrata' => ['assigned' => $leon->id],
            'balkon' => ['assigned' => $leon->id],
            'igrala' => ['assigned' => $leon->id],
            'kopalnica' => ['assigned' => $katja->id],
            'lan-cevi' => ['assigned' => $leon->id],
            'otroska-oprema' => ['assigned' => $katja->id],
            'spalnica-luci' => ['assigned' => $katja->id],
            'katja-kolo' => ['assigned' => $leon->id],
            'majeva-soba' => ['assigned' => $leon->id],
            'preostale-stene' => ['assigned' => $katja->id],
            'zakljucki-vrat' => ['assigned' => $leon->id],
        ];

        $updated = 0;

        foreach ($snapshot as $slug => $state) {
            $task = Task::query()->where('slug', $slug)->first();

            if (! $task) {
                $this->warn("Missing task: {$slug}");

                continue;
            }

            $task->forceFill([
                'assigned_to' => $state['assigned'],
                'completed_by' => $state['completed_by'] ?? null,
                'completed_at' => $state['completed_at'] ?? null,
            ])->save();

            $updated++;
        }

        $this->info("Restored assignments on {$updated} tasks.");

        return self::SUCCESS;
    }
}
