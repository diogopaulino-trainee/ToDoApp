<?php

namespace Database\Seeders;

use App\Models\SubTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $completedCounts = [14, 35, 15, 5, 0];

        $users->each(function ($user, $index) use ($completedCounts) {
            $completedCount = $completedCounts[$index] ?? rand(0, 50);

            $tasks = Task::factory(50)->create([
                'user_id' => $user->id,
            ]);

            $tasks->each(function ($task, $i) use ($completedCount) {
                $task->update([
                    'completed' => $i < $completedCount,
                ]);

                SubTask::factory(rand(1, 3))->create([
                    'task_id' => $task->id,
                    'completed' => $i < $completedCount,
                ]);
            });
        });
    }
}
