@extends('admin.layout')

@section('title', 'Thêm Câu hỏi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Thêm Câu hỏi mới</h1>
</div>

<form action="{{ route('questions.store') }}" method="POST" id="questionForm">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Question Info -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">info</span>
                    Thông tin câu hỏi
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Chủ đề <span class="text-red-500">*</span></label>
                        <select name="topic_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('topic_id') border-red-500 @enderror">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Loại câu hỏi <span class="text-red-500">*</span></label>
                            <select name="type" id="typeSelect" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                                <option value="single_choice" {{ old('type') == 'single_choice' ? 'selected' : '' }}>Một lựa chọn</option>
                                <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Nhiều lựa chọn</option>
                                <option value="fill_in_blank" {{ old('type') == 'fill_in_blank' ? 'selected' : '' }}>Điền trống</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Độ khó <span class="text-red-500">*</span></label>
                            <select name="difficulty" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('difficulty') border-red-500 @enderror">
                                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                            </select>
                            @error('difficulty')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nội dung câu hỏi <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="4" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror" placeholder="Nhập nội dung câu hỏi...">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Giải thích (tùy chọn)</label>
                        <textarea name="explanation" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('explanation') border-red-500 @enderror" placeholder="Giải thích đáp án đúng...">{{ old('explanation') }}</textarea>
                        @error('explanation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-medium text-slate-700">Hoạt động</label>
                    </div>
                </div>
            </div>

            <!-- Answers -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600">list</span>
                        Đáp án
                    </h3>
                    <button type="button" id="addAnswerBtn" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors flex items-center gap-1 text-sm font-medium">
                        <span class="material-symbols-outlined text-base">add</span>
                        Thêm đáp án
                    </button>
                </div>

                <div id="answersContainer" class="space-y-3">
                    <!-- Answer rows will be added here by JavaScript -->
                </div>

                <div id="answerHint" class="mt-3 text-sm text-slate-500 hidden">
                    <span class="material-symbols-outlined text-base align-middle">lightbulb</span>
                    <span id="hintText"></span>
                </div>

                @error('answers')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                @error('answers.*')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Hướng dẫn</h3>
                <div class="space-y-3 text-sm text-slate-600">
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-base">check_circle</span>
                        <span><strong>Một lựa chọn:</strong> Chỉ 1 đáp án đúng</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-purple-600 text-base">check_circle</span>
                        <span><strong>Nhiều lựa chọn:</strong> Có thể nhiều đáp án đúng</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-green-600 text-base">check_circle</span>
                        <span><strong>Điền trống:</strong> Tất cả đáp án đều đúng</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Thao tác</h3>
                <div class="space-y-3">
                    <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Lưu câu hỏi
                    </button>
                    <a href="{{ route('questions.index') }}" class="block w-full px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors font-medium text-center">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let answerIndex = 0;

function addAnswerRow(optionText = '', isCorrect = false) {
    const type = document.getElementById('typeSelect').value;
    const isFillInBlank = type === 'fill_in_blank';
    
    const container = document.getElementById('answersContainer');
    const div = document.createElement('div');
    div.className = 'answer-row flex items-center gap-3 p-3 bg-slate-50 rounded-xl';
    div.dataset.index = answerIndex;
    
    const correctClass = isCorrect ? 'bg-emerald-100 border-emerald-300' : '';
    
    div.innerHTML = `
        <div class="flex-1">
            <input type="text" name="answers[${answerIndex}][option_text]" 
                value="${escapeHtml(optionText)}" 
                placeholder="Nhập đáp án..." 
                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 @error('answers.*.option_text') border-red-500 @enderror"
                required>
        </div>
        <div class="w-24 text-center">
            <input type="${isFillInBlank ? 'hidden' : 'checkbox'}" 
                name="answers[${answerIndex}][is_correct]" 
                value="1" 
                ${isCorrect ? 'checked' : ''} 
                class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 correct-checkbox">
            ${isFillInBlank ? '<span class="text-xs text-emerald-600 font-medium">✓ Đúng</span>' : ''}
        </div>
        <button type="button" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors remove-answer" title="Xóa">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    `;
    
    container.appendChild(div);
    answerIndex++;
    
    updateHint();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateHint() {
    const type = document.getElementById('typeSelect').value;
    const hint = document.getElementById('answerHint');
    const hintText = document.getElementById('hintText');
    
    hint.classList.remove('hidden');
    
    if (type === 'single_choice') {
        hintText.textContent = 'Chọn 1 đáp án đúng cho câu hỏi một lựa chọn.';
    } else if (type === 'multiple_choice') {
        hintText.textContent = 'Chọn 1 hoặc nhiều đáp án đúng.';
    } else {
        hintText.textContent = 'Tất cả đáp án điền trống đều được coi là đúng.';
    }
}

document.getElementById('addAnswerBtn').addEventListener('click', () => addAnswerRow());

document.getElementById('answersContainer').addEventListener('click', (e) => {
    const removeBtn = e.target.closest('.remove-answer');
    if (removeBtn) {
        const row = removeBtn.closest('.answer-row');
        const container = document.getElementById('answersContainer');
        if (container.children.length > 2) {
            row.remove();
        } else {
            alert('Phải có ít nhất 2 đáp án!');
        }
    }
});

document.getElementById('typeSelect').addEventListener('change', () => {
    const type = document.getElementById('typeSelect').value;
    const checkboxes = document.querySelectorAll('.correct-checkbox');
    const labels = document.querySelectorAll('.answer-row span.text-emerald-600');
    
    checkboxes.forEach(cb => {
        const td = cb.parentElement;
        if (type === 'fill_in_blank') {
            td.innerHTML = '<span class="text-xs text-emerald-600 font-medium">✓ Đúng</span>';
        } else {
            td.innerHTML = '';
            const newCb = document.createElement('input');
            newCb.type = 'checkbox';
            newCb.name = cb.name;
            newCb.value = '1';
            newCb.className = 'w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 correct-checkbox';
            td.appendChild(newCb);
        }
    });
    
    updateHint();
});

document.getElementById('questionForm').addEventListener('submit', (e) => {
    const type = document.getElementById('typeSelect').value;
    const correctCheckboxes = document.querySelectorAll('.correct-checkbox:checked');
    const answers = document.querySelectorAll('.answer-row');
    
    if (type === 'single_choice' && correctCheckboxes.length !== 1) {
        e.preventDefault();
        alert('Câu hỏi một lựa chọn phải có đúng 1 đáp án đúng!');
        return;
    }
    
    if (type === 'multiple_choice' && correctCheckboxes.length < 1) {
        e.preventDefault();
        alert('Câu hỏi nhiều lựa chọn phải có ít nhất 1 đáp án đúng!');
        return;
    }
    
    if (answers.length < 2) {
        e.preventDefault();
        alert('Phải có ít nhất 2 đáp án!');
        return;
    }
});

// Initialize with 2 empty answer rows
addAnswerRow();
addAnswerRow();
</script>
@endsection
