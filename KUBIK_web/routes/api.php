<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CategoryController,
    TypeController,
    AssetMasterController,
    AssetController,
    BookingController,
    BookingAssetController,
    NotificationController,
    AdminNotificationController,
    UserController,
    AdminController
};

// ----------------------------------------------------
//  TEST ROUTE (Cek apakah API aktif)
// ----------------------------------------------------
Route::get('/ping', function () {
    return response()->json(['message' => 'KUBIK API is running successfully!']);
});

// ----------------------------------------------------
//  USER AUTHENTICATION & MANAGEMENT
// ----------------------------------------------------
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);        // lihat semua user
    Route::get('/{id}', [UserController::class, 'show']);     // lihat 1 user
    Route::post('/register', [UserController::class, 'register']); // daftar
    Route::post('/login', [UserController::class, 'login']);       // login
});

// ----------------------------------------------------
//  ADMIN AUTHENTICATION & MANAGEMENT
// ----------------------------------------------------
Route::prefix('admins')->group(function () {
    Route::get('/', [AdminController::class, 'index']);       // semua admin
    Route::get('/{id}', [AdminController::class, 'show']);    // detail admin
    Route::post('/register', [AdminController::class, 'register']); // daftar admin
    Route::post('/login', [AdminController::class, 'login']);       // login admin
});


// ----------------------------------------------------
//  CATEGORY MANAGEMENT
// ----------------------------------------------------
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

// ----------------------------------------------------
//  TYPE MANAGEMENT
// ----------------------------------------------------
Route::prefix('types')->group(function () {
    Route::get('/', [TypeController::class, 'index']);
    Route::get('/{id}', [TypeController::class, 'show']);
    Route::post('/', [TypeController::class, 'store']);
    Route::put('/{id}', [TypeController::class, 'update']);
    Route::delete('/{id}', [TypeController::class, 'destroy']);
});

// ----------------------------------------------------
//  ASSET MASTER MANAGEMENT
// ----------------------------------------------------
Route::prefix('asset-masters')->group(function () {
    Route::get('/', [AssetMasterController::class, 'index']);
    Route::get('/{id}', [AssetMasterController::class, 'show']);
    Route::post('/', [AssetMasterController::class, 'store']);
    Route::put('/{id}', [AssetMasterController::class, 'update']);
    Route::delete('/{id}', [AssetMasterController::class, 'destroy']);
});

// ----------------------------------------------------
//  ASSET (UNIT INDIVIDUAL)
// ----------------------------------------------------
Route::prefix('assets')->group(function () {
    Route::get('/', [AssetController::class, 'index']);
    Route::get('/{id}', [AssetController::class, 'show']);
    Route::put('/{id}/condition', [AssetController::class, 'updateCondition']);
    Route::put('/{id}/borrow', [AssetController::class, 'markBorrowed']);
    Route::put('/{id}/return', [AssetController::class, 'markReturned']);
});

// ----------------------------------------------------
//  BOOKING (PEMINJAMAN)
// ----------------------------------------------------
Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']); // user ajukan peminjaman
    Route::put('/{id}/approve', [BookingController::class, 'approve']); // admin setujui
    Route::put('/{id}/reject', [BookingController::class, 'reject']);   // admin tolak
    Route::put('/{id}/request-return', [BookingController::class, 'requestReturn']); // user ajukan pengembalian
    Route::put('/{id}/confirm-return', [BookingController::class, 'confirmReturn']); // admin verifikasi
});

// ----------------------------------------------------
//  BOOKING-ASSET (PIVOT MANY TO MANY)
// ----------------------------------------------------
Route::prefix('booking-assets')->group(function () {
    Route::get('/{id_booking}', [BookingAssetController::class, 'index']); // lihat daftar aset per booking
    Route::post('/attach', [BookingAssetController::class, 'attachAsset']); // tambah aset ke booking
    Route::post('/detach', [BookingAssetController::class, 'detachAsset']); // hapus aset dari booking
});

// ----------------------------------------------------
//  NOTIFICATION SYSTEM
// ----------------------------------------------------
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/admin/{id_admin}', [NotificationController::class, 'showByAdmin']);
    Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/cleanup/auto', [NotificationController::class, 'autoCleanup']);
});

// ----------------------------------------------------
//  ADMIN NOTIFICATION MANAGEMENT
// ----------------------------------------------------
Route::prefix('admin-notifications')->group(function () {
    Route::get('/{id_admin}', [AdminNotificationController::class, 'index']);
    Route::put('/{id_admin}/read-all', [AdminNotificationController::class, 'markAllAsRead']);
    Route::delete('/{id_admin}/clear', [AdminNotificationController::class, 'clearOldNotifications']);
});
