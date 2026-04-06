<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $title ?? 'Lumina Quiz Admin' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, sans-serif; }
        .brand { font-family: "Plus Jakarta Sans", sans-serif; }
        .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 450, "GRAD" 0, "opsz" 24; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<aside class="fixed left-0 top-0 h-screen w-64 bg-white border-r border-slate-200 p-4 z-30">
    <div class="mb-6">
        <h1 class="brand text-xl font-extrabold text-blue-700">Lumina Admin</h1>
        <p class="text-xs text-slate-500 uppercase tracking-widest">Quiz Generator</p>
    </div>
    <nav class="space-y-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">dashboard</span><span>Dashboard</span>
        </a>
        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('admin.roles.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">admin_panel_settings</span><span>Role Management</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">group</span><span>User Management</span>
        </a>
        <a href="{{ route('admin.topics.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('admin.topics.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">account_tree</span><span>Topic Management</span>
        </a>
        <a href="{{ route('questions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('questions.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">quiz</span><span>Question Management</span>
        </a>
        <a href="{{ route('exams.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('exams.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">assignment</span><span>Exam Management</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-slate-100 text-slate-700' }}">
            <span class="material-symbols-outlined text-base">insights</span><span>Reports</span>
        </a>
    </nav>
</aside>

<header class="fixed top-0 left-64 right-0 h-16 bg-white/90 backdrop-blur border-b border-slate-200 z-20 px-6 flex items-center justify-between">
    <h2 class="brand font-bold text-slate-800">{{ $title ?? 'Admin' }}</h2>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                <span class="text-xs font-bold text-blue-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-base">logout</span>
                Đăng xuất
            </button>
        </form>
    </div>
</header>

<main class="ml-64 pt-20 p-6">
    @if (session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
