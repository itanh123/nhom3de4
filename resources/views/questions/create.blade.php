@extends('admin.layout')

@section('title', 'Thêm Câu hỏi')

@section('content')
<div class="mb-4"><h2><i class="bi bi-plus-circle me-2"></i>Thêm Câu hỏi mới</h2></div>

<form action="{{ route('questions.store') }}" method="POST" id="questionForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Thông tin câu hỏi</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" required class="form-select @error('topic_id') is-invalid @enderror">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)<option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>@endforeach
                        </select>
                        @error('topic_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                            <select name="type" id="typeSelect" required class="form-select">
                                <option value="single_choice" {{ old('type') == 'single_choice' ? 'selected' : '' }}>Một lựa chọn</option>
                                <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Nhiều lựa chọn</option>
                                <option value="fill_in_blank" {{ old('type') == 'fill_in_blank' ? 'selected' : '' }}>Điền trống</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Độ khó <span class="text-danger">*</span></label>
                            <select name="difficulty" required class="form-select">
                                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea name="content" rows="4" required class="form-control @error('content') is-invalid @enderror" placeholder="Nhập nội dung...">{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giải thích</label>
                        <textarea name="explanation" rows="3" class="form-control" placeholder="Giải thích đáp án đúng...">{{ old('explanation') }}</textarea>
                    </div>
                    <div class="form-check"><input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="form-check-input"><label for="is_active" class="form-check-label">Hoạt động</label></div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-list-ol text-success me-2"></i>Đáp án</h6>
                    <button type="button" id="addAnswerBtn" class="btn btn-sm btn-outline-success"><i class="bi bi-plus me-1"></i>Thêm đáp án</button>
                </div>
                <div class="card-body">
                    <div id="answersContainer"></div>
                    <div id="answerHint" class="mt-3 small text-muted d-none"><i class="bi bi-lightbulb me-1"></i><span id="hintText"></span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Hướng dẫn</h6></div>
                <div class="card-body small text-muted">
                    <p><i class="bi bi-check-circle text-primary me-1"></i> <strong>Một lựa chọn:</strong> Chỉ 1 đáp án đúng</p>
                    <p><i class="bi bi-check-circle text-info me-1"></i> <strong>Nhiều lựa chọn:</strong> Có thể nhiều đáp án đúng</p>
                    <p class="mb-0"><i class="bi bi-check-circle text-success me-1"></i> <strong>Điền trống:</strong> Tất cả đều đúng</p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thao tác</h6></div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu câu hỏi</button>
                    <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let answerIndex = 0;
function addAnswerRow(optionText = '', isCorrect = false) {
    const type = document.getElementById('typeSelect').value;
    const isFill = type === 'fill_in_blank';
    const container = document.getElementById('answersContainer');
    const div = document.createElement('div');
    div.className = 'answer-row d-flex align-items-center gap-3 p-3 bg-light rounded mb-2';
    div.innerHTML = `<div class="flex-grow-1"><input type="text" name="answers[${answerIndex}][option_text]" value="${escapeHtml(optionText)}" placeholder="Nhập đáp án..." class="form-control" required></div><div class="text-center" style="width:80px"><input type="${isFill ? 'hidden' : 'checkbox'}" name="answers[${answerIndex}][is_correct]" value="1" ${isCorrect ? 'checked' : ''} class="form-check-input correct-checkbox">${isFill ? '<span class="badge bg-success">✓</span>' : ''}</div><button type="button" class="btn btn-sm btn-outline-danger remove-answer"><i class="bi bi-x-lg"></i></button>`;
    container.appendChild(div);
    answerIndex++;
    updateHint();
}
function escapeHtml(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
function updateHint() {
    const type = document.getElementById('typeSelect').value;
    const hint = document.getElementById('answerHint');
    const text = document.getElementById('hintText');
    hint.classList.remove('d-none');
    if (type === 'single_choice') text.textContent = 'Chọn 1 đáp án đúng.';
    else if (type === 'multiple_choice') text.textContent = 'Chọn 1 hoặc nhiều đáp án đúng.';
    else text.textContent = 'Tất cả đáp án điền trống đều đúng.';
}
document.getElementById('addAnswerBtn').addEventListener('click', () => addAnswerRow());
document.getElementById('answersContainer').addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-answer');
    if (btn) { const row = btn.closest('.answer-row'); if (document.getElementById('answersContainer').children.length > 2) row.remove(); else alert('Phải có ít nhất 2 đáp án!'); }
});
document.getElementById('typeSelect').addEventListener('change', updateHint);
document.getElementById('questionForm').addEventListener('submit', (e) => {
    const type = document.getElementById('typeSelect').value;
    const checked = document.querySelectorAll('.correct-checkbox:checked').length;
    if (type === 'single_choice' && checked !== 1) { e.preventDefault(); alert('Phải có đúng 1 đáp án đúng!'); }
    if (type === 'multiple_choice' && checked < 1) { e.preventDefault(); alert('Phải có ít nhất 1 đáp án đúng!'); }
});
addAnswerRow(); addAnswerRow();
</script>
@endsection
