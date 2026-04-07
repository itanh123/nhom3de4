@extends('admin.layout')

@section('title', 'Quản lý Kết quả')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Quản lý Kết quả</h1>
</div>

<!-- Filters -->
<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên học sinh..." 
                class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Bài thi</label>
            <select name="exam_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Kết quả</label>
            <select name="passed" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="1" {{ request('passed') === '1' ? 'selected' : '' }}>Đạt</option>
                <option value="0" {{ request('passed') === '0' ? 'selected' : '' }}>Không đạt</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-xl hover:bg-slate-800">
                Lọc
            </button>
            <a href="{{ route('results.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Học sinh</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Bài thi</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Điểm</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Kết quả</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Ngày nộp</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($results as $result)
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-700">{{ substr($result->student?->name ?? 'N', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $result->student?->name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">{{ $result->student?->email ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-slate-800">{{ $result->exam?->title ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-400">{{ $result->exam?->topic?->name ?? '' }}</p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-bold text-slate-800">{{ $result->score_pct }}%</span>
                    <p class="text-xs text-slate-400">{{ $result->correct_count }}/{{ $result->total_questions }}</p>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($result->passed)
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Đạt</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Không đạt</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center text-sm text-slate-600">
                    {{ $result->submitted_at?->format('d/m/Y H:i') ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('results.show', $result) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                        Xem
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                    <span class="material-symbols-outlined text-4xl text-slate-300">assignment</span>
                    <p class="mt-2">Chưa có kết quả nào</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($results->hasPages())
<div class="mt-4">
    {{ $results->appends(request()->query())->links() }}
</div>
@endif
@endsection
