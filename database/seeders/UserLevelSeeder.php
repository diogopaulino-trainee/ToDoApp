<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Level;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $levels = Level::orderBy('required_tasks')->get();

        foreach ($users as $user) {
            $completedTasks = $user->tasks()->where('completed', true)->count();

            $currentLevel = $levels->filter(fn ($level) => $completedTasks >= $level->required_tasks)->last();

            if ($currentLevel) {
                UserLevel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'level_id' => $currentLevel->id,
                        'animation_seen' => false,
                    ]
                );
            }
        }
    }
}
