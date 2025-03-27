<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * Seed the application's database.
         */
        $this->call([
            UserSeeder::class,
            TaskSeeder::class,
            LevelSeeder::class,
            UserLevelSeeder::class,
        ]);
    }
}
