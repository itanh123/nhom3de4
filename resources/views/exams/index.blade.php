@extends('admin.layout')

@section('title', 'Quản lý Bài thi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Quản lý Bài thi</h2>
    <a href="{{ route('exams.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tạo bài thi
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề bài thi..." class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Chủ đề</label>
                <select name="topic_id" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Mở</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đóng</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">Lọc</button>
                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Bài thi</th>
                        <th class="text-center">Chủ đề</th>
                        <th class="text-center">Câu hỏi</th>
                        <th class="text-center">Thời gian</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Công khai</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-medium">{{ Str::limit($exam->title, 50) }}</div>
                            <small class="text-muted">Bởi: {{ $exam->creator?->name ?? 'N/A' }}</small>
                        </td>
                        <td class="text-center text-muted">{{ $exam->topic?->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge bg-primary">{{ $exam->exam_questions_count }} câu</span></td>
                        <td class="text-center text-muted">{{ $exam->duration_mins ?? 'N/A' }} phút</td>
                        <td class="text-center">
                            @switch($exam->status)
                                @case('draft') <span class="badge bg-secondary">Nháp</span> @break
                                @case('scheduled') <span class="badge bg-warning text-dark">Đã lên lịch</span> @break
                                @case('open') <span class="badge bg-success">Mở</span> @break
                                @case('closed') <span class="badge bg-danger">Đóng</span> @break
                                @case('archived') <span class="badge bg-dark">Lưu trữ</span> @break
                            @endswitch
                        </td>
                        <td class="text-center">
                            <form action="{{ route('exams.togglePublish', $exam) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $exam->is_published ? 'btn-success' : 'btn-outline-danger' }}">
                                    {{ $exam->is_published ? 'Công khai' : 'Riêng tư' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline-primary" title="Xem"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>Chưa có bài thi nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($exams->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $exams->appends(request()->query())->links() }}</div>
@endif
@endsection
