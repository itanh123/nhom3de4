@extends('admin.layout')

@section('title', 'Quản lý Câu hỏi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-question-circle me-2"></i>Quản lý Câu hỏi</h2>
    <div class="d-flex gap-2">
        @php $hasAiConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_QUESTION_GENERATION)->exists(); @endphp
        @if($hasAiConfig)
        <a href="{{ route('questions.generate-ai.form') }}" class="btn btn-outline-primary"><i class="bi bi-stars me-1"></i>Tạo bằng AI</a>
        @endif
        <a href="{{ route('questions.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Thêm câu hỏi</a>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md"><label class="form-label small">Tìm kiếm</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Từ khóa..." class="form-control"></div>
            <div class="col-md"><label class="form-label small">Chủ đề</label><select name="topic_id" class="form-select"><option value="">Tất cả</option>@foreach($topics as $topic)<option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>@endforeach</select></div>
            <div class="col-md"><label class="form-label small">Độ khó</label><select name="difficulty" class="form-select"><option value="">Tất cả</option><option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option><option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option><option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option></select></div>
            <div class="col-md"><label class="form-label small">Loại</label><select name="type" class="form-select"><option value="">Tất cả</option><option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Một lựa chọn</option><option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Nhiều lựa chọn</option><option value="fill_in_blank" {{ request('type') == 'fill_in_blank' ? 'selected' : '' }}>Điền trống</option></select></div>
            <div class="col-md d-flex align-items-end gap-2"><button type="submit" class="btn btn-secondary">Lọc</button><a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Câu hỏi</th><th class="text-center">Loại</th><th class="text-center">Độ khó</th><th class="text-center">Chủ đề</th><th class="text-center">Trạng thái</th><th class="text-center">Hành động</th></tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                    <tr>
                        <td class="ps-3"><div class="small">{{ Str::limit($question->content, 80) }}</div><small class="text-muted">Bởi: {{ $question->creator?->name ?? 'N/A' }}</small></td>
                        <td class="text-center">
                            @if($question->type == 'single_choice') <span class="badge bg-primary">Một lựa chọn</span>
                            @elseif($question->type == 'multiple_choice') <span class="badge bg-info">Nhiều lựa chọn</span>
                            @else <span class="badge bg-success">Điền trống</span> @endif
                        </td>
                        <td class="text-center">
                            @if($question->difficulty == 'easy') <span class="badge bg-success">Dễ</span>
                            @elseif($question->difficulty == 'medium') <span class="badge bg-warning text-dark">TB</span>
                            @else <span class="badge bg-danger">Khó</span> @endif
                        </td>
                        <td class="text-center"><span class="text-muted small">{{ $question->topic?->name ?? 'N/A' }}</span></td>
                        <td class="text-center">
                            <form action="{{ route('questions.toggleActive', $question) }}" method="POST" class="d-inline">@csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $question->is_active ? 'btn-success' : 'btn-outline-danger' }}">{{ $question->is_active ? 'Hoạt động' : 'Tắt' }}</button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('questions.show', $question) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('questions.edit', $question) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa câu hỏi này?')">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Chưa có câu hỏi nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@if($questions->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $questions->appends(request()->query())->links() }}</div>
@endif
@endsection
