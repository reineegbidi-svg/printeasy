<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PrinterController;
use App\Http\Controllers\Api\SupportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/orders/calculate-price', [OrderController::class, 'calculatePrice']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);

        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/orders/{order}/pay', [PaymentController::class, 'pay']);
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt']);

        Route::get('/support', [SupportController::class, 'index']);
        Route::post('/support', [SupportController::class, 'store']);

        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->middleware('role:printer,admin');

        Route::middleware('role:printer')->prefix('printer')->group(function () {
            Route::get('/dashboard', [PrinterController::class, 'dashboard']);
            Route::get('/orders/available', [PrinterController::class, 'availableOrders']);
            Route::post('/orders/{order}/accept', [PrinterController::class, 'acceptOrder']);
            Route::get('/stats', [PrinterController::class, 'stats']);
            Route::get('/pricing', [PrinterController::class, 'pricingRules']);
            Route::post('/pricing', [PrinterController::class, 'storePricingRule']);
            Route::get('/availabilities', [PrinterController::class, 'availabilities']);
            Route::post('/availabilities', [PrinterController::class, 'storeAvailability']);
            Route::post('/toggle-availability', [PrinterController::class, 'toggleAvailability']);
        });

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/users', [AdminController::class, 'users']);
            Route::put('/users/{user}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
            Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
            Route::post('/support/{ticket}/reply', [SupportController::class, 'reply']);
        });
    });
});
