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
        $leon = User::query()->updateOrCreate(
            ['email' => 'leon@opravko.local'],
            [
                'name' => 'Leon',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $katja = User::query()->updateOrCreate(
            ['email' => 'katja@opravko.local'],
            [
                'name' => 'Katja',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $groups = [
            [
                'slug' => 'julij',
                'title' => 'Rok je bil 31. julija 2026',
                'due_date' => '2026-07-31',
                'tone' => 'overdue',
                'sort_order' => 1,
                'tasks' => [
                    ['slug' => 'baraka-vrata', 'title' => 'Popraviti vrata oziroma okvir na baraki'],
                    ['slug' => 'zar', 'title' => 'Pospraviti žar iz male sobe v barako', 'done' => true, 'by' => 'leon'],
                    ['slug' => 'kuhinjske-police', 'title' => 'Pripraviti načrt dodatnih polic v kuhinji'],
                    ['slug' => 'fotografije', 'title' => 'Kupiti okvirje in natisniti fotografije otrok za stopnišče'],
                    ['slug' => 'peskovnik-mreza', 'title' => 'Pripraviti igrala za peskovnik in visečo mrežo'],
                    ['slug' => 'vizualizacija-sten', 'title' => 'Pripraviti vizualizacijo sten za beljenje oziroma barvanje'],
                    ['slug' => 'luc-dnevna', 'title' => 'Urediti luč v dnevni sobi', 'done' => true, 'by' => 'katja'],
                    ['slug' => 'stekla-avtomobila', 'title' => 'Očistiti notranjo stran stekel v obeh avtomobilih'],
                ],
            ],
            [
                'slug' => 'avgust',
                'title' => 'Do 15. avgusta 2026',
                'due_date' => '2026-08-15',
                'tone' => 'august',
                'sort_order' => 2,
                'tasks' => [
                    ['slug' => 'okvircek-vhodna-vrata', 'title' => 'Popraviti okvirček na vhodnih vratih'],
                ],
            ],
            [
                'slug' => 'september',
                'title' => 'Do 30. septembra 2026',
                'due_date' => '2026-09-30',
                'tone' => 'september',
                'sort_order' => 3,
                'tasks' => [
                    ['slug' => 'balkon', 'title' => 'Položiti ploščice na balkonu'],
                    ['slug' => 'igrala', 'title' => 'Dokončati osnovna igrala: platformo, tobogan, plezalno steno in stopnice'],
                    ['slug' => 'kopalnica', 'title' => 'Pobeliti spodnjo kopalnico'],
                    ['slug' => 'lan-cevi', 'title' => 'Zapreti cevi v Lanovi sobi z mavčno ploščo in profili'],
                    ['slug' => 'otroska-oprema', 'title' => 'Umiti, fotografirati in prodati vozičke ter stole za dojenčke'],
                    ['slug' => 'spalnica-luci', 'title' => 'Najti ustrezni luči za spalnico ob postelji'],
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
                    ['slug' => 'majeva-soba', 'title' => 'Pripraviti načrt Majove mladinske sobe za šolo'],
                    ['slug' => 'preostale-stene', 'title' => 'Pobeliti oziroma prebarvati preostale stene'],
                    ['slug' => 'zakljucki-vrat', 'title' => 'Popraviti zaključke okoli vrat'],
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

            $group = TaskGroup::query()->updateOrCreate(
                ['slug' => $groupData['slug']],
                $groupData,
            );

            foreach ($tasks as $index => $taskData) {
                $assignee = isset($taskData['assigned']) ? $users[$taskData['assigned']] : null;
                $completer = isset($taskData['by']) ? $users[$taskData['by']] : null;
                $done = (bool) ($taskData['done'] ?? false);

                // firstOrCreate: ne prepiši assignee/completed stanja, ki ga uporabnika že spremenita.
                Task::query()->firstOrCreate(
                    ['slug' => $taskData['slug']],
                    [
                        'task_group_id' => $group->id,
                        'title' => $taskData['title'],
                        'due_date' => $groupData['due_date'],
                        'assigned_to' => $assignee?->id,
                        'created_by' => $leon->id,
                        'completed_by' => $done ? $completer?->id : null,
                        'completed_at' => $done ? now()->subDays(3) : null,
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }
    }
}
