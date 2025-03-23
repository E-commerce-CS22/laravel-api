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
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductTagController;

// Public routes
Route::apiResource('products', ProductController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

// Swagger documentation routes
Route::get('/docs', function () {
    return view('swagger.index');
});
Route::get('/docs.json', function () {
    return response()->file(base_path('public/docs/swagger.json'));
});

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
        Route::get('/categories/{category}', [CategoryManagementController::class, 'show']);

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
        
        // Product Discount Management
        Route::post('/products/{id}/discount', [App\Http\Controllers\Api\Admin\ProductController::class, 'applyDiscount']);
        Route::put('/products/{id}/discount', [App\Http\Controllers\Api\Admin\ProductController::class, 'updateDiscount']);
        Route::delete('/products/{id}/discount', [App\Http\Controllers\Api\Admin\ProductController::class, 'removeDiscount']);
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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('carts')->group(function () {
        Route::post('/products', [CartController::class, 'addProduct']);
        Route::delete('/products/{productId}', [CartController::class, 'deleteProduct']);
        Route::get('/products', [CartController::class, 'showProducts']);
        Route::patch('/products/{productId}', [CartController::class, 'updateProductQuantity']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('wishlists')->group(function () {
        Route::post('/products/{productId}', [WishListController::class, 'addProduct']);
        Route::delete('/products/{productId}', [WishListController::class, 'deleteProduct']);
        Route::get('/products', [WishListController::class, 'showProducts']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('products')->group(function () {
        Route::post('/{product}/categories', [ProductCategoryController::class, 'store']);
        Route::delete('/{product}/categories/{category}', [ProductCategoryController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('products')->group(function () {
        Route::post('/{product}/tags', [ProductTagController::class, 'store']);
        Route::delete('/{product}/tags/{tag}', [ProductTagController::class, 'destroy']);
    });
});
