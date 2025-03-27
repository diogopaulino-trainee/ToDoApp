<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\TaskController;
use App\Models\Level;
use App\Models\Task;
use App\Models\UserLevel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/help', function () {
    return Inertia::render('Help');
})->name('help');

Route::get('/dashboard', function () {
    $user = Auth::user();

    $completed = Task::where('user_id', $user->id)->where('completed', true)->where('is_deleted', false)->count();
    $pending = Task::where('user_id', $user->id)->where('completed', false)->where('is_deleted', false)->count();

    $userLevel = UserLevel::where('user_id', $user->id)->first();
    $currentLevel = $userLevel?->level;
    $allLevels = Level::orderBy('required_tasks')->get();

    $showConfetti = false;

    if ($userLevel && !$userLevel->animation_seen) {
        $showConfetti = true;
        $userLevel->animation_seen = true;
        $userLevel->save();
    }

    return Inertia::render('Dashboard', [
        'auth' => ['user' => $user],
        'completedCount' => $completed,
        'pendingCount' => $pending,
        'currentLevel' => $currentLevel,
        'levels' => $allLevels,
        'showConfetti' => $showConfetti,
    ]);

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/recycle', [TaskController::class, 'recycleBin'])->name('tasks.recycle');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/restore/{id}', [TaskController::class, 'restore'])->name('tasks.restore');

    Route::post('/tasks/{task}/subtasks', [SubTaskController::class, 'store'])->name('subtasks.store');
    Route::patch('/subtasks/{subtask}', [SubTaskController::class, 'update'])->name('subtasks.update');
    Route::delete('/subtasks/{subtask}', [SubTaskController::class, 'destroy'])->name('subtasks.destroy');
    Route::patch('/subtasks/{subtask}/toggle', [SubTaskController::class, 'toggle'])->name('subtasks.toggle');

    Route::post('/tasks/{task}/attachments/upload', [AttachmentController::class, 'upload'])->name('tasks.attachments.upload');
    Route::delete('/tasks/{task}/attachments/{attachment}', [AttachmentController::class, 'delete'])->name('tasks.attachments.delete');

    Route::get('/api/tasks/{task}/subtasks', function (Task $task) {
        return $task->subtasks;
    });

    Route::post('/level/seen', function (Request $request) {
        $user = Auth::user();
        $levelId = $request->input('level_id');
    
        $userLevel = \App\Models\UserLevel::where('user_id', $user->id)
            ->where('level_id', $levelId)
            ->first();
    
        if ($userLevel && !$userLevel->animation_seen) {
            $userLevel->animation_seen = true;
            $userLevel->save();
        }
    
        return response()->json(['message' => 'Animation marked as seen']);
    })->name('level.markAsSeen');
});

require __DIR__.'/auth.php';
