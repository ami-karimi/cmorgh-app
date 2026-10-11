<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\MessagesController;


Route::post('/message', [MessagesController::class, 'get'])->name('message');


Route::prefix('v2')->name('pc.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('login', [\App\Http\Controllers\Pc\AuthController::class, 'login'])->name('login');
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::get('accounts', [\App\Http\Controllers\Pc\AccountsController::class, 'accounts'])->name('accounts');
        Route::get('user', [\App\Http\Controllers\Pc\AuthController::class, 'user'])->name('user');
        Route::get('servers', [\App\Http\Controllers\Pc\ServersListController::class, 'index'])->name('servers');
    });

});


Route::prefix('mobile')->group(function () {
    Route::post('/sign-in', [\App\Http\Controllers\Mobile\AuthController::class, 'sign_in'])->name('sign_in');
    Route::post('/config', [\App\Http\Controllers\Mobile\AuthController::class, 'is_valid_token'])->name('is_valid_token');
    Route::post('/servers', [\App\Http\Controllers\Mobile\AuthController::class, 'get_servers'])->name('get_servers');
    Route::post('/notifications', [\App\Http\Controllers\Mobile\AuthController::class, 'get_notifications'])->name('get_notifications');
    Route::get('/get-ip', [\App\Http\Controllers\Mobile\AuthController::class, 'get_ip'])->name('get_ip');

});
