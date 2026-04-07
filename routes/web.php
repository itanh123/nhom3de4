<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ResultController as StudentResultController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TopicManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminAgentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AiConfigController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ChatManagementController;

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

// Trang chủ - redirect theo role
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role === 'teacher') {
            return redirect()->route('exams.index');
        }
        return redirect()->route('student.exams.index');
    }
    return redirect()->route('login');
});

// =====================
// ROUTE CẦU HỌC SINH (Student Routes)
// =====================
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () {
        // AI Generator
        Route::get('/exams/ai-generator', [\App\Http\Controllers\Student\AiExamController::class, 'create'])->name('exams.ai-generator');
        Route::post('/exams/ai-generator', [\App\Http\Controllers\Student\AiExamController::class, 'generate'])->name('exams.ai-generator.submit');

        // Exams
        Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
        Route::get('/exams/{exam}/take', [StudentExamController::class, 'take'])->name('exams.take');
        Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');

        // Results
        Route::get('/results', [StudentResultController::class, 'index'])->name('results.index');
        Route::get('/results/{result}', [StudentResultController::class, 'show'])->name('results.show');
        Route::post('/results/{result}/rate-ai', [StudentResultController::class, 'rateAi'])->name('results.rate-ai');
        Route::post('/results/{result}/ai-explain', [StudentResultController::class, 'generateExplanation'])->name('results.ai-explain');
        Route::post('/results/{result}/ai-learning-path', [StudentResultController::class, 'generateLearningPath'])->name('results.ai-learning-path');
    });

// =====================
// ROUTE CẦU HỌC SINH/GIÁO VIÊN LÀM BÀI THI
// =====================
Route::middleware(['auth'])->group(function () {
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

        // AI Question Generation
        Route::get('/questions-generate-ai', [QuestionController::class, 'generateAiForm'])->name('questions.generate-ai.form');
        Route::post('/questions-generate-ai', [QuestionController::class, 'generateAi'])->name('questions.generate-ai');
        Route::get('/questions-generate-ai/preview', [QuestionController::class, 'previewAiQuestions'])->name('questions.generate-ai.preview');
        Route::post('/questions-generate-ai/save', [QuestionController::class, 'saveAiQuestions'])->name('questions.generate-ai.save');
    });

    // Quản lý Exams (cần role admin hoặc teacher)
    Route::middleware('role:admin,teacher')->group(function () {
        // Static routes MUST come before resource/parameterized routes
        Route::get('/exams/questions', [ExamController::class, 'getQuestionsByTopic'])->name('exams.questions');
        Route::resource('exams', ExamController::class)->except(['show']);
        Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::patch('/exams/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('exams.togglePublish');
    });

    // Kết quả thi (admin, teacher)
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/results', [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{result}', [ResultController::class, 'show'])->name('results.show');
        Route::post('/results/{result}/ai-evaluate', [ResultController::class, 'aiEvaluate'])->name('results.ai-evaluate');
        Route::post('/results/{result}/ai-learning-path', [ResultController::class, 'aiLearningPath'])->name('results.ai-learning-path');
    });
});

    // Chat AI
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'chat'])->name('chat.send');
    Route::get('/chat/history/{session}', [ChatController::class, 'history'])->name('chat.history');
    Route::get('/chat/sessions', [ChatController::class, 'sessions'])->name('chat.sessions');
    Route::delete('/chat/session/{session}', [ChatController::class, 'deleteSession'])->name('chat.session.delete');

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

        // AI Configs (chỉ admin)
        Route::prefix('ai-configs')->name('ai-configs.')->group(function () {
            Route::get('/', [AiConfigController::class, 'index'])->name('index');
            Route::get('/create', [AiConfigController::class, 'create'])->name('create');
            Route::post('/', [AiConfigController::class, 'store'])->name('store');
            Route::get('/{aiConfig}', [AiConfigController::class, 'show'])->name('show');
            Route::get('/{aiConfig}/edit', [AiConfigController::class, 'edit'])->name('edit');
            Route::put('/{aiConfig}', [AiConfigController::class, 'update'])->name('update');
            Route::delete('/{aiConfig}', [AiConfigController::class, 'destroy'])->name('destroy');
            Route::patch('/{aiConfig}/toggle', [AiConfigController::class, 'toggle'])->name('toggle');
        });

        // Activity Logs (chỉ admin)
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
        });

        // AI Agent (chỉ admin)
        Route::prefix('ai-agent')->name('ai-agent.')->group(function () {
            Route::get('/', [AdminAgentController::class, 'index'])->name('index');
            Route::post('/chat', [AdminAgentController::class, 'chat'])->name('chat');
            Route::post('/execute', [AdminAgentController::class, 'execute'])->name('execute');
            Route::get('/history', [AdminAgentController::class, 'history'])->name('history');
            Route::get('/history/{id}', [AdminAgentController::class, 'show'])->name('show');
        });

        // Chat Management (Full CRUD - Admin only)
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatManagementController::class, 'index'])->name('index');
            Route::get('/create', [ChatManagementController::class, 'create'])->name('create');
            Route::post('/', [ChatManagementController::class, 'store'])->name('store');
            Route::get('/stats', [ChatManagementController::class, 'stats'])->name('stats');
            Route::get('/{session}', [ChatManagementController::class, 'show'])->name('show');
            Route::put('/{session}', [ChatManagementController::class, 'update'])->name('update');
            Route::delete('/{session}', [ChatManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{session}/send', [ChatManagementController::class, 'sendMessage'])->name('send');
            Route::post('/{session}/star', [ChatManagementController::class, 'toggleStar'])->name('star');
            Route::post('/{session}/clear', [ChatManagementController::class, 'clearMessages'])->name('clear');
            Route::get('/{session}/export', [ChatManagementController::class, 'export'])->name('export');
        });
    });

// =====================
// ROUTE DOCUMENTS (admin, teacher)
// =====================
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
    });

    // Imports (admin, teacher)
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::get('/create', [ImportController::class, 'create'])->name('create');
        Route::post('/', [ImportController::class, 'store'])->name('store');
        Route::get('/template', [ImportController::class, 'template'])->name('template');
        Route::get('/{import}', [ImportController::class, 'show'])->name('show');
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
