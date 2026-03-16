<?php

use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RunningTextController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TvController;
use Illuminate\Support\Facades\Route;

Route::get('/', TvController::class);

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/admin', fn () => redirect('/admin/tu'));
    Route::get('/admin/{unit}', [AdminPanelController::class, 'show'])->whereIn('unit', ['tu', 'data']);

    Route::prefix('admin/api')->group(function (): void {
        Route::apiResource('agendas', AgendaController::class);
        Route::apiResource('profiles', ProfileController::class);
        Route::apiResource('running-texts', RunningTextController::class);
        Route::apiResource('videos', VideoController::class);
        Route::apiResource('users', AdminUserController::class);
    });
});
