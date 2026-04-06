<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TopicManagementController;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/', function () {
    return redirect()->route('admin.roles.index');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
    Route::patch('/roles/{user}', [RoleManagementController::class, 'update'])->name('roles.update');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::get('/topics', [TopicManagementController::class, 'index'])->name('topics.index');
    Route::post('/topics', [TopicManagementController::class, 'store'])->name('topics.store');
    Route::put('/topics/{topic}', [TopicManagementController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{topic}', [TopicManagementController::class, 'destroy'])->name('topics.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('topics', TopicController::class);
    });
});
