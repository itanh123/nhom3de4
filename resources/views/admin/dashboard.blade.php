@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    {{-- Dashboard Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.users.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg hover:border-blue-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Người dùng</p>
                    <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-blue-600">group</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Quản lý tài khoản người dùng</p>
        </a>

        <a href="{{ route('admin.topics.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg hover:border-emerald-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Chủ đề</p>
                    <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_topics'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-emerald-600">account_tree</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Quản lý chủ đề bài thi</p>
        </a>

        <a href="{{ route('questions.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg hover:border-purple-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Câu hỏi</p>
                    <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_questions'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-purple-600">quiz</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Ngân hàng câu hỏi</p>
        </a>

        <a href="{{ route('exams.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg hover:border-orange-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Bài thi</p>
                    <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_exams'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-orange-600">assignment</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Quản lý bài thi trắc nghiệm</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Users by Role --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">pie_chart</span>
                Người dùng theo vai trò
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                        <span class="text-sm text-slate-600">Admin</span>
                    </div>
                    <span class="font-semibold text-slate-800">{{ $userByRole['admin'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-slate-600">Giáo viên</span>
                    </div>
                    <span class="font-semibold text-slate-800">{{ $userByRole['teacher'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-sm text-slate-600">Học sinh</span>
                    </div>
                    <span class="font-semibold text-slate-800">{{ $userByRole['student'] ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Tổng cộng</span>
                    <span class="font-bold text-slate-800">{{ $stats['total_users'] }}</span>
                </div>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">person_search</span>
                Người dùng mới
            </h3>
            @if($recentUsers->isEmpty())
                <p class="text-sm text-slate-400 text-center py-4">Chưa có người dùng nào</p>
            @else
                <div class="space-y-3">
                    @foreach($recentUsers as $u)
                        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center">
                                    <span class="text-xs font-bold text-slate-600">{{ substr($u->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $u->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $u->email }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $u->role === 'teacher' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $u->role === 'student' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.users.index') }}" class="block mt-4 text-center text-sm text-blue-600 font-semibold hover:text-blue-700">
                    Xem tất cả →
                </a>
            @endif
        </div>

        {{-- Recent Topics --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">library_books</span>
                Chủ đề mới
            </h3>
            @if($recentTopics->isEmpty())
                <p class="text-sm text-slate-400 text-center py-4">Chưa có chủ đề nào</p>
            @else
                <div class="space-y-3">
                    @foreach($recentTopics as $topic)
                        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $topic->name }}</p>
                                <p class="text-xs text-slate-400">@ {{ $topic->creator?->name ?? 'Unknown' }}</p>
                            </div>
                            <span class="text-xs text-slate-400 ml-2">{{ $topic->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.topics.index') }}" class="block mt-4 text-center text-sm text-blue-600 font-semibold hover:text-blue-700">
                    Xem tất cả →
                </a>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white">
        <h3 class="font-bold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">bolt</span>
            Thao tác nhanh
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-xl hover:bg-white/20 transition-colors">
                <span class="material-symbols-outlined text-2xl">person_add</span>
                <span class="text-sm font-medium">Thêm người dùng</span>
            </a>
            <a href="{{ route('admin.topics.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-xl hover:bg-white/20 transition-colors">
                <span class="material-symbols-outlined text-2xl">create_new_folder</span>
                <span class="text-sm font-medium">Tạo chủ đề</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-xl hover:bg-white/20 transition-colors">
                <span class="material-symbols-outlined text-2xl">manage_accounts</span>
                <span class="text-sm font-medium">Phân quyền</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-xl hover:bg-white/20 transition-colors">
                <span class="material-symbols-outlined text-2xl">analytics</span>
                <span class="text-sm font-medium">Xem báo cáo</span>
            </a>
        </div>
    </div>
@endsection
