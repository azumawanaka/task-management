<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

// Route::get('/login', function () {
//     return view('welcome');
// });

Route::get('/login', LoginController::class);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', RegisterController::class);
Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Users
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::get('/api/check-email', [UserController::class, 'checkEmail']);
    Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);

    // Tasks
    Route::get('/api/tasks', [TaskController::class, 'index']);
    Route::post('/api/tasks', [TaskController::class, 'store']);
    Route::get('/api/tasks/{task}/show', [TaskController::class, 'show']);
    Route::post('/api/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/api/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/api/tasks/{task}/publish_status', [TaskController::class, 'updatePublishStatus']);

    Route::get('/api/tasks/statuses', function () {
        return Task::STATUS;
    });

    Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
});

