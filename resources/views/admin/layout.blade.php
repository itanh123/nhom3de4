<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lumina Quiz Admin' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f3f4f6;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #1e293b;
            color: white;
            padding-top: 1rem;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            background: #334155;
            color: white;
        }
        .sidebar .nav-link.active {
            background: #4f46e5;
            color: white;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .main-content {
            margin-left: {{ auth()->check() ? '250px' : '0' }};
            min-height: 100vh;
        }
        .top-bar {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .content-area {
            padding: 1.5rem;
        }
        .brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            color: #4f46e5;
        }
        .sidebar-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1rem 0.5rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="sidebar">
        <div class="px-3 mb-4">
            <h1 class="brand-text">Quiz Admin</h1>
            <small class="text-muted">{{ auth()->user()->role === 'admin' ? 'Quản trị viên' : (auth()->user()->role === 'teacher' ? 'Giáo viên' : 'Học sinh') }}</small>
        </div>

        <ul class="nav flex-column">
            @if(auth()->user()?->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                </a>
            </li>
            @endif

            @if(auth()->user()?->role === 'student')
            <li class="nav-item">
                <a href="{{ route('student.exams.index') }}" class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i>Bài thi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('student.results.index') }}" class="nav-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data me-2"></i>Kết quả học tập
                </a>
            </li>
            @endif
            
            @if(auth()->user()?->role === 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i>Phân quyền
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.topics.index') }}" class="nav-link {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                        <i class="bi bi-folder me-2"></i>Chủ đề
                    </a>
                </li>
            @endif
            
            @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'teacher')
                <li class="nav-item">
                    <a href="{{ route('questions.index') }}" class="nav-link {{ request()->routeIs('questions.*') ? 'active' : '' }}">
                        <i class="bi bi-list-question me-2"></i>Câu hỏi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('exams.index') }}" class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i>Quản lý Bài thi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('results.index') }}" class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check me-2"></i>Kết quả học sinh
                    </a>
                </li>
            @endif

            @if(auth()->user()?->role === 'admin')
                <div class="sidebar-section-title">Hệ thống</div>
                
                <li class="nav-item">
                    <a href="{{ route('admin.ai-configs.index') }}" class="nav-link {{ request()->routeIs('admin.ai-configs.*') ? 'active' : '' }}">
                        <i class="bi bi-robot me-2"></i>Cấu hình AI
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.chat.index') }}" class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots me-2"></i>Quản lý Chat
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.ai-agent.index') }}" class="nav-link {{ request()->routeIs('admin.ai-agent.*') ? 'active' : '' }}">
                        <i class="bi bi-robot me-2"></i>AI Agent
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i>Nhật ký hoạt động
                    </a>
                </li>
            @endif
        </ul>

        <div class="mt-auto p-3 border-top border-secondary">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                    <span class="text-white fw-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-medium text-truncate">{{ auth()->user()->name }}</div>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                </button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="main-content">
        @auth
        <header class="top-bar">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-semibold text-secondary">{{ $title ?? 'Admin' }}</h4>
                <div>
                    <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : 'warning' }}">
                        {{ auth()->user()->role === 'admin' ? 'Admin' : (auth()->user()->role === 'teacher' ? 'Giáo viên' : 'Học sinh') }}
                    </span>
                </div>
            </div>
        </header>
        @endauth

        <main class="content-area">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
