<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\UserManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/permissions', [AuthorizationController::class, 'getPermissions']);
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::patch('/users/{user}/role', [AuthorizationController::class, 'updateUserRole']);

    Route::get('/users', [UserManagementController::class, 'index']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::put('/users/{user}', [UserManagementController::class, 'update']);
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    Route::patch('/users/{user}/lock', [UserManagementController::class, 'lock']);
    Route::patch('/users/{user}/unlock', [UserManagementController::class, 'unlock']);

    Route::get('/topics', [TopicController::class, 'index']);
    Route::post('/topics', [TopicController::class, 'store']);
    Route::get('/topics/{topic}', [TopicController::class, 'show']);
    Route::put('/topics/{topic}', [TopicController::class, 'update']);
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy']);

    Route::get('/reports/overview', [ReportController::class, 'overview']);
    Route::get('/reports/users', [ReportController::class, 'users']);
    Route::get('/reports/topics', [ReportController::class, 'topics']);
    Route::get('/reports/exams', [ReportController::class, 'exams']);
});
