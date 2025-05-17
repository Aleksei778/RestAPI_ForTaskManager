<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TaskController;

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class,'logout']);

    // User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user', [UserController::class,'profile']);
    Route::patch('/user', [UserController::class,'update']);
    Route::delete('/user/{user}', [UserController::class,'destroy']);

    // Task routes
    Route::apiResource('tasks', TaskController::class);
});
