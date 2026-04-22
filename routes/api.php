<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, ProductController, CategoryController, CartController, OrderController, AdminController};

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Customer Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);

    // Vendor
    Route::middleware('role:vendor')->prefix('vendor')->group(function () {
        Route::get('/products', [ProductController::class, 'vendorProducts']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::get('/orders', [OrderController::class, 'vendorOrders']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateItemStatus']);
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/vendors', [AdminController::class, 'vendors']);
        Route::post('/vendors/{id}/approve', [AdminController::class, 'approveVendor']);
        Route::post('/vendors/{id}/reject', [AdminController::class, 'rejectVendor']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/orders', [OrderController::class, 'allOrders']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});
