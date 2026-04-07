@extends('admin.layout')

@section('title', 'Chi tiết nhập câu hỏi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Chi tiết nhập câu hỏi</h2>
    <a href="{{ route('imports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">File:</th>
                        <td><strong>{{ $import->file_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Chủ đề:</th>
                        <td><span class="badge bg-info">{{ $import->topic?->name ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Người nhập:</th>
                        <td>{{ $import->user?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày nhập:</th>
                        <td>{{ $import->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>

                <hr>

                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="mb-0 text-primary">{{ $import->total_rows }}</h3>
                            <small class="text-muted">Tổng dòng</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="mb-0 text-success">{{ $import->success_rows }}</h3>
                            <small class="text-muted">Thành công</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="mb-0 text-danger">{{ $import->failed_rows }}</h3>
                            <small class="text-muted">Lỗi</small>
                        </div>
                    </div>
                </div>

                @if($import->status === 'failed' && $import->error_message)
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Lỗi:</strong>
                    <pre class="mb-0 mt-2 small">{{ $import->error_message }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Trạng thái</h5>
            </div>
            <div class="card-body text-center">
                @if($import->status === 'completed')
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <h5 class="mt-2 text-success">Hoàn thành</h5>
                @elseif($import->status === 'completed_with_errors')
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                    <h5 class="mt-2 text-warning">Có lỗi</h5>
                @elseif($import->status === 'processing')
                    <i class="bi bi-hourglass-split text-info fs-1"></i>
                    <h5 class="mt-2 text-info">Đang xử lý</h5>
                @else
                    <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                    <h5 class="mt-2 text-danger">Thất bại</h5>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
