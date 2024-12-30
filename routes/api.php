<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Admin\UserManagementController;

// Public routes
Route::apiResource('products', ProductController::class);

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

// Protected Admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/logout', [AdminAuthController::class, 'logout']);

        // User Management
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
    });
});

// Protected Customer routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/logout', [CustomerAuthController::class, 'logout']);
        // Add other customer-specific routes here
    });
});
