<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->orderBy('due_date', 'asc')
            ->orderByRaw("
                CASE 
                    WHEN priority = 'high' THEN 1
                    WHEN priority = 'medium' THEN 2
                    WHEN priority = 'low' THEN 3
                    ELSE 4
                END
            ")
            ->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'priority'    => 'required|in:low,medium,high',
            'due_date'    => 'nullable|date|after_or_equal:today',
        ]);

        try {
            auth()->user()->tasks()->create($validated);
            return redirect()->route('tasks.index')->with('success', 'Task added successfully!');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add task.', 'error' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return abort(403, 'You do not have permission to update this task.');
        }

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|min:3',
            'description' => 'sometimes|required|string|min:3',
            'priority'    => 'sometimes|required|in:low,medium,high',
            'due_date'    => 'sometimes|nullable|date|after_or_equal:today',
            'completed'   => 'required|boolean',
        ]);

        try {
            $task->update($validated);
            return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Failed to update task.');
        }
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return abort(403, 'You do not have permission to delete this task.');
        }

        try {
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Failed to delete task.');
        }
    }
}
