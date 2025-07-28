<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    $users = User::all();
    $projects = Project::all();
    $tasks = Task::all();
    return response()->json([
        'users' => $users,
        'projects' => $projects,
        'tasks' => $tasks
    ], 
        200
    );
})->middleware('auth:sanctum', 'permision:task.create');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('admin')->middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::post('/role/{user}', [RoleController::class, 'changeRole']);
});

Route::prefix('projects')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->middleware('permision:project.read');
    Route::post('/', [ProjectController::class, 'store'])->middleware('permision:project.create');
    Route::get('/{id}', [ProjectController::class, 'show'])->middleware('permision:project.read');
    Route::put('/{id}', [ProjectController::class, 'update'])->middleware('permision:project.update');
    Route::delete('/{id}', [ProjectController::class, 'destroy'])->middleware('permision:project.delete');
});

Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->middleware('permision:task.read');
    Route::post('/', [TaskController::class, 'store'])->middleware('permision:task.create');
    Route::get('/{id}', [TaskController::class, 'show'])->middleware('permision:task.read');
    Route::put('/{id}', [TaskController::class, 'update'])->middleware('permision:task.update');
    Route::delete('/{id}', [TaskController::class, 'destroy'])->middleware('permision:task.delete');
});
