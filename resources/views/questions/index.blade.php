@extends('admin.layout')

@section('title', 'Quản lý Câu hỏi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Quản lý Câu hỏi</h1>
    <div class="flex gap-3">
        @php
            $hasAiConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_QUESTION_GENERATION)->exists();
        @endphp
        @if($hasAiConfig)
        <a href="{{ route('questions.generate-ai.form') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-lg">auto_awesome</span>
            Tạo bằng AI
        </a>
        @endif
        <a href="{{ route('questions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add</span>
            Thêm câu hỏi
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Từ khóa..." 
                class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Chủ đề</label>
            <select name="topic_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Độ khó</label>
            <select name="difficulty" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Loại</label>
            <select name="type" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Một lựa chọn</option>
                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Nhiều lựa chọn</option>
                <option value="fill_in_blank" {{ request('type') == 'fill_in_blank' ? 'selected' : '' }}>Điền trống</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-xl hover:bg-slate-800 transition-colors">
                Lọc
            </button>
            <a href="{{ route('questions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Questions Table -->
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Câu hỏi</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Loại</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Độ khó</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Chủ đề</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($questions as $question)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <p class="text-sm text-slate-800 line-clamp-2">{{ Str::limit($question->content, 80) }}</p>
                    <p class="text-xs text-slate-400 mt-1">Bởi: {{ $question->creator?->name ?? 'N/A' }}</p>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($question->type == 'single_choice')
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Một lựa chọn</span>
                    @elseif($question->type == 'multiple_choice')
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Nhiều lựa chọn</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Điền trống</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    @if($question->difficulty == 'easy')
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Dễ</span>
                    @elseif($question->difficulty == 'medium')
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Trung bình</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Khó</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm text-slate-600">{{ $question->topic?->name ?? 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <form action="{{ route('questions.toggleActive', $question) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-2 py-1 rounded-full text-xs font-medium {{ $question->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $question->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('questions.show', $question) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Xem">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                        <a href="{{ route('questions.edit', $question) }}" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Sửa">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form action="{{ route('questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                    <span class="material-symbols-outlined text-4xl text-slate-300">quiz</span>
                    <p class="mt-2">Chưa có câu hỏi nào</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($questions->hasPages())
<div class="mt-4">
    {{ $questions->appends(request()->query())->links() }}
</div>
@endif
@endsection
