<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HouseholdPlanSeeder extends Seeder
{
    public function run(): void
    {
        $leon = User::query()->firstOrCreate(
            ['email' => 'leon@opravko.local'],
            [
                'name' => 'Leon',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $katja = User::query()->firstOrCreate(
            ['email' => 'katja@opravko.local'],
            [
                'name' => 'Katja',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // Če naloge že obstajajo, jih seeder ne sme več nastavljati / prepisovati.
        if (Task::query()->exists()) {
            return;
        }

        $groups = [
            [
                'slug' => 'julij',
                'title' => 'Rok je bil 31. julija 2026',
                'due_date' => '2026-07-31',
                'tone' => 'overdue',
                'sort_order' => 1,
                'tasks' => [
                    ['slug' => 'baraka-vrata', 'title' => 'Popraviti vrata oziroma okvir na baraki', 'assigned' => 'leon'],
                    ['slug' => 'zar', 'title' => 'Pospraviti žar iz male sobe v barako', 'assigned' => 'leon', 'done' => true, 'by' => 'leon'],
                    ['slug' => 'kuhinjske-police', 'title' => 'Pripraviti načrt dodatnih polic v kuhinji', 'assigned' => 'katja'],
                    ['slug' => 'fotografije', 'title' => 'Kupiti okvirje in natisniti fotografije otrok za stopnišče', 'assigned' => 'leon'],
                    ['slug' => 'peskovnik-mreza', 'title' => 'Pripraviti igrala za peskovnik in visečo mrežo', 'assigned' => 'leon', 'done' => true, 'by' => 'leon'],
                    ['slug' => 'vizualizacija-sten', 'title' => 'Pripraviti vizualizacijo sten za beljenje oziroma barvanje', 'assigned' => 'leon'],
                    ['slug' => 'luc-dnevna', 'title' => 'Urediti luč v dnevni sobi', 'assigned' => 'leon', 'done' => true, 'by' => 'leon'],
                    ['slug' => 'stekla-avtomobila', 'title' => 'Očistiti notranjo stran stekel v obeh avtomobilih', 'assigned' => 'katja'],
                ],
            ],
            [
                'slug' => 'avgust',
                'title' => 'Do 15. avgusta 2026',
                'due_date' => '2026-08-15',
                'tone' => 'august',
                'sort_order' => 2,
                'tasks' => [
                    ['slug' => 'okvircek-vhodna-vrata', 'title' => 'Popraviti okvirček na vhodnih vratih', 'assigned' => 'leon'],
                ],
            ],
            [
                'slug' => 'september',
                'title' => 'Do 30. septembra 2026',
                'due_date' => '2026-09-30',
                'tone' => 'september',
                'sort_order' => 3,
                'tasks' => [
                    ['slug' => 'balkon', 'title' => 'Položiti ploščice na balkonu', 'assigned' => 'leon'],
                    ['slug' => 'igrala', 'title' => 'Dokončati osnovna igrala: platformo, tobogan, plezalno steno in stopnice', 'assigned' => 'leon'],
                    ['slug' => 'kopalnica', 'title' => 'Pobeliti spodnjo kopalnico', 'assigned' => 'katja'],
                    ['slug' => 'lan-cevi', 'title' => 'Zapreti cevi v Lanovi sobi z mavčno ploščo in profili', 'assigned' => 'leon'],
                    ['slug' => 'otroska-oprema', 'title' => 'Umiti, fotografirati in prodati vozičke ter stole za dojenčke', 'assigned' => 'katja'],
                    ['slug' => 'spalnica-luci', 'title' => 'Najti ustrezni luči za spalnico ob postelji', 'assigned' => 'katja'],
                ],
            ],
            [
                'slug' => 'december',
                'title' => 'Do 31. decembra 2026',
                'due_date' => '2026-12-31',
                'tone' => 'december',
                'sort_order' => 4,
                'tasks' => [
                    ['slug' => 'katja-kolo', 'title' => 'Kupiti Katji novo kolo', 'assigned' => 'leon'],
                    ['slug' => 'majeva-soba', 'title' => 'Pripraviti načrt Majove mladinske sobe za šolo', 'assigned' => 'leon'],
                    ['slug' => 'preostale-stene', 'title' => 'Pobeliti oziroma prebarvati preostale stene', 'assigned' => 'katja'],
                    ['slug' => 'zakljucki-vrat', 'title' => 'Popraviti zaključke okoli vrat', 'assigned' => 'leon'],
                ],
            ],
        ];

        $users = [
            'leon' => $leon,
            'katja' => $katja,
        ];

        foreach ($groups as $groupData) {
            $tasks = $groupData['tasks'];
            unset($groupData['tasks']);

            $group = TaskGroup::query()->firstOrCreate(
                ['slug' => $groupData['slug']],
                $groupData,
            );

            foreach ($tasks as $index => $taskData) {
                $assignee = $users[$taskData['assigned']];
                $completer = isset($taskData['by']) ? $users[$taskData['by']] : null;
                $done = (bool) ($taskData['done'] ?? false);

                Task::query()->create([
                    'slug' => $taskData['slug'],
                    'task_group_id' => $group->id,
                    'title' => $taskData['title'],
                    'due_date' => $groupData['due_date'],
                    'assigned_to' => $assignee->id,
                    'created_by' => $leon->id,
                    'completed_by' => $done ? $completer?->id : null,
                    'completed_at' => $done ? now()->subDays(3) : null,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
