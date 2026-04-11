<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quiz Generator')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338CA;
        }
        body { background: #f3f4f6; }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, #7C3AED 100%); }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: 240px;
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 20px 0;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #6b7280;
            padding: 12px 24px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover { background: #f3f4f6; color: var(--primary-color); }
        .sidebar .nav-link.active { background: #EEF2FF; color: var(--primary-color); font-weight: 600; }
        .sidebar .nav-link i { width: 24px; }
        .main-content { margin-left: 240px; padding: 24px; min-height: calc(100vh - 56px); }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Quiz Generator
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            <span class="badge bg-light text-dark ms-1">{{ ucfirst(Auth::user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @auth
    @if(Auth::user()->role === 'student')
    <nav class="sidebar">
        <div class="px-3 mb-4">
            <small class="text-muted text-uppercase fw-bold">Học sinh</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" href="{{ route('student.exams.index') }}">
                    <i class="bi bi-journal-text me-2"></i>Bài thi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}" href="{{ route('student.results.index') }}">
                    <i class="bi bi-bar-chart me-2"></i>Kết quả
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chat AI (Hỗ trợ)
                </a>
            </li>
        </ul>
    </nav>
    @elseif(Auth::user()->role === 'teacher')
    <nav class="sidebar">
        <div class="px-3 mb-4">
            <small class="text-muted text-uppercase fw-bold">Giáo viên</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
                    <i class="bi bi-file-earmark-text me-2"></i>Quản lý bài thi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}">
                    <i class="bi bi-question-circle me-2"></i>Quản lý câu hỏi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                    <i class="bi bi-file-earmark me-2"></i>Tài liệu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('imports.*') ? 'active' : '' }}" href="{{ route('imports.index') }}">
                    <i class="bi bi-upload me-2"></i>Nhập câu hỏi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('results.index') ? 'active' : '' }}" href="{{ route('results.index') }}">
                    <i class="bi bi-clipboard-data me-2"></i>Xem kết quả
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chat AI (Hỗ trợ)
                </a>
            </li>
        </ul>
    </nav>
    @elseif(Auth::user()->role === 'admin')
    <nav class="sidebar">
        <div class="px-3 mb-4">
            <small class="text-muted text-uppercase fw-bold">Quản trị</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people me-2"></i>Người dùng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                    <i class="bi bi-shield-check me-2"></i>Phân quyền
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}" href="{{ route('admin.topics.index') }}">
                    <i class="bi bi-folder me-2"></i>Chủ đề
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
                    <i class="bi bi-file-earmark-text me-2"></i>Bài thi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}">
                    <i class="bi bi-question-circle me-2"></i>Câu hỏi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                    <i class="bi bi-file-earmark me-2"></i>Tài liệu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('imports.*') ? 'active' : '' }}" href="{{ route('imports.index') }}">
                    <i class="bi bi-upload me-2"></i>Nhập câu hỏi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('results.index') ? 'active' : '' }}" href="{{ route('results.index') }}">
                    <i class="bi bi-clipboard-data me-2"></i>Kết quả
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ai-configs.*') ? 'active' : '' }}" href="{{ route('admin.ai-configs.index') }}">
                    <i class="bi bi-gear me-2"></i>Cấu hình AI
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
                    <i class="bi bi-chat-left-dots me-2"></i>Quản lý Chat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ai-agent.*') ? 'active' : '' }}" href="{{ route('admin.ai-agent.index') }}">
                    <i class="bi bi-robot me-2"></i>Chat Admin (AI CRUD)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chat AI (Hỗ trợ)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                    <i class="bi bi-clock-history me-2"></i>Nhật ký hoạt động
                </a>
            </li>
        </ul>
    </nav>
    @endif
    @endauth

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
