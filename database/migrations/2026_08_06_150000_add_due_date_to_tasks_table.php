<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('title');
        });

        if (Schema::hasTable('task_groups')) {
            $groups = DB::table('task_groups')->pluck('due_date', 'id');

            foreach ($groups as $groupId => $dueDate) {
                DB::table('tasks')
                    ->where('task_group_id', $groupId)
                    ->update(['due_date' => $dueDate]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
