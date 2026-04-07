@extends('admin.layout')

@section('title', 'Sửa Bài thi')

@section('content')
<div class="mb-4"><h2><i class="bi bi-pencil-square me-2"></i>Sửa Bài thi</h2></div>

<form action="{{ route('exams.update', $exam) }}" method="POST" id="examForm">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Thông tin bài thi</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" id="topicSelect" required class="form-select">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $exam->topic_id) == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $exam->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Thời gian (phút)</label><input type="number" name="duration_mins" value="{{ old('duration_mins', $exam->duration_mins) }}" min="1" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Điểm đạt (%)</label><input type="number" name="pass_score" value="{{ old('pass_score', $exam->pass_score) }}" required min="0" max="100" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Bắt đầu</label><input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Kết thúc</label><input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" class="form-control"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" required class="form-select">
                            <option value="draft" {{ old('status', $exam->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                            <option value="open" {{ old('status', $exam->status) == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="closed" {{ old('status', $exam->status) == 'closed' ? 'selected' : '' }}>Đóng</option>
                            <option value="archived" {{ old('status', $exam->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-check mb-2"><input type="checkbox" name="shuffle_q" id="shuffle_q" value="1" {{ old('shuffle_q', $exam->shuffle_q) ? 'checked' : '' }} class="form-check-input"><label for="shuffle_q" class="form-check-label">Xáo trộn câu hỏi</label></div>
                    <div class="form-check mb-2"><input type="checkbox" name="shuffle_a" id="shuffle_a" value="1" {{ old('shuffle_a', $exam->shuffle_a) ? 'checked' : '' }} class="form-check-input"><label for="shuffle_a" class="form-check-label">Xáo trộn đáp án</label></div>
                    <div class="form-check mb-2"><input type="checkbox" name="show_explain" id="show_explain" value="1" {{ old('show_explain', $exam->show_explain) ? 'checked' : '' }} class="form-check-input"><label for="show_explain" class="form-check-label">Hiện giải thích</label></div>
                    <div class="form-check"><input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $exam->is_published) ? 'checked' : '' }} class="form-check-input"><label for="is_published" class="form-check-label">Công khai</label></div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Chọn câu hỏi</h6></div>
                <div class="card-body">
                    <div id="topicPrompt" class="d-none text-center py-5 text-muted"><i class="bi bi-hand-index fs-1 d-block mb-2"></i><p>Vui lòng chọn chủ đề</p></div>
                    <div id="questionsContainer">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="mb-0 text-muted">Đã chọn: <span id="selectedCount" class="fw-bold text-primary">{{ $exam->examQuestions->count() }}</span> câu hỏi</p>
                            <button type="button" id="selectAllBtn" class="btn btn-link btn-sm">Chọn tất cả</button>
                        </div>
                        <div id="questionsList" style="max-height: 400px; overflow-y: auto;">
                            @foreach($questions as $q)
                            <div class="p-3 bg-light rounded mb-2" style="cursor:pointer">
                                <label class="d-flex align-items-start gap-3" style="cursor:pointer">
                                    <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" class="form-check-input mt-1 question-checkbox" {{ $exam->examQuestions->where('question_id', $q->id)->first() ? 'checked' : '' }}>
                                    <div class="flex-grow-1">
                                        <div class="small">{{ Str::limit($q->content, 100) }}</div>
                                        <div class="mt-1">
                                            @if($q->type == 'single_choice') <span class="badge bg-primary">Một lựa chọn</span>
                                            @elseif($q->type == 'multiple_choice') <span class="badge bg-info">Nhiều lựa chọn</span>
                                            @else <span class="badge bg-success">Điền trống</span> @endif
                                            <small class="text-muted ms-1">{{ $q->answers->count() }} đáp án</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
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
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let selectedQuestions = new Set();
@foreach($exam->examQuestions as $eq) selectedQuestions.add({{ $eq->question_id }}); @endforeach
document.querySelectorAll('.question-checkbox').forEach(cb => {
    cb.addEventListener('change', function() { const v = parseInt(this.value); if (this.checked) { selectedQuestions.add(v); } else { selectedQuestions.delete(v); } document.getElementById('selectedCount').textContent = selectedQuestions.size; });
});
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
                const div = document.createElement('div');
                div.className = 'p-3 bg-light rounded mb-2'; div.style.cursor = 'pointer';
                div.innerHTML = `<label class="d-flex align-items-start gap-3" style="cursor:pointer"><input type="checkbox" name="question_ids[]" value="${q.id}" class="form-check-input mt-1 question-checkbox" ${selectedQuestions.has(q.id) ? 'checked' : ''}><div class="flex-grow-1"><div class="small">${escapeHtml(q.content)}</div><div class="mt-1"><span class="badge bg-primary">${typeLabels[q.type] || q.type}</span> <small class="text-muted">${q.answers ? q.answers.length : 0} đáp án</small></div></div></label>`;
                div.addEventListener('click', (e) => { if (e.target.type !== 'checkbox') { const cb = div.querySelector('input[type="checkbox"]'); cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); } });
                list.appendChild(div);
            });
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                cb.addEventListener('change', function() { const v = parseInt(this.value); if (this.checked) { selectedQuestions.add(v); } else { selectedQuestions.delete(v); } document.getElementById('selectedCount').textContent = selectedQuestions.size; });
            });
        });
});
function escapeHtml(text) { const d = document.createElement('div'); d.textContent = text || ''; return d.innerHTML; }
document.getElementById('examForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    if (checked === 0) { e.preventDefault(); alert('Vui lòng chọn ít nhất 1 câu hỏi!'); }
});
</script>
@endsection
