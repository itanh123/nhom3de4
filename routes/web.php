<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TopicManagementController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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
