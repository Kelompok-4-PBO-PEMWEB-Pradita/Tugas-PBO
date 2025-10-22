<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\AssetMasterController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes - KUBIK Final Version
|--------------------------------------------------------------------------
|
| Semua endpoint API untuk sistem peminjaman internal kampus Aplikasi KUBIK.
| Setiap route mengikuti class diagram & smart logic versi final.
|
*/

// =================== USER ===================
Route::prefix('user')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::get('list', [UserController::class, 'index']);
    Route::get('{id}', [UserController::class, 'show']);
});

// =================== ADMIN ===================
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
    Route::get('list', [AdminController::class, 'index']);
    Route::get('{id}', [AdminController::class, 'show']);
});

// =================== CATEGORY ===================
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('create', [CategoryController::class, 'store']);
    Route::get('{id}', [CategoryController::class, 'show']);
    Route::put('{id}/update', [CategoryController::class, 'update']);
    Route::delete('{id}/delete', [CategoryController::class, 'destroy']);
});

// =================== TYPE ===================
Route::prefix('type')->group(function () {
    Route::get('/', [TypeController::class, 'index']);
    Route::post('create', [TypeController::class, 'store']);
    Route::get('{id}', [TypeController::class, 'show']);
    Route::put('{id}/update', [TypeController::class, 'update']);
    Route::delete('{id}/delete', [TypeController::class, 'destroy']);
});

// =================== ASSET MASTER ===================
Route::prefix('asset-master')->group(function () {
    Route::get('/', [AssetMasterController::class, 'index']);
    Route::post('create', [AssetMasterController::class, 'store']);
    Route::get('{id}', [AssetMasterController::class, 'show']);
    Route::put('{id}/update', [AssetMasterController::class, 'update']);
    Route::delete('{id}/delete', [AssetMasterController::class, 'destroy']);
});

// =================== ASSET ===================
Route::prefix('asset')->group(function () {
    Route::get('/', [AssetController::class, 'index']);
    Route::get('{id}', [AssetController::class, 'show']);
    Route::put('{id}/condition', [AssetController::class, 'updateCondition']);
});

// =================== BOOKING ===================
Route::prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('{id}', [BookingController::class, 'show']);
    Route::post('create', [BookingController::class, 'store']);
    Route::put('{id}/approve', [BookingController::class, 'approve']);
    Route::put('{id}/reject', [BookingController::class, 'reject']);
    Route::put('{id}/request-return', [BookingController::class, 'requestReturn']);
    Route::put('{id}/verify-return', [BookingController::class, 'verifyReturn']);
});

// =================== NOTIFICATION (USER) ===================
Route::prefix('notification')->group(function () {
    Route::get('/user/{id}', [NotificationController::class, 'getUserNotifications']);
    Route::put('/read/{id}', [NotificationController::class, 'markAsRead']);
});

// =================== ADMIN NOTIFICATION ===================
Route::prefix('admin-notification')->group(function () {
    Route::get('/{id_admin}', [AdminNotificationController::class, 'getAdminNotifications']);
    Route::put('/read/{id}', [AdminNotificationController::class, 'markAsRead']);
});
