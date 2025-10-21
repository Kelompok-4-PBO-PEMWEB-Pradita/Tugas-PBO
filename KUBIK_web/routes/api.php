<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

// Health
Route::get('/check', fn() => response()->json(['message' => 'API Ready']));

// Category & Type management (admin)
Route::apiResource('categories', CategoryController::class)->only(['index','store','show','update','destroy']);
Route::apiResource('types', TypeController::class)->only(['index','store','show','update','destroy']);

// Asset master & unit
Route::get('assets', [AssetController::class, 'index']);         // list asset masters + units
Route::post('assets', [AssetController::class, 'store']);        // create master + auto generate units
Route::get('assets/{id_master}', [AssetController::class, 'show']);
Route::put('assets/{id_master}', [AssetController::class, 'update']);
Route::delete('assets/{id_master}', [AssetController::class, 'destroy']);
Route::post('assets/{id_asset}/status', [AssetController::class, 'updateAssetStatus']); // change single unit status

// Booking flow
Route::post('booking', [BookingController::class, 'store']);                       // user create booking
Route::post('booking/cancel/{id}', [BookingController::class, 'cancel']);         // user cancel (before approve)
Route::put('booking/approve/{id}', [BookingController::class, 'approve']);        // admin approve
Route::put('booking/reject/{id}', [BookingController::class, 'reject']);          // admin reject
Route::put('booking/borrow/{id}', [BookingController::class, 'markBorrowed']);    // admin/user mark started
Route::post('booking/return/{id}', [BookingController::class, 'returnBooking']);  // return all or partial

// Users & Admin (basic management)
Route::apiResource('users', UserController::class)->only(['index','show','store','update','destroy']);
Route::apiResource('admins', AdminController::class)->only(['index','show','store','update','destroy']);

// Notifications
Route::get('notifications/user/{id_user}', [NotificationController::class, 'userNotifications']);
Route::post('notifications/user/{id_user}/mark-read', [NotificationController::class, 'markAllRead']);
Route::get('notifications/admin/{id_admin}', [NotificationController::class, 'adminNotifications']);

// Reports / Export (admin)
Route::get('reports/bookings', [AdminController::class, 'exportBookingsCsv']); // export CSV with optional filters
