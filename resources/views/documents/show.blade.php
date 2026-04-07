@extends('admin.layout')

@section('title', 'Chi tiết tài liệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Chi tiết tài liệu</h2>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Tên file:</th>
                        <td><strong>{{ $document->file_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Chủ đề:</th>
                        <td><span class="badge bg-info">{{ $document->topic->name ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Kích thước:</th>
                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                    </tr>
                    <tr>
                        <th>Loại file:</th>
                        <td><code>{{ $document->mime_type }}</code></td>
                    </tr>
                    <tr>
                        <th>Người tải lên:</th>
                        <td>{{ $document->uploader->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tải lên:</th>
                        <td>{{ $document->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>

                <div class="mt-4">
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-success me-2">
                        <i class="bi bi-download me-2"></i>Tải xuống
                    </a>
                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                    </a>
                    <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài liệu này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Xóa
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-light p-5 rounded">
                    <i class="bi bi-file-earmark fs-1 text-secondary"></i>
                    <p class="mt-2 text-muted small">{{ pathinfo($document->file_name, PATHINFO_EXTENSION) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
