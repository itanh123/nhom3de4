@extends('admin.layout')

@section('title', 'Chi tiết Bài thi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Chi tiết Bài thi</h2>
    <div class="btn-group">
        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Sửa</a>
        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="fw-bold">{{ $exam->title }}</h4>
                    @switch($exam->status)
                        @case('draft') <span class="badge bg-secondary">Nháp</span> @break
                        @case('scheduled') <span class="badge bg-warning text-dark">Đã lên lịch</span> @break
                        @case('open') <span class="badge bg-success">Mở</span> @break
                        @case('closed') <span class="badge bg-danger">Đóng</span> @break
                        @case('archived') <span class="badge bg-dark">Lưu trữ</span> @break
                    @endswitch
                </div>
                @if($exam->description)<p class="text-muted mb-3">{{ $exam->description }}</p>@endif
                <div class="row g-3">
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3"><small class="text-muted d-block">Chủ đề</small><strong>{{ $exam->topic?->name ?? 'N/A' }}</strong></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3"><small class="text-muted d-block">Thời gian</small><strong>{{ $exam->duration_mins ?? 'N/A' }} phút</strong></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3"><small class="text-muted d-block">Điểm đạt</small><strong>{{ $exam->pass_score }}%</strong></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3"><small class="text-muted d-block">Câu hỏi</small><strong>{{ $exam->examQuestions->count() }}</strong></div></div>
                </div>
            </div>
        </div>
        @if($exam->start_time || $exam->end_time)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i>Lịch thi</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    @if($exam->start_time)<div class="col-md-6"><div class="alert alert-warning mb-0"><small>Bắt đầu</small><br><strong>{{ $exam->start_time->format('d/m/Y H:i') }}</strong></div></div>@endif
                    @if($exam->end_time)<div class="col-md-6"><div class="alert alert-danger mb-0"><small>Kết thúc</small><br><strong>{{ $exam->end_time->format('d/m/Y H:i') }}</strong></div></div>@endif
                </div>
            </div>
        </div>
        @endif
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-list-check text-success me-2"></i>Danh sách câu hỏi ({{ $exam->examQuestions->count() }})</h6></div>
            <div class="card-body">
                @foreach($exam->examQuestions->sortBy('display_order') as $index => $eq)
                <div class="p-3 bg-light rounded mb-3">
                    <div class="d-flex gap-3 mb-2">
                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">{{ $index + 1 }}</span>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ Str::limit($eq->question->content, 150) }}</div>
                            <div class="mt-1">
                                @if($eq->question->type == 'single_choice') <span class="badge bg-primary">Một lựa chọn</span>
                                @elseif($eq->question->type == 'multiple_choice') <span class="badge bg-info">Nhiều lựa chọn</span>
                                @else <span class="badge bg-success">Điền trống</span> @endif
                                <span class="badge bg-{{ $eq->question->difficulty === 'easy' ? 'success' : ($eq->question->difficulty === 'medium' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($eq->question->difficulty) }}</span>
                                <small class="text-muted ms-1">{{ $eq->point }} điểm</small>
                            </div>
                        </div>
                    </div>
                    @if($eq->question->answers && $eq->question->answers->count() > 0)
                    <div class="ms-5">
                        @foreach($eq->question->answers->sortBy('display_order') as $ansIndex => $answer)
                        <div class="d-flex align-items-center gap-2 small py-1">
                            <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-secondary' }} rounded-circle" style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;">{{ chr(65 + $ansIndex) }}</span>
                            <span class="{{ $answer->is_correct ? 'text-success fw-medium' : '' }}">{{ $answer->option_text }}</span>
                            @if($answer->is_correct)<i class="bi bi-check-circle-fill text-success ms-1"></i>@endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thông tin</h6></div>
            <div class="card-body small">
                <p><span class="text-muted">Người tạo</span><br><strong>{{ $exam->creator?->name ?? 'N/A' }}</strong></p>
                <p><span class="text-muted">Ngày tạo</span><br><strong>{{ $exam->created_at->format('d/m/Y H:i') }}</strong></p>
                <p><span class="text-muted">Cập nhật</span><br><strong>{{ $exam->updated_at->format('d/m/Y H:i') }}</strong></p>
                <p class="mb-0"><span class="text-muted">Hiển thị</span><br>
                    @if($exam->shuffle_q) <span class="text-success">Xáo trộn câu hỏi</span>
                    @else <span class="text-muted">Theo thứ tự</span> @endif
                </p>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Thao tác</h6></div>
            <div class="card-body d-grid gap-2">
                <form action="{{ route('exams.togglePublish', $exam) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn w-100 {{ $exam->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        <i class="bi bi-{{ $exam->is_published ? 'eye-slash' : 'eye' }} me-1"></i>{{ $exam->is_published ? 'Ẩn bài thi' : 'Công khai' }}
                    </button>
                </form>
                <form action="{{ route('exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Xóa bài thi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
