@extends('admin.layout')

@section('title', 'Quản lý tài liệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-folder2-open me-2"></i>Quản lý tài liệu</h2>
    <a href="{{ route('documents.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tải lên tài liệu
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên file..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="topic_id" class="form-select">
                    <option value="">-- Chọn chủ đề --</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                            {{ $topic->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Lọc</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tên file</th>
                        <th>Chủ đề</th>
                        <th>Kích thước</th>
                        <th>Loại</th>
                        <th>Người tải lên</th>
                        <th>Ngày tải</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td>
                            <i class="bi bi-file-earmark me-2 text-primary"></i>
                            <strong>{{ $doc->file_name }}</strong>
                        </td>
                        <td><span class="badge bg-info">{{ $doc->topic->name ?? 'N/A' }}</span></td>
                        <td>{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                        <td><small class="text-muted">{{ $doc->mime_type }}</small></td>
                        <td>{{ $doc->uploader->name ?? 'N/A' }}</td>
                        <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary" title="Xem">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-success" title="Tải xuống">
                                <i class="bi bi-download"></i>
                            </a>
                            <a href="{{ route('documents.edit', $doc) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài liệu này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có tài liệu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $documents->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
