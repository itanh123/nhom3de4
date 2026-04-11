@extends('layouts.app')

@section('title', 'Lịch sử nhập câu hỏi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history me-2"></i>Lịch sử nhập câu hỏi</h2>
    <a href="{{ route('imports.create') }}" class="btn btn-primary">
        <i class="bi bi-upload me-2"></i>Nhập mới
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>File</th>
                        <th>Chủ đề</th>
                        <th class="text-center">Tổng dòng</th>
                        <th class="text-center">Thành công</th>
                        <th class="text-center">Lỗi</th>
                        <th>Trạng thái</th>
                        <th>Người nhập</th>
                        <th>Ngày</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($imports as $import)
                    <tr>
                        <td><i class="bi bi-file-earmark me-2 text-secondary"></i>{{ $import->file_name }}</td>
                        <td><span class="badge bg-info">{{ $import->topic->name ?? 'N/A' }}</span></td>
                        <td class="text-center">{{ $import->total_rows }}</td>
                        <td class="text-center"><span class="text-success">{{ $import->success_rows }}</span></td>
                        <td class="text-center"><span class="text-danger">{{ $import->failed_rows }}</span></td>
                        <td>
                            @if($import->status === 'completed')
                                <span class="badge bg-success">Hoàn thành</span>
                            @elseif($import->status === 'completed_with_errors')
                                <span class="badge bg-warning">Có lỗi</span>
                            @elseif($import->status === 'processing')
                                <span class="badge bg-info">Đang xử lý</span>
                            @else
                                <span class="badge bg-danger">Thất bại</span>
                            @endif
                        </td>
                        <td>{{ $import->user->name ?? 'N/A' }}</td>
                        <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('imports.show', $import) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có lịch sử nhập câu hỏi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $imports->links() }}
        </div>
    </div>
</div>
@endsection
