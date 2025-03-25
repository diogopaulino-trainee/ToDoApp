<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\SubTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SubTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('inertia.testing', true);
        \Inertia\Inertia::setRootView('testing');
    }

    public function test_store_multiple_subtasks()
    {
        Log::info('TEST: Begin test_store_multiple_subtasks');

        $user = User::factory()->create();
        $this->actingAs($user);

        Log::info('Created user with ID: ' . $user->id);

        $task = Task::factory()->create(['user_id' => $user->id]);

        Log::info('Created task with ID: ' . $task->id);

        $payload = [
            'titles' => ['First subtask', 'Second subtask'],
            'task_id' => $task->id,
        ];

        Log::info('Sending POST request to store subtasks', $payload);

        $response = $this->postJson(route('subtasks.store', ['task' => $task->id]), $payload);

        $response->assertStatus(200);
        $this->assertCount(2, SubTask::where('task_id', $task->id)->get());

        Log::info('Subtasks successfully stored in database');
        Log::info('TEST: End test_store_multiple_subtasks');
    }

    public function test_update_subtask()
    {
        Log::info('TEST: Begin test_update_subtask');

        $user = User::factory()->create();
        $this->actingAs($user);

        Log::info('Created user with ID: ' . $user->id);

        $task = Task::factory()->create(['user_id' => $user->id]);
        $subtask = SubTask::create(['task_id' => $task->id, 'title' => 'Old Title']);

        Log::info('Created subtask with ID: ' . $subtask->id);

        $payload = ['title' => 'Updated Title'];

        Log::info('Sending PATCH request to update subtask', $payload);

        $response = $this->patchJson(route('subtasks.update', $subtask), $payload);

        $response->assertStatus(200);
        $this->assertEquals('Updated Title', $subtask->fresh()->title);

        Log::info('Subtask title successfully updated');
        Log::info('TEST: End test_update_subtask');
    }

    public function test_destroy_subtask()
    {
        Log::info('TEST: Begin test_destroy_subtask');

        $user = User::factory()->create();
        $this->actingAs($user);

        Log::info('Created user with ID: ' . $user->id);

        $task = Task::factory()->create(['user_id' => $user->id]);
        $subtask = SubTask::create(['task_id' => $task->id, 'title' => 'To delete']);

        Log::info('Created subtask with ID: ' . $subtask->id);

        Log::info('Sending DELETE request to remove subtask');

        $response = $this->deleteJson(route('subtasks.destroy', $subtask));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sub_tasks', ['id' => $subtask->id]);

        Log::info('Subtask successfully deleted from database');
        Log::info('TEST: End test_destroy_subtask');
    }

    public function test_toggle_subtask_completion()
    {
        Log::info('TEST: Begin test_toggle_subtask_completion');

        $user = User::factory()->create();
        $this->actingAs($user);

        Log::info('Created user with ID: ' . $user->id);

        $task = Task::factory()->create(['user_id' => $user->id]);
        $subtask = SubTask::create([
            'task_id' => $task->id,
            'title' => 'Toggle me',
            'completed' => false,
        ]);

        Log::info('Created subtask with ID: ' . $subtask->id . ' and completed = false');

        Log::info('Sending PATCH request to toggle completion status');

        $response = $this->patchJson(route('subtasks.toggle', $subtask));

        $response->assertStatus(200);
        $this->assertEquals(1, $subtask->fresh()->completed);

        Log::info('Subtask completion status toggled to true');
        Log::info('TEST: End test_toggle_subtask_completion');
    }
}
