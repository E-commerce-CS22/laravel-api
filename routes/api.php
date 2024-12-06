<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('posts', PostController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Admin Routes
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    // User Management
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
});
