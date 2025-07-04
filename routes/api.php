<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassTypeController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\ScheduledClassController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('api.logout');

Route::middleware('auth:sanctum')->group(function () {
    // Class Types CRUD
    Route::apiResource('class-types', ClassTypeController::class);
    // Route::apiResource('class-types.scheduled-classes', ScheduledClassController::class)->shallow();
    Route::get('class-types/{id}/scheduled-classes', [ScheduledClassController::class, 'indexByClassType']);
    // Scheduled Classes CRUD
    Route::apiResource('scheduled-classes', ScheduledClassController::class);

    // User Management (Admin Only)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // User Self-Service
    Route::get('/me', [MeController::class, 'show']);
    Route::put('/me', [MeController::class, 'update']);
    Route::put('/me/password', [MeController::class, 'updatePassword']);

    // User's Scheduled Classes
    Route::get('/users/{id}/scheduled-classes', [ScheduledClassController::class, 'userClasses']);
});
