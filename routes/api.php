<?php
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/sign-in', [\App\Http\Controllers\Mobile\AuthController::class, 'sign_in'])->name('sign_in');
    Route::post('/config', [\App\Http\Controllers\Mobile\AuthController::class, 'is_valid_token'])->name('is_valid_token');
    Route::post('/servers', [\App\Http\Controllers\Mobile\AuthController::class, 'get_servers'])->name('get_servers');
    Route::post('/notifications', [\App\Http\Controllers\Mobile\AuthController::class, 'get_notifications'])->name('get_notifications');
    Route::get('/get-ip', [\App\Http\Controllers\Mobile\AuthController::class, 'get_ip'])->name('get_ip');

});
