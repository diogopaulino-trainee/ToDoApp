<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
    /**
     * Store a new subtask.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'titles' => 'required|array',
            'titles.*' => 'required|string|max:255',
            'task_id' => 'required|exists:tasks,id',
        ]);

        $created = [];

        foreach ($request->titles as $title) {
            $created[] = SubTask::create([
                'task_id' => $request->task_id,
                'title' => $title,
            ]);
        }

        return response()->json($created);
    }

    /**
     * Update a subtask.
     *
     * @param Request $request The request object.
     * @param SubTask $subtask The subtask to update.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, SubTask $subtask)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $subtask->update(['title' => $request->title]);

        return response()->json($subtask);
    }

    /**
     * Delete a subtask.
     *
     * @param SubTask $subtask The subtask to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SubTask $subtask)
    {
        $subtask->delete();
        return response()->json(['message' => 'Subtask deleted successfully']);
    }

    /**
     * Toggle a subtask.
     *
     * @param SubTask $subtask The subtask to toggle.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(SubTask $subtask)
    {
        $subtask->update([
            'completed' => !$subtask->completed
        ]);

        return response()->json($subtask);
    }
}
