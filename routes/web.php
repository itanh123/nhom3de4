<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TopicManagementController;
use App\Http\Controllers\Admin\UserManagementController;

// =====================
// ROUTE CÔNG KHAI (Public Routes)
// =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
// Logout - cần đăng nhập mới logout được
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Trang chủ - redirect đến dashboard nếu đã login, ngược lại đến login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// =====================
// ROUTE CẦN ĐĂNG NHẬP (Authenticated Routes)
// =====================
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User quản lý Topics (cần role admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('topics', TopicController::class);
    });

    // Quản lý Questions (cần role admin hoặc teacher)
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('questions', QuestionController::class)->except(['show']);
        Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
        Route::patch('/questions/{question}/toggle-active', [QuestionController::class, 'toggleActive'])->name('questions.toggleActive');
    });

    // Quản lý Exams (cần role admin hoặc teacher)
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('exams', ExamController::class)->except(['show']);
        Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::patch('/exams/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('exams.togglePublish');
        Route::get('/exams/questions', [ExamController::class, 'getQuestionsByTopic'])->name('exams.questions');
    });
});

// =====================
// ROUTE QUẢN TRỊ (Admin Routes) - CẦN ĐĂNG NHẬP VÀ CÓ ROLE ADMIN
// =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        // Dashboard (trang chính)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Quản lý Roles
        Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::patch('/roles/{user}', [RoleManagementController::class, 'update'])->name('roles.update');

        // Quản lý Users
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        // Quản lý Topics
        Route::get('/topics', [TopicManagementController::class, 'index'])->name('topics.index');
        Route::post('/topics', [TopicManagementController::class, 'store'])->name('topics.store');
        Route::put('/topics/{topic}', [TopicManagementController::class, 'update'])->name('topics.update');
        Route::delete('/topics/{topic}', [TopicManagementController::class, 'destroy'])->name('topics.destroy');

        // Báo cáo
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
