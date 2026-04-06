@extends('admin.layout')

@section('title', 'Quản lý Bài thi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Quản lý Bài thi</h1>
    <a href="{{ route('exams.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">add</span>
        Tạo bài thi
    </a>
</div>

<!-- Filters -->
<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề bài thi..." 
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
            <label class="block text-sm font-medium text-slate-600 mb-1">Trạng thái</label>
            <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Mở</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đóng</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-xl hover:bg-slate-800 transition-colors">
                Lọc
            </button>
            <a href="{{ route('exams.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Exams Table -->
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Bài thi</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Chủ đề</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Câu hỏi</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Thời gian</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Công khai</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($exams as $exam)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-slate-800">{{ Str::limit($exam->title, 50) }}</p>
                    <p class="text-xs text-slate-400 mt-1">Bởi: {{ $exam->creator?->name ?? 'N/A' }}</p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm text-slate-600">{{ $exam->topic?->name ?? 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                        {{ $exam->examQuestions->count() }} câu
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm text-slate-600">{{ $exam->duration_mins ?? 'N/A' }} phút</span>
                </td>
                <td class="px-6 py-4 text-center">
                    @switch($exam->status)
                        @case('draft')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Nháp</span>
                            @break
                        @case('scheduled')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Đã lên lịch</span>
                            @break
                        @case('open')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Mở</span>
                            @break
                        @case('closed')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Đóng</span>
                            @break
                        @case('archived')
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Lưu trữ</span>
                            @break
                    @endswitch
                </td>
                <td class="px-6 py-4 text-center">
                    <form action="{{ route('exams.togglePublish', $exam) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $exam->is_published ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $exam->is_published ? 'Công khai' : 'Riêng tư' }}
                            </span>
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('exams.show', $exam) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Xem">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                        <a href="{{ route('exams.edit', $exam) }}" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Sửa">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bài thi này?')">
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
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                    <span class="material-symbols-outlined text-4xl text-slate-300">assignment</span>
                    <p class="mt-2">Chưa có bài thi nào</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($exams->hasPages())
<div class="mt-4">
    {{ $exams->appends(request()->query())->links() }}
</div>
@endif
@endsection
