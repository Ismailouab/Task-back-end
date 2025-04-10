<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Password reset routes
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Team routes
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/{team}', [TeamController::class, 'show']);
    Route::put('/teams/{team}', action: [TeamController::class, 'update']);
    Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
    
    // Team member routes
    Route::get('/teams/{team}/non-members', [TeamController::class, 'getNonMembers']);
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember']);
    Route::put('/teams/{team}/members/{user}/role', [TeamController::class, 'updateMemberRole']);
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    // Project routes
    Route::get('teams/{team}/projects', [ProjectController::class, 'index']);
    Route::post('teams/{team}/projects', [ProjectController::class, 'store']);
    Route::get('teams/{team}/projects/{project}', [ProjectController::class, 'show']);
    Route::put('teams/{team}/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('teams/{team}/projects/{project}', [ProjectController::class, 'destroy']);
    // Task routes
    Route::get('teams/{team}/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('teams/{team}/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::put('teams/{team}/projects/{project}/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('teams/{team}/projects/{project}/tasks/{task}', [TaskController::class, 'destroy']);
});
