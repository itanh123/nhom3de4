@extends('admin.layout')

@section('title', 'Xem trước câu hỏi AI')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Xem trước câu hỏi AI</h1>
        <p class="text-slate-500 mt-1">Chọn các câu hỏi bạn muốn lưu vào hệ thống</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('questions.generate-ai.form') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add</span>
            Tạo thêm
        </a>
        <button type="submit" form="mainForm" class="px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 flex items-center gap-2" id="saveBtn">
            <span class="material-symbols-outlined text-lg">save</span>
            Lưu câu hỏi đã chọn
        </button>
    </div>
</div>

<!-- Stats -->
<div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-6">
        <div>
            <span class="text-sm text-slate-500">Tổng câu hỏi</span>
            <p class="text-2xl font-bold text-slate-800">{{ count($questions) }}</p>
        </div>
        <div>
            <span class="text-sm text-slate-500">Chủ đề</span>
            <p class="text-lg font-semibold text-slate-800">{{ $topic?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm text-slate-500">Độ khó</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium 
                @if($difficulty === 'easy') bg-green-100 text-green-700
                @elseif($difficulty === 'medium') bg-yellow-100 text-yellow-700
                @else bg-red-100 text-red-700 @endif">
                {{ $difficulty === 'easy' ? 'Dễ' : ($difficulty === 'medium' ? 'Trung bình' : 'Khó') }}
            </span>
        </div>
    </div>
    <div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-slate-700">Chọn tất cả</span>
        </label>
    </div>
</div>

@if(session('ai_success'))
<div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 flex items-center gap-2">
    <span class="material-symbols-outlined">check_circle</span> {{ session('ai_success') }}
</div>
@endif

<!-- Questions List -->
<form action="{{ route('questions.generate-ai.save') }}" method="POST" id="mainForm">
    @csrf
    <div class="space-y-4" id="questionsList">
        @foreach($questions as $index => $q)
        <div class="bg-white border border-slate-200 rounded-2xl p-6 question-item" data-index="{{ $index }}">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <label class="flex items-center">
                        <input type="checkbox" name="selected_questions[]" value="{{ $index }}" 
                            class="question-checkbox w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500" checked>
                    </label>
                </div>
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                    </div>
                    <p class="text-lg font-medium text-slate-800 mb-4">{{ $q['content'] }}</p>
                    
                    <div class="space-y-2">
                        @foreach($q['answers'] as $ansIdx => $answer)
                        <div class="p-3 rounded-xl {{ $answer['is_correct'] ? 'bg-green-50 border-2 border-green-300' : 'bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $answer['is_correct'] ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                                    {{ chr(65 + $ansIdx) }}
                                </span>
                                <span class="text-slate-700">{{ $answer['option_text'] }}</span>
                                @if($answer['is_correct'])
                                    <span class="ml-auto px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                        ✓ Đáp án đúng
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</form>

<script>
let isSubmitting = false;

document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSaveButton();
});

document.querySelectorAll('.question-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSaveButton);
});

function updateSaveButton() {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    const btn = document.getElementById('saveBtn');
    if (btn) {
        btn.disabled = checked === 0;
        btn.innerHTML = `<span class="material-symbols-outlined text-lg">save</span> Lưu ${checked} câu hỏi đã chọn`;
    }
}

document.getElementById('mainForm')?.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    if (checked === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất 1 câu hỏi để lưu!');
        return;
    }
    if (isSubmitting) {
        e.preventDefault();
        return;
    }
    isSubmitting = true;
    const btn = document.getElementById('saveBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="material-symbols-outlined text-lg">hourglass_empty</span> Đang lưu...`;
    }
});

updateSaveButton();
</script>
@endsection
