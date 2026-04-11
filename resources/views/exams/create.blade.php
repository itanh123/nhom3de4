@extends('admin.layout')

@section('title', 'Tạo Bài thi')

@section('content')
<div class="mb-4"><h2><i class="bi bi-plus-circle me-2"></i>Tạo Bài thi mới</h2></div>

<form action="{{ route('exams.store') }}" method="POST" id="examForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Thông tin bài thi</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" id="topicSelect" required class="form-select @error('topic_id') is-invalid @enderror">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tiêu đề bài thi...">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Mô tả bài thi...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian (phút)</label>
                            <input type="number" name="duration_mins" value="{{ old('duration_mins', 30) }}" min="1" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Điểm đạt (%) <span class="text-danger">*</span></label>
                            <input type="number" name="pass_score" value="{{ old('pass_score', 60) }}" required min="0" max="100" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" required class="form-select">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                            <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Đóng</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-check mb-2"><input type="checkbox" name="shuffle_q" id="shuffle_q" value="1" {{ old('shuffle_q') ? 'checked' : '' }} class="form-check-input"><label for="shuffle_q" class="form-check-label">Xáo trộn câu hỏi</label></div>
                    <div class="form-check mb-2"><input type="checkbox" name="shuffle_a" id="shuffle_a" value="1" {{ old('shuffle_a') ? 'checked' : '' }} class="form-check-input"><label for="shuffle_a" class="form-check-label">Xáo trộn đáp án</label></div>
                    <div class="form-check mb-2"><input type="checkbox" name="show_explain" id="show_explain" value="1" {{ old('show_explain') ? 'checked' : '' }} class="form-check-input"><label for="show_explain" class="form-check-label">Hiện giải thích sau khi nộp bài</label></div>
                    <div class="form-check"><input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="form-check-input"><label for="is_published" class="form-check-label">Công khai</label></div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Chọn câu hỏi</h6></div>
                <div class="card-body">
                    <div id="topicPrompt" class="text-center py-5 text-muted">
                        <i class="bi bi-hand-index fs-1 d-block mb-2"></i>
                        <p>Vui lòng chọn chủ đề trước để hiển thị câu hỏi</p>
                    </div>
                    <div id="questionsContainer" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="mb-0 text-muted">Đã chọn: <span id="selectedCount" class="fw-bold text-primary">0</span> câu hỏi</p>
                            <button type="button" id="selectAllBtn" class="btn btn-link btn-sm">Chọn tất cả</button>
                        </div>
                        <div id="questionsList" style="max-height: 400px; overflow-y: auto;"></div>
                        @error('question_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Hướng dẫn</h6></div>
                <div class="card-body small text-muted">
                    <p><i class="bi bi-info-circle text-primary me-1"></i> Chọn chủ đề để hiển thị câu hỏi</p>
                    <p><i class="bi bi-check-circle text-success me-1"></i> Đánh dấu các câu hỏi muốn thêm</p>
                    <p class="mb-0"><i class="bi bi-star text-warning me-1"></i> Tối thiểu 1 câu hỏi</p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thao tác</h6></div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu bài thi</button>
                    <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let selectedQuestions = new Set();
document.getElementById('topicSelect').addEventListener('change', function() {
    const topicId = this.value;
    const prompt = document.getElementById('topicPrompt');
    const container = document.getElementById('questionsContainer');
    const list = document.getElementById('questionsList');
    if (!topicId) { prompt.classList.remove('d-none'); container.classList.add('d-none'); return; }
    prompt.classList.add('d-none'); container.classList.remove('d-none');
    list.innerHTML = '<div class="text-center py-4"><span class="spinner-border"></span></div>';
    fetch(`/exams/questions?topic_id=${topicId}`)
        .then(res => res.json())
        .then(questions => {
            list.innerHTML = '';
            if (questions.length === 0) { list.innerHTML = '<div class="text-center py-4 text-muted">Không có câu hỏi nào</div>'; return; }
            questions.forEach(q => {
                const typeLabels = { 'single_choice': 'Một lựa chọn', 'multiple_choice': 'Nhiều lựa chọn', 'fill_in_blank': 'Điền trống' };
                const diffBadge = q.difficulty === 'easy' ? 'bg-success' : q.difficulty === 'medium' ? 'bg-warning text-dark' : 'bg-danger';
                const diffLabel = q.difficulty === 'easy' ? 'Dễ' : q.difficulty === 'medium' ? 'TB' : 'Khó';
                const div = document.createElement('div');
                div.className = 'p-3 bg-light rounded mb-2 question-item';
                div.style.cursor = 'pointer';
                div.innerHTML = `<label class="d-flex align-items-start gap-3" style="cursor:pointer"><input type="checkbox" name="question_ids[]" value="${q.id}" class="form-check-input mt-1 question-checkbox" ${selectedQuestions.has(q.id) ? 'checked' : ''}><div class="flex-grow-1"><div class="small">${escapeHtml(q.content)}</div><div class="mt-1"><span class="badge bg-primary">${typeLabels[q.type] || q.type}</span> <span class="badge ${diffBadge}">${diffLabel}</span> <small class="text-muted ms-1">${q.answers ? q.answers.length : 0} đáp án</small></div></div></label>`;
                div.addEventListener('click', (e) => { if (e.target.type !== 'checkbox') { const cb = div.querySelector('input[type="checkbox"]'); cb.checked = !cb.checked; updateSelection(cb); } });
                list.appendChild(div);
            });
            document.querySelectorAll('.question-checkbox').forEach(cb => { cb.addEventListener('change', updateSelection); });
            updateSelectedCount();
        });
});
function escapeHtml(text) { const d = document.createElement('div'); d.textContent = text || ''; return d.innerHTML; }
function updateSelection(checkbox) { const v = parseInt(checkbox.value); if (checkbox.checked) { selectedQuestions.add(v); } else { selectedQuestions.delete(v); } updateSelectedCount(); }
function updateSelectedCount() { document.getElementById('selectedCount').textContent = selectedQuestions.size; }
document.getElementById('selectAllBtn').addEventListener('click', function() {
    const cbs = document.querySelectorAll('.question-checkbox');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => { cb.checked = !allChecked; const v = parseInt(cb.value); if (!allChecked) { selectedQuestions.add(v); } else { selectedQuestions.delete(v); } });
    updateSelectedCount(); this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
});
document.getElementById('examForm').addEventListener('submit', function(e) {
    if (selectedQuestions.size === 0) { e.preventDefault(); alert('Vui lòng chọn ít nhất 1 câu hỏi!'); }
});
</script>
@endsection
