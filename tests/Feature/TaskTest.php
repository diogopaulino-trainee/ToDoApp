<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('inertia.testing', true);
        \Inertia\Inertia::setRootView('testing');
    }

    public function test_user_can_create_task()
    {
        Log::info('[Create Task] Starting test: user can create task');

        $user = User::factory()->create();
        Log::info('[Create Task] Authenticated user:', ['user_id' => $user->id]);

        $this->actingAs($user);

        $payload = [
            'title' => 'Test Task',
            'description' => 'This is a task.',
            'priority' => 'medium',
            'due_datetime' => now()->addDay()->toDateTimeString(),
        ];

        Log::info('[Create Task] Sending payload:', $payload);

        $response = $this->post(route('tasks.store'), $payload);

        Log::info('[Create Task] Received response:', ['status' => $response->status()]);
        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $user->id]);

        Log::info('[Create Task] Task created successfully.');
    }

    public function test_task_requires_minimum_title_length()
    {
        Log::info('[Validation] Starting test: task title requires minimum length');

        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = ['title' => 'Hi', 'priority' => 'low'];
        Log::info('[Validation] Sending payload:', $payload);

        $response = $this->post(route('tasks.store'), $payload);

        $response->assertSessionHasErrors('title');
        Log::info('[Validation] Title length validation passed.');
    }

    public function test_user_can_update_own_task()
    {
        Log::info('[Update Task] Starting test: user can update their own task');

        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Initial Title',
            'priority' => 'low',
        ]);

        Log::info('[Update Task] Created task:', $task->toArray());

        $payload = ['title' => 'Updated Title', 'priority' => 'high'];
        $response = $this->patch(route('tasks.update', $task), $payload);

        Log::info('[Update Task] Received response:', ['status' => $response->status()]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);

        Log::info('[Update Task] Task updated successfully.');
    }

    public function test_user_cannot_update_others_task()
    {
        Log::info('[Unauthorized Update] Starting test: user cannot update other users\' tasks');

        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $task = Task::create([
            'user_id' => $owner->id,
            'title' => 'Protected Task',
            'priority' => 'medium',
        ]);

        $this->actingAs($intruder);
        Log::info('[Unauthorized Update] Intruder authenticated:', ['user_id' => $intruder->id]);

        $response = $this->patch(route('tasks.update', $task), ['title' => 'Hacked Title']);

        $response->assertStatus(403);
        Log::info('[Unauthorized Update] Access denied as expected.');
    }

    public function test_user_can_soft_delete_task()
    {
        Log::info('[Soft Delete] Starting test: user can soft delete task');

        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'To be deleted',
            'priority' => 'medium',
        ]);

        Log::info('[Soft Delete] Task to be deleted:', $task->toArray());

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_deleted' => true]);

        Log::info('[Soft Delete] Task soft deleted successfully.');
    }

    public function test_user_can_restore_task_from_recycle_bin()
    {
        Log::info('[Restore] Starting test: user can restore task from recycle bin');

        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Deleted Task',
            'is_deleted' => true,
        ]);

        Log::info('[Restore] Task marked as deleted:', $task->toArray());

        $response = $this->patch(route('tasks.restore', $task->id));

        $response->assertRedirect(route('tasks.recycle'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_deleted' => false]);

        Log::info('[Restore] Task restored successfully.');
    }

    public function test_task_requires_valid_priority()
    {
        Log::info('[Validation] Starting test: task requires valid priority');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('tasks.store'), [
            'title' => 'Invalid Priority',
            'priority' => 'urgent',
        ]);

        $response->assertSessionHasErrors('priority');
        Log::info('[Validation] Invalid priority validation passed.');
    }

    public function test_due_date_must_not_be_in_past()
    {
        Log::info('[Validation] Starting test: due date must not be in the past');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('tasks.store'), [
            'title' => 'Past Task',
            'priority' => 'medium',
            'due_datetime' => now()->subDay()->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('due_datetime');
        Log::info('[Validation] Past due date validation passed.');
    }

    public function test_user_can_view_own_task_details()
    {
        Log::info('[View Task] Starting test: user can view their own task');

        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'My Task',
        ]);

        $response = $this->get(route('tasks.show', $task));
        $response->assertStatus(200);
        $response->assertSee($task->title);

        Log::info('[View Task] Task viewed successfully.');
    }

    public function test_user_cannot_view_others_task()
    {
        Log::info('[Unauthorized View] Starting test: user cannot view other users\' tasks');

        $owner = User::factory()->create();
        $other = User::factory()->create();

        $task = Task::create([
            'user_id' => $owner->id,
            'title' => 'Secret Task',
        ]);

        $this->actingAs($other);
        Log::info('[Unauthorized View] Intruder authenticated:', ['user_id' => $other->id]);

        $response = $this->get(route('tasks.show', $task));
        $response->assertStatus(403);

        Log::info('[Unauthorized View] Access denied as expected.');
    }
}
