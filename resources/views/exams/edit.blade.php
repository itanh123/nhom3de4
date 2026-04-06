@extends('admin.layout')

@section('title', 'Sửa Bài thi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Sửa Bài thi</h1>
</div>

<form action="{{ route('exams.update', $exam) }}" method="POST" id="examForm">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Exam Info -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">info</span>
                    Thông tin bài thi
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Chủ đề <span class="text-red-500">*</span></label>
                        <select name="topic_id" id="topicSelect" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('topic_id') border-red-500 @enderror">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $exam->topic_id) == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror" placeholder="Nhập tiêu đề bài thi...">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Mô tả bài thi (tùy chọn)...">{{ old('description', $exam->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Thời gian (phút)</label>
                            <input type="number" name="duration_mins" value="{{ old('duration_mins', $exam->duration_mins) }}" min="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('duration_mins') border-red-500 @enderror" placeholder="30">
                            @error('duration_mins')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Điểm đạt (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="pass_score" value="{{ old('pass_score', $exam->pass_score) }}" required min="0" max="100" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('pass_score') border-red-500 @enderror" placeholder="60">
                            @error('pass_score')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('start_time') border-red-500 @enderror">
                            @error('start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('end_time') border-red-500 @enderror">
                            @error('end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="draft" {{ old('status', $exam->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                            <option value="open" {{ old('status', $exam->status) == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="closed" {{ old('status', $exam->status) == 'closed' ? 'selected' : '' }}>Đóng</option>
                            <option value="archived" {{ old('status', $exam->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="shuffle_q" id="shuffle_q" value="1" {{ old('shuffle_q', $exam->shuffle_q) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="shuffle_q" class="text-sm font-medium text-slate-700">Xáo trộn câu hỏi</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="shuffle_a" id="shuffle_a" value="1" {{ old('shuffle_a', $exam->shuffle_a) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="shuffle_a" class="text-sm font-medium text-slate-700">Xáo trộn đáp án</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="show_explain" id="show_explain" value="1" {{ old('show_explain', $exam->show_explain) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="show_explain" class="text-sm font-medium text-slate-700">Hiện giải thích sau khi nộp bài</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $exam->is_published) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="is_published" class="text-sm font-medium text-slate-700">Công khai (cho phép học sinh thấy)</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Selection -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">quiz</span>
                    Chọn câu hỏi
                </h3>

                <div id="topicPrompt" class="hidden text-center py-8 text-slate-500">
                    <span class="material-symbols-outlined text-4xl text-slate-300">touch_app</span>
                    <p class="mt-2">Vui lòng chọn chủ đề trước để hiển thị câu hỏi</p>
                </div>

                <div id="questionsContainer">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-slate-600">Đã chọn: <span id="selectedCount" class="font-semibold text-blue-600">{{ $exam->examQuestions->count() }}</span> câu hỏi</p>
                        <button type="button" id="selectAllBtn" class="text-sm text-blue-600 hover:text-blue-700">Chọn tất cả</button>
                    </div>
                    <div id="questionsList" class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($questions as $q)
                        <div class="p-3 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors question-item">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" 
                                    class="w-5 h-5 mt-0.5 text-blue-600 border-slate-300 rounded question-checkbox"
                                    {{ $exam->examQuestions->where('question_id', $q->id)->first() ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-800">{{ Str::limit($q->content, 100) }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($q->type == 'single_choice')
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Một lựa chọn</span>
                                        @elseif($q->type == 'multiple_choice')
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">Nhiều lựa chọn</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Điền trống</span>
                                        @endif
                                        <span class="text-xs text-slate-400">{{ $q->answers->count() }} đáp án</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('question_ids')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Hướng dẫn</h3>
                <div class="space-y-3 text-sm text-slate-600">
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-base">info</span>
                        <span>Chọn chủ đề để hiển thị câu hỏi</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-base">check</span>
                        <span>Đánh dấu các câu hỏi muốn thêm vào bài thi</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-purple-600 text-base">star</span>
                        <span>Tối thiểu 1 câu hỏi</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Thao tác</h3>
                <div class="space-y-3">
                    <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('exams.show', $exam) }}" class="block w-full px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors font-medium text-center">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let selectedQuestions = new Set();

@foreach($exam->examQuestions as $eq)
selectedQuestions.add({{ $eq->question_id }});
@endforeach

document.querySelectorAll('.question-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const value = parseInt(this.value);
        if (this.checked) {
            selectedQuestions.add(value);
        } else {
            selectedQuestions.delete(value);
        }
        document.getElementById('selectedCount').textContent = selectedQuestions.size;
    });
});

document.getElementById('topicSelect').addEventListener('change', function() {
    const topicId = this.value;
    const prompt = document.getElementById('topicPrompt');
    const container = document.getElementById('questionsContainer');
    const list = document.getElementById('questionsList');
    
    if (!topicId) {
        prompt.classList.remove('hidden');
        container.classList.add('hidden');
        return;
    }
    
    prompt.classList.add('hidden');
    container.classList.remove('hidden');
    list.innerHTML = '<div class="text-center py-4"><span class="material-symbols-outlined text-2xl text-slate-300 animate-spin">progress_activity</span></div>';
    
    fetch(`/exams/questions?topic_id=${topicId}`)
        .then(res => res.json())
        .then(questions => {
            list.innerHTML = '';
            if (questions.length === 0) {
                list.innerHTML = '<div class="text-center py-4 text-slate-500">Không có câu hỏi nào trong chủ đề này</div>';
                return;
            }
            
            questions.forEach(q => {
                const typeLabels = {
                    'single_choice': 'Một lựa chọn',
                    'multiple_choice': 'Nhiều lựa chọn',
                    'fill_in_blank': 'Điền trống'
                };
                
                const div = document.createElement('div');
                div.className = 'p-3 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors question-item';
                div.innerHTML = `
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="question_ids[]" value="${q.id}" 
                            class="w-5 h-5 mt-0.5 text-blue-600 border-slate-300 rounded question-checkbox"
                            ${selectedQuestions.has(q.id) ? 'checked' : ''}>
                        <div class="flex-1">
                            <p class="text-sm text-slate-800">${escapeHtml(q.content)}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">${typeLabels[q.type] || q.type}</span>
                                <span class="text-xs text-slate-400">${q.answers ? q.answers.length : 0} đáp án</span>
                            </div>
                        </div>
                    </label>
                `;
                
                div.addEventListener('click', (e) => {
                    if (e.target.type !== 'checkbox') {
                        const checkbox = div.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
                
                list.appendChild(div);
            });
            
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const value = parseInt(this.value);
                    if (this.checked) {
                        selectedQuestions.add(value);
                    } else {
                        selectedQuestions.delete(value);
                    }
                    document.getElementById('selectedCount').textContent = selectedQuestions.size;
                });
            });
        });
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

document.getElementById('examForm').addEventListener('submit', function(e) {
    const checkedCount = document.querySelectorAll('.question-checkbox:checked').length;
    if (checkedCount === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất 1 câu hỏi!');
        return;
    }
});
</script>
@endsection
