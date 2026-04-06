@extends('admin.layout')

@section('title', 'Chi tiết Bài thi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Chi tiết Bài thi</h1>
    <div class="flex gap-2">
        <a href="{{ route('exams.edit', $exam) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">edit</span>
            Sửa
        </a>
        <a href="{{ route('exams.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Exam Info -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <div class="flex items-start justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">assignment</span>
                    {{ $exam->title }}
                </h3>
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
            </div>
            
            @if($exam->description)
            <p class="text-slate-600 mb-4">{{ $exam->description }}</p>
            @endif
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Chủ đề</p>
                    <p class="font-semibold text-slate-800">{{ $exam->topic?->name ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Thời gian</p>
                    <p class="font-semibold text-slate-800">{{ $exam->duration_mins ?? 'N/A' }} phút</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Điểm đạt</p>
                    <p class="font-semibold text-slate-800">{{ $exam->pass_score }}%</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Câu hỏi</p>
                    <p class="font-semibold text-slate-800">{{ $exam->examQuestions->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Schedule Info -->
        @if($exam->start_time || $exam->end_time)
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">schedule</span>
                Lịch thi
            </h3>
            <div class="grid grid-cols-2 gap-4">
                @if($exam->start_time)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-xs text-yellow-700">Bắt đầu</p>
                    <p class="font-semibold text-yellow-900">{{ $exam->start_time->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @if($exam->end_time)
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs text-red-700">Kết thúc</p>
                    <p class="font-semibold text-red-900">{{ $exam->end_time->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Questions List -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">quiz</span>
                Danh sách câu hỏi ({{ $exam->examQuestions->count() }})
            </h3>

            <div class="space-y-4">
                @foreach($exam->examQuestions->sortBy('display_order') as $index => $eq)
                <div class="p-4 bg-slate-50 rounded-xl">
                    <div class="flex items-start gap-3 mb-3">
                        <span class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1">
                            <p class="text-slate-800 font-medium">{{ Str::limit($eq->question->content, 150) }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                @if($eq->question->type == 'single_choice')
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Một lựa chọn</span>
                                @elseif($eq->question->type == 'multiple_choice')
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">Nhiều lựa chọn</span>
                                @else
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Điền trống</span>
                                @endif
                                <span class="px-2 py-0.5 rounded text-xs {{ $eq->question->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($eq->question->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($eq->question->difficulty) }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $eq->point }} điểm</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($eq->question->answers && $eq->question->answers->count() > 0)
                    <div class="ml-11 space-y-2">
                        @foreach($eq->question->answers->sortBy('display_order') as $ansIndex => $answer)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-6 h-6 rounded-full {{ $answer->is_correct ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center text-xs font-medium">
                                {{ chr(65 + $ansIndex) }}
                            </span>
                            <span class="{{ $answer->is_correct ? 'text-emerald-700 font-medium' : 'text-slate-600' }}">
                                {{ $answer->option_text }}
                            </span>
                            @if($answer->is_correct)
                            <span class="text-emerald-600">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
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
                    <p class="text-slate-500">Người tạo</p>
                    <p class="font-medium text-slate-800">{{ $exam->creator?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Ngày tạo</p>
                    <p class="font-medium text-slate-800">{{ $exam->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Cập nhật</p>
                    <p class="font-medium text-slate-800">{{ $exam->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Hiển thị</p>
                    <p class="font-medium">
                        @if($exam->shuffle_q)
                            <span class="text-emerald-600">Xáo trộn câu hỏi</span>
                        @else
                            <span class="text-slate-400">Theo thứ tự</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Thao tác</h3>
            <div class="space-y-3">
                <form action="{{ route('exams.togglePublish', $exam) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2.5 {{ $exam->is_published ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-xl transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">{{ $exam->is_published ? 'visibility_off' : 'visibility' }}</span>
                        {{ $exam->is_published ? 'Ẩn bài thi' : 'Công khai' }}
                    </button>
                </form>
                <form action="{{ route('exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài thi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Xóa bài thi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
