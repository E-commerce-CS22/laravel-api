<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\CategoryManagementController;
use App\Http\Controllers\Api\Admin\TagController;

// Public routes
Route::apiResource('products', ProductController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

// Protected Admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/logout', [AdminAuthController::class, 'logout']);

        // User Management
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
        
        // Category Management
        Route::get('/categories', [CategoryManagementController::class, 'index']);
        Route::post('/categories', [CategoryManagementController::class, 'store']);
        Route::put('/categories/{category}', [CategoryManagementController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy']);
        
        // Tag Management
        Route::get('/tags', [TagController::class, 'index']);
        Route::get('/tags/{id}', [TagController::class, 'show']);
        Route::post('/tags', [TagController::class, 'store']);
        Route::put('/tags/{id}', [TagController::class, 'update']);
        Route::delete('/tags/{id}', [TagController::class, 'destroy']);
        
        // Product Management
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });
});

// Protected Customer routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/logout', [CustomerAuthController::class, 'logout']);
        
        // Customer Product Management
        Route::get('/products', [App\Http\Controllers\Api\Customer\ProductController::class, 'index']);
        Route::get('/products/{product}', [App\Http\Controllers\Api\Customer\ProductController::class, 'show']);
        
        // Customer Profile Management
        Route::get('/profile', [App\Http\Controllers\Api\Customer\ProfileController::class, 'show']);
        Route::put('/profile', [App\Http\Controllers\Api\Customer\ProfileController::class, 'update']);
    });
});
