<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'completed'   => $this->faker->boolean(),
            'due_date'    => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'priority'    => $this->faker->randomElement(['low', 'medium', 'high']),
        ];
    }
}
