<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /api automatically.
|
*/

// -------------------------------------------------------
// AUTH ROUTES (Public - No token needed)
// -------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// -------------------------------------------------------
// PROTECTED ROUTES (Require Sanctum token)
// -------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // -------------------------------------------------------
    // CATEGORIES
    // View: all authenticated users
    // Create/Update/Delete: admin only
    // -------------------------------------------------------
    Route::get('/categories',       [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/categories',            [CategoryController::class, 'store']);
        Route::put('/categories/{category}',  [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // -------------------------------------------------------
    // PRODUCTS
    // View: all authenticated users
    // Create/Update: admin and manager
    // Delete: admin only
    // -------------------------------------------------------
    Route::get('/products',        [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/products',           [ProductController::class, 'store']);
        Route::put('/products/{product}',  [ProductController::class, 'update']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });
    // -------------------------------------------------------
    // DASHBOARD STATS
    // -------------------------------------------------------
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    });
});
