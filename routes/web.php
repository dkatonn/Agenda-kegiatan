<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TVController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/* TV DISPLAY */

Route::get('/', [TVController::class, 'index'])->name('tv');
Route::get('/tv/state', [TVController::class, 'state'])->name('tv.state');
Route::get('/tv/payload', [TVController::class, 'payload'])->name('tv.payload');

Route::middleware('guest')->group(function () {
    Route::get('/csrf-token', [AdminAuthController::class, 'token'])->name('csrf.token');
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::get('/login/token', [AdminAuthController::class, 'token'])->name('login.token');
    Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/logout/tab-close', [AdminAuthController::class, 'closeTab'])->middleware('auth')->name('logout.tab-close');

/* ADMIN */

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('index');

    // HALAMAN

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');

    Route::get('/video', [VideoController::class, 'index'])->name('video');

    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');

    Route::get('/teks-berjalan', [SettingController::class, 'index'])->name('running-text');
    Route::get('/latar-belakang', [SettingController::class, 'background'])->name('background');

    Route::get('/user-settings', [UserSettingController::class, 'index'])->name('user-settings');
    Route::post('/user-settings', [UserSettingController::class, 'store'])->name('user-settings.store');
    Route::put('/user-settings/{id}', [UserSettingController::class, 'update'])->name('user-settings.update');
    Route::post('/user-settings/{id}/lock', [UserSettingController::class, 'lock'])->name('user-settings.lock');
    Route::delete('/user-settings/{id}/lock', [UserSettingController::class, 'unlock'])->name('user-settings.unlock');
    Route::patch('/user-settings/{id}/toggle', [UserSettingController::class, 'toggle'])->name('user-settings.toggle');
    Route::delete('/user-settings/{id}', [UserSettingController::class, 'destroy'])->name('user-settings.delete');
    Route::get('/password', [UserSettingController::class, 'editPassword'])->name('password.edit');
    Route::put('/password', [UserSettingController::class, 'updatePassword'])->name('password.update');

    // CRUD EMPLOYEE

    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');

    Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::post('/employee/{id}/lock', [EmployeeController::class, 'lock'])->name('employee.lock');
    Route::delete('/employee/{id}/lock', [EmployeeController::class, 'unlock'])->name('employee.unlock');

    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete');

    // CRUD AGENDA

    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');

    Route::put('/agenda/{id}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::post('/agenda/{id}/lock', [AgendaController::class, 'lock'])->name('agenda.lock');
    Route::delete('/agenda/{id}/lock', [AgendaController::class, 'unlock'])->name('agenda.unlock');

    Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.delete');

    // CRUD VIDEO

    Route::post('/video', [VideoController::class, 'store'])->name('video.store');

    Route::put('/video/{id}', [VideoController::class, 'update'])->name('video.update');
    Route::post('/video/{id}/lock', [VideoController::class, 'lock'])->name('video.lock');
    Route::delete('/video/{id}/lock', [VideoController::class, 'unlock'])->name('video.unlock');

    Route::patch('/video/{id}/toggle', [VideoController::class, 'toggle'])->name('video.toggle');

    Route::patch('/video/reorder', [VideoController::class, 'reorder'])->name('video.reorder');

    Route::delete('/video/{id}', [VideoController::class, 'delete'])->name('video.delete');

    // SETTING (background + running text + dll)

    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
});
