@extends('admin.layout')

@section('title', 'Chi tiết Câu hỏi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-question-circle me-2"></i>Chi tiết Câu hỏi</h2>
    <div class="btn-group">
        <a href="{{ route('questions.edit', $question) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Sửa</a>
        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="fw-bold">Câu hỏi</h5>
                    <div class="d-flex gap-2">
                        @if($question->type == 'single_choice') <span class="badge bg-primary">Một lựa chọn</span>
                        @elseif($question->type == 'multiple_choice') <span class="badge bg-info">Nhiều lựa chọn</span>
                        @else <span class="badge bg-success">Điền trống</span> @endif
                        @if($question->difficulty == 'easy') <span class="badge bg-success">Dễ</span>
                        @elseif($question->difficulty == 'medium') <span class="badge bg-warning text-dark">TB</span>
                        @else <span class="badge bg-danger">Khó</span> @endif
                        <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-danger' }}">{{ $question->is_active ? 'Hoạt động' : 'Tắt' }}</span>
                    </div>
                </div>
                <div class="p-3 bg-light rounded mb-3"><p class="mb-0 fs-5">{!! nl2br(e($question->content)) !!}</p></div>
                @if($question->explanation)
                <div class="p-3 bg-info bg-opacity-10 border border-info rounded">
                    <h6 class="fw-bold text-info mb-1"><i class="bi bi-lightbulb me-1"></i>Giải thích</h6>
                    <p class="mb-0">{{ $question->explanation }}</p>
                </div>
                @endif
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-list-ol text-success me-2"></i>Đáp án ({{ $question->answers->count() }})</h6></div>
            <div class="card-body">
                @foreach($question->answers->sortBy('display_order') as $index => $answer)
                <div class="p-3 rounded mb-2 d-flex align-items-center gap-3 {{ $answer->is_correct ? 'bg-success bg-opacity-10 border border-success' : 'bg-light border' }}">
                    <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-secondary' }} rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">{{ chr(65 + $index) }}</span>
                    <div class="flex-grow-1">{{ $answer->option_text }}</div>
                    @if($answer->is_correct)<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đúng</span>@endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thông tin</h6></div>
            <div class="card-body small">
                <p><span class="text-muted">Chủ đề</span><br><strong>{{ $question->topic?->name ?? 'N/A' }}</strong></p>
                <p><span class="text-muted">Người tạo</span><br><strong>{{ $question->creator?->name ?? 'N/A' }}</strong></p>
                <p><span class="text-muted">Ngày tạo</span><br><strong>{{ $question->created_at->format('d/m/Y H:i') }}</strong></p>
                <p class="mb-0"><span class="text-muted">Cập nhật</span><br><strong>{{ $question->updated_at->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thao tác</h6></div>
            <div class="card-body d-grid gap-2">
                <form action="{{ route('questions.toggleActive', $question) }}" method="POST">@csrf @method('PATCH')
                    <button type="submit" class="btn w-100 {{ $question->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"><i class="bi bi-{{ $question->is_active ? 'eye-slash' : 'eye' }} me-1"></i>{{ $question->is_active ? 'Ẩn' : 'Hiện' }}</button>
                </form>
                <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Xóa câu hỏi này?')">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
