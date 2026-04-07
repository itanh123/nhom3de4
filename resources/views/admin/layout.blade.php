<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Quiz Lumina' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --sidebar-bg: #0f172a;
            --content-bg: #f8fafc;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--content-bg);
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            padding-top: 1.5rem;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.8rem 1.2rem;
            margin: 0.2rem 0.8rem;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.2rem;
        }

        .main-content {
            margin-left: {{ auth()->check() ? '260px' : '0' }};
            min-height: 100vh;
            background: white;
            transition: margin 0.3s ease;
        }

        .top-bar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 1.25rem 2rem;
            border-bottom: 
            1px solid #f1f5f9;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .content-area {
            padding: 2.5rem;
            animation: slideInUp 0.6s ease-out;
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        /* Animations */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }

        .animate-up { animation: slideInUp 0.5s ease-out; }
        
        .card {
            border: none;
            border-radius: 1.25rem;
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 0.75rem;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: scale(1.03);
            filter: brightness(1.1);
        }

        .btn-pulse {
            animation: pulse-glow 2s infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="sidebar">
        <div class="px-3 mb-4">
            <h1 class="brand-text">Quiz Lumina</h1>
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
                <a href="{{ route('student.exams.index') }}" class="nav-link {{ request()->routeIs('student.exams.index') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i>Bài thi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('student.exams.ai-generator') }}" class="nav-link {{ request()->routeIs('student.exams.ai-generator') ? 'active' : '' }}">
                    <i class="bi bi-stars me-2 text-warning"></i>Luyện tập AI
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

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
