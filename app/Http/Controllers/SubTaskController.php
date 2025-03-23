<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
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

    public function update(Request $request, SubTask $subtask)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $subtask->update(['title' => $request->title]);

        return response()->json($subtask);
    }

    public function destroy(SubTask $subtask)
    {
        $subtask->delete();
        return response()->json(['message' => 'Subtask deleted successfully']);
    }

    public function toggle(SubTask $subtask)
    {
        $subtask->update([
            'completed' => !$subtask->completed
        ]);

        return response()->json($subtask);
    }
}
