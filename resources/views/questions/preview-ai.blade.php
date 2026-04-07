@extends('admin.layout')

@section('title', 'Xem trước câu hỏi AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-stars me-2"></i>Xem trước câu hỏi AI</h2>
        <p class="text-muted mb-0">Chọn các câu hỏi bạn muốn lưu</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('questions.generate-ai.form') }}" class="btn btn-outline-secondary"><i class="bi bi-plus-circle me-1"></i>Tạo thêm</a>
        <button type="submit" form="mainForm" class="btn btn-success" id="saveBtn"><i class="bi bi-save me-1"></i>Lưu đã chọn</button>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4">
            <div><small class="text-muted">Tổng</small><p class="fs-4 fw-bold mb-0">{{ count($questions) }}</p></div>
            <div><small class="text-muted">Chủ đề</small><p class="fw-semibold mb-0">{{ $topic?->name ?? 'N/A' }}</p></div>
            <div><small class="text-muted">Độ khó</small><br>
                @if($difficulty === 'easy') <span class="badge bg-success">Dễ</span>
                @elseif($difficulty === 'medium') <span class="badge bg-warning text-dark">TB</span>
                @else <span class="badge bg-danger">Khó</span> @endif
            </div>
        </div>
        <div class="form-check"><input type="checkbox" id="selectAll" class="form-check-input" checked><label for="selectAll" class="form-check-label">Chọn tất cả</label></div>
    </div>
</div>
@if(session('ai_success'))<div class="alert alert-success d-flex align-items-center gap-2 mb-4"><i class="bi bi-check-circle-fill"></i>{{ session('ai_success') }}</div>@endif
<form action="{{ route('questions.generate-ai.save') }}" method="POST" id="mainForm">
    @csrf
    @foreach($questions as $index => $q)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex gap-3">
                <div class="flex-shrink-0 pt-1">
                    <input type="checkbox" name="questions[{{ $index }}][selected]" value="1" class="form-check-input question-checkbox" checked>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">{{ $index + 1 }}</span>
                        <div class="text-end text-muted small">
                            <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa trực tiếp
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Nội dung câu hỏi</label>
                        <textarea name="questions[{{ $index }}][content]" class="form-control" rows="2" required>{{ $q['content'] }}</textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        @foreach($q['answers'] as $ansIdx => $answer)
                        <div class="col-md-6">
                            <div class="p-2 rounded d-flex align-items-center gap-2 {{ $answer['is_correct'] ? 'bg-success bg-opacity-10 border border-success' : 'bg-light border' }}">
                                <span class="badge {{ $answer['is_correct'] ? 'bg-success' : 'bg-secondary' }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:0.7rem;">{{ chr(65 + $ansIdx) }}</span>
                                <input type="text" name="questions[{{ $index }}][answers][{{ $ansIdx }}][option_text]" value="{{ $answer['option_text'] }}" class="form-control form-control-sm border-0 bg-transparent" required>
                                <input type="hidden" name="questions[{{ $index }}][answers][{{ $ansIdx }}][is_correct]" value="{{ $answer['is_correct'] ? '1' : '0' }}">
                                @if($answer['is_correct'])<i class="bi bi-check-circle-fill text-success"></i>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-info bg-opacity-10 p-3 rounded border border-info border-start-4">
                        <label class="form-label fw-bold text-info small"><i class="bi bi-info-circle-fill me-1"></i>Giải thích</label>
                        <textarea name="questions[{{ $index }}][explanation]" class="form-control form-control-sm bg-transparent border-0 border-bottom rounded-0" rows="1" placeholder="Nhập giải thích...">{{ $q['explanation'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</form>
<script>
document.getElementById('selectAll')?.addEventListener('change', function() { document.querySelectorAll('.question-checkbox').forEach(cb => cb.checked = this.checked); });
document.getElementById('mainForm')?.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    if (checked === 0) { e.preventDefault(); alert('Chọn ít nhất 1 câu hỏi!'); }
});
</script>
@endsection
