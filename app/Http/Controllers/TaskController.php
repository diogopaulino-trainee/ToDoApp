<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Level;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the user's tasks, ordered by due date and priority.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->with('attachments')
            ->where('is_deleted', false)
            ->orderBy('due_datetime', 'asc')
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

    /**
     * Store a newly created task in the database.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|min:3',
            'description' => 'nullable|string|min:3',
            'priority'    => 'required|in:low,medium,high',
            'due_datetime'    => 'nullable|date|after_or_equal:today',
        ]);

        try {
            $task = auth()->user()->tasks()->create($validated);

            if ($request->has('subtasks') && is_array($request->subtasks)) {
                $subtasks = array_filter($request->subtasks, fn ($text) => trim($text) !== '');

                foreach ($subtasks as $subtask) {
                    $task->subtasks()->create([
                        'title' => $subtask,
                        'completed' => false,
                    ]);
                }
            }

            return redirect()->route('tasks.index')->with('success', 'Task added successfully!');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add task.', 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified task.
     * 
     * If the "completed" status is updated, all related subtasks will be updated accordingly.
     *
     * @param Request $request The request object.
     * @param Task $task The task to update.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return abort(403, 'You do not have permission to update this task.');
        }

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|min:3',
            'description' => 'sometimes|nullable|string|min:3',
            'priority'    => 'sometimes|required|in:low,medium,high',
            'due_datetime'    => 'sometimes|nullable|date|after_or_equal:today',
            'completed'   => 'sometimes|boolean',
        ]);

        try {
            $task->update($validated);
    
            if (array_key_exists('completed', $validated)) {
                $task->subtasks()->update(['completed' => $validated['completed']]);
    
                if ($validated['completed']) {
                    $completedCount = Task::where('user_id', auth()->id())
                        ->where('completed', true)
                        ->where('is_deleted', false)
                        ->count();
    
                    $newLevel = Level::where('required_tasks', '<=', $completedCount)
                        ->orderByDesc('required_tasks')
                        ->first();
    
                    if ($newLevel) {
                        $userLevel = UserLevel::firstOrNew(['user_id' => auth()->id()]);
                        $previousLevelId = $userLevel->level_id;
    
                        if ($userLevel->level_id !== $newLevel->id) {
                            $userLevel->level_id = $newLevel->id;
                            $userLevel->animation_seen = false;
                            $userLevel->save();
    
                            return redirect()
                                ->route('tasks.index')
                                ->with('success', "🎉 Congrats! You've reached {$newLevel->name}!")
                                ->with('newLevel', $newLevel);
                        }
                    }
                }
            }
    
            return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Failed to update task.');
        }
    }

    /**
     * Remove the specified task from the database.
     *
     * @param Task $task The task to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return abort(403, 'You do not have permission to delete this task.');
        }
    
        try {
            $task->update(['is_deleted' => true]);
            return redirect()->route('tasks.index')->with('success', 'Task moved to Recycle Bin!');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('error', 'Failed to move task to Recycle Bin.');
        }
    }

    /**
     * Display the Recycle Bin.
     *
     * @return \Inertia\Response
     */
    public function recycleBin()
    {
        $deletedTasks = auth()->user()->tasks()->where('is_deleted', true)->get();

        return Inertia::render('Tasks/RecycleBin', [
            'deletedTasks' => $deletedTasks,
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    /**
     * Restore a task from the Recycle Bin.
     *
     * @param int $id The ID of the task to restore.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $task->update(['is_deleted' => false]);

        return redirect()->route('tasks.recycle')->with('success', 'Task restored successfully!');
    }

    /**
     * Display a specific task.
     *
     * @param Task $task The task to display.
     * @return \Inertia\Response
     */
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['subtasks', 'attachments']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }
}
