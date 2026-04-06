@extends('admin.layout')

@section('title', 'Chi tiết Câu hỏi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Chi tiết Câu hỏi</h1>
    <div class="flex gap-2">
        <a href="{{ route('questions.edit', $question) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">edit</span>
            Sửa
        </a>
        <a href="{{ route('questions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Question Info -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <div class="flex items-start justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">quiz</span>
                    Câu hỏi
                </h3>
                <div class="flex gap-2">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        @if($question->type == 'single_choice') bg-blue-100 text-blue-700
                        @elseif($question->type == 'multiple_choice') bg-purple-100 text-purple-700
                        @else bg-green-100 text-green-700 @endif">
                        @if($question->type == 'single_choice') Một lựa chọn
                        @elseif($question->type == 'multiple_choice') Nhiều lựa chọn
                        @else Điền trống @endif
                    </span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        @if($question->difficulty == 'easy') bg-green-100 text-green-700
                        @elseif($question->difficulty == 'medium') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        @if($question->difficulty == 'easy') Dễ
                        @elseif($question->difficulty == 'medium') Trung bình
                        @else Khó @endif
                    </span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $question->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $question->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                    </span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 rounded-xl mb-4">
                <p class="text-slate-800 text-lg">{!! nl2br(e($question->content)) !!}</p>
            </div>

            @if($question->explanation)
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h4 class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">lightbulb</span>
                    Giải thích
                </h4>
                <p class="text-slate-700">{{ $question->explanation }}</p>
            </div>
            @endif
        </div>

        <!-- Answers -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">list</span>
                Đáp án ({{ $question->answers->count() }})
            </h3>

            <div class="space-y-3">
                @foreach($question->answers->sortBy('display_order') as $index => $answer)
                <div class="p-4 rounded-xl flex items-center gap-3 {{ $answer->is_correct ? 'bg-emerald-50 border-2 border-emerald-300' : 'bg-slate-50 border border-slate-200' }}">
                    <div class="w-8 h-8 rounded-full {{ $answer->is_correct ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center font-bold text-sm">
                        {{ chr(65 + $index) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-slate-800">{{ $answer->option_text }}</p>
                    </div>
                    @if($answer->is_correct)
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">check_circle</span>
                        Đúng
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Thông tin</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-slate-500">Chủ đề</p>
                    <p class="font-medium text-slate-800">{{ $question->topic?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Người tạo</p>
                    <p class="font-medium text-slate-800">{{ $question->creator?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Ngày tạo</p>
                    <p class="font-medium text-slate-800">{{ $question->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Cập nhật</p>
                    <p class="font-medium text-slate-800">{{ $question->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Thao tác</h3>
            <div class="space-y-3">
                <form action="{{ route('questions.toggleActive', $question) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2.5 {{ $question->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-xl transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">{{ $question->is_active ? 'visibility_off' : 'visibility' }}</span>
                        {{ $question->is_active ? 'Ẩn câu hỏi' : 'Hiện câu hỏi' }}
                    </button>
                </form>
                <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Xóa câu hỏi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
