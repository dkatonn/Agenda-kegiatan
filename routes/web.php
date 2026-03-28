<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TVController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserSettingController;


/* TV DISPLAY */

Route::get('/', [TVController::class, 'index'])->name('tv');
Route::get('/tv/state', [TVController::class, 'state'])->name('tv.state');
Route::get('/tv/payload', [TVController::class, 'payload'])->name('tv.payload');


/* ADMIN */

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('index');


    // HALAMAN

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');

    Route::get('/video', [VideoController::class, 'index'])->name('video');

    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');

    Route::get('/teks-berjalan', [SettingController::class, 'index'])->name('running-text');

    Route::get('/user-settings', [UserSettingController::class, 'index'])->name('user-settings');


    // CRUD EMPLOYEE

    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');

    Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');

    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete');


    // CRUD AGENDA

    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');

    Route::put('/agenda/{id}', [AgendaController::class, 'update'])->name('agenda.update');

    Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.delete');


    // CRUD VIDEO

    Route::post('/video', [VideoController::class, 'store'])->name('video.store');

    Route::put('/video/{id}', [VideoController::class, 'update'])->name('video.update');

    Route::patch('/video/{id}/toggle', [VideoController::class, 'toggle'])->name('video.toggle');

    Route::patch('/video/reorder', [VideoController::class, 'reorder'])->name('video.reorder');

    Route::delete('/video/{id}', [VideoController::class, 'delete'])->name('video.delete');


    // SETTING (background + running text + dll)

    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
});
