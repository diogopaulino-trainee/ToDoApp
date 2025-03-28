<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Upload attachments to a task.
     *
     * @param Request $request The request object.
     * @param Task $task The task to upload attachments to.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'files.*' => 'required|file|max:5120',
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('attachments', 'public');
            $attachment = $task->attachments()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
            ]);

            $uploaded[] = $attachment;
        }

        if ($request->wantsJson()) {
            $task->load('attachments');
            return response()->json([
                'task' => $task,
            ]);
        }

        return redirect()->route('tasks.show', $task->id)->with('success', 'Files uploaded successfully.');
    }

    /**
     * Delete an attachment from a task.
     *
     * @param Task $task The task to delete the attachment from.
     * @param Attachment $attachment The attachment to delete.
     * @param Request $request The request object.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function delete(Task $task, Attachment $attachment, Request $request)
    {
        if ($task->user_id !== auth()->id() || $attachment->task_id !== $task->id) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        if ($request->wantsJson()) {
            $task->load('attachments');
            return response()->json([
                'task' => $task,
            ]);
        }

        return redirect()->route('tasks.show', $task->id)->with('success', 'Attachment removed.');
    }
}
