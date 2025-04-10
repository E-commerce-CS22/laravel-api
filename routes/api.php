<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Customer\ProfileController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\CategoryManagementController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\Api\Admin\ProductVariantController;
use App\Http\Controllers\Api\Admin\AttributeController;
use App\Http\Controllers\Api\Admin\AttributeValueController;
use App\Http\Controllers\Api\ProductSearchController;
use App\Http\Controllers\Api\Admin\ProductImageController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Admin\SlideController;

// Public routes
Route::apiResource('products', ProductController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::get('/categories', [CategoryManagementController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/search/{query}', [ProductSearchController::class, 'searchGet']);
Route::get('/categories/{categoryId}/products', [CategoryProductController::class, 'getProductsByCategory']);
Route::get('/slides', [SlideController::class, 'index']);

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
        Route::get('/products/{id}/summary', [ProductController::class, 'getProductSummary']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Product Discount Management
        Route::get('/products/{id}/discount', [ProductController::class, 'getDiscount']);
        Route::post('/products/{id}/discount', [ProductController::class, 'applyDiscount']);
        Route::put('/products/{id}/discount', [ProductController::class, 'updateDiscount']);
        Route::delete('/products/{id}/discount', [ProductController::class, 'removeDiscount']);

        // Product Variant Management
        Route::get('/products/{productId}/variants', [ProductVariantController::class, 'index']);
        Route::get('/products/{productId}/variants/{variantId}', [ProductVariantController::class, 'show']);
        Route::post('/products/{productId}/variants', [ProductVariantController::class, 'store']);
        Route::put('/products/{productId}/variants/{variantId}', [ProductVariantController::class, 'update']);
        Route::delete('/products/{productId}/variants/{variantId}', [ProductVariantController::class, 'destroy']);
        Route::patch('/products/{productId}/variants/{variantId}/stock', [ProductVariantController::class, 'updateStock']);
        Route::patch('/products/{productId}/variants/{variantId}/default', [ProductVariantController::class, 'setDefault']);

        // Product Image Management
        Route::post('/products/{id}/images', [ProductImageController::class, 'uploadProductImages']);
        Route::post('/products/{productId}/variants/{variantId}/images', [ProductImageController::class, 'uploadVariantImages']);
        Route::delete('/products/{productId}/images/{imageId}', [ProductImageController::class, 'deleteImage']);

        // Attribute Management
        Route::get('/attributes', [AttributeController::class, 'index']);
        Route::get('/attributes/{id}', [AttributeController::class, 'show']);
        Route::post('/attributes', [AttributeController::class, 'store']);
        Route::put('/attributes/{id}', [AttributeController::class, 'update']);
        Route::delete('/attributes/{id}', [AttributeController::class, 'destroy']);

        // Attribute Value Management
        Route::get('/attributes/{attributeId}/values', [AttributeValueController::class, 'index']);
        Route::get('/attributes/{attributeId}/values/{valueId}', [AttributeValueController::class, 'show']);
        Route::post('/attributes/{attributeId}/values', [AttributeValueController::class, 'store']);
        Route::put('/attributes/{attributeId}/values/{valueId}', [AttributeValueController::class, 'update']);
        Route::delete('/attributes/{attributeId}/values/{valueId}', [AttributeValueController::class, 'destroy']);
        
        // Slide Management
        Route::get('/slides', [SlideController::class, 'index']);
        Route::post('/slides', [SlideController::class, 'store']);
        Route::put('/slides/{slide}', [SlideController::class, 'update']);
        Route::delete('/slides/{slide}', [SlideController::class, 'destroy']);

        // Order Management
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::patch('/orders/{id}/payment-status', [AdminOrderController::class, 'updatePaymentStatus']);
        Route::patch('/orders/{id}/tracking', [AdminOrderController::class, 'updateTracking']);
        Route::get('/orders-statistics', [AdminOrderController::class, 'statistics']);
    });
});

// Protected Customer routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/logout', [CustomerAuthController::class, 'logout']);

        // Customer Product Management
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);

        // Customer Profile Management
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile/password', [ProfileController::class, 'changePassword']);
        
        // Customer Order Management
        Route::get('/orders', [CustomerOrderController::class, 'index']);
        Route::post('/orders', [CustomerOrderController::class, 'store']);
        Route::get('/orders/{id}', [CustomerOrderController::class, 'show']);
        Route::patch('/orders/{id}/cancel', [CustomerOrderController::class, 'cancel']);
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
