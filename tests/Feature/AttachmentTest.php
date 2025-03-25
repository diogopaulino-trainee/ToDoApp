<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('inertia.testing', true);
        \Inertia\Inertia::setRootView('testing');

        Storage::fake('public');
    }

    public function test_user_can_upload_attachments_to_own_task()
    {
        Log::info('TEST: Start test_user_can_upload_attachments_to_own_task');

        $user = User::factory()->create();
        $this->actingAs($user);

        Log::info('Creating a new task for the user');
        $task = Task::factory()->create(['user_id' => $user->id]);

        Log::info('Uploading fake files');
        $file1 = UploadedFile::fake()->create('file1.pdf', 100);
        $file2 = UploadedFile::fake()->create('file2.jpg', 200);

        $response = $this->post(route('tasks.attachments.upload', $task), [
            'files' => [$file1, $file2],
        ]);

        Log::info('Checking redirection and storage');
        $response->assertRedirect(route('tasks.show', $task));
        Storage::disk('public')->assertExists("attachments/{$file1->hashName()}");
        Storage::disk('public')->assertExists("attachments/{$file2->hashName()}");

        Log::info('Verifying database entries');
        $this->assertDatabaseHas('attachments', [
            'task_id' => $task->id,
            'name' => 'file1.pdf',
        ]);
        $this->assertDatabaseHas('attachments', [
            'task_id' => $task->id,
            'name' => 'file2.jpg',
        ]);

        Log::info('TEST: End test_user_can_upload_attachments_to_own_task');
    }

    public function test_user_cannot_upload_attachments_to_other_users_task()
    {
        Log::info('TEST: Start test_user_cannot_upload_attachments_to_other_users_task');

        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder);

        Log::info('Attempting to upload to another user\'s task');
        $file = UploadedFile::fake()->create('intrusion.pdf', 50);

        $response = $this->post(route('tasks.attachments.upload', $task), [
            'files' => [$file],
        ]);

        $response->assertStatus(403);
        Storage::disk('public')->assertMissing("attachments/{$file->hashName()}");

        Log::info('TEST: End test_user_cannot_upload_attachments_to_other_users_task');
    }

    public function test_user_can_delete_own_attachment()
    {
        Log::info('TEST: Start test_user_can_delete_own_attachment');

        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        Log::info('Storing a fake file manually');
        $file = UploadedFile::fake()->create('delete-me.pdf', 120);
        $path = $file->store('attachments', 'public');

        $attachment = $task->attachments()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        Log::info('Attempting to delete attachment');
        $response = $this->delete(route('tasks.attachments.delete', [
            'task' => $task->id,
            'attachment' => $attachment->id,
        ]));

        $response->assertRedirect(route('tasks.show', $task));
        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);

        Log::info('TEST: End test_user_can_delete_own_attachment');
    }

    public function test_user_cannot_delete_others_attachment()
    {
        Log::info('TEST: Start test_user_cannot_delete_others_attachment');

        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $owner->id]);

        $file = UploadedFile::fake()->create('dont-touch.pdf', 50);
        $path = $file->store('attachments', 'public');

        $attachment = $task->attachments()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
        ]);

        $this->actingAs($intruder);

        Log::info('Intruder attempting to delete another user\'s attachment');
        $response = $this->delete(route('tasks.attachments.delete', [
            'task' => $task->id,
            'attachment' => $attachment->id,
        ]));

        $response->assertStatus(403);
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);

        Log::info('TEST: End test_user_cannot_delete_others_attachment');
    }
}
