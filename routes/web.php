<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    $user = Auth::user();

    $completed = Task::where('user_id', $user->id)->where('completed', true)->count();
    $pending = Task::where('user_id', $user->id)->where('completed', false)->count();

    return Inertia::render('Dashboard', [
        'completedCount' => $completed,
        'pendingCount' => $pending,
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
});

require __DIR__.'/auth.php';
