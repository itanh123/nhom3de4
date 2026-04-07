@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="bi bi-search text-primary me-2"></i>Chi tiết lệnh #{{ $command->id }}
                            </h4>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.ai-agent') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </a>
                            <a href="{{ route('admin.ai-agent.history') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-list me-1"></i>Lịch sử
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold">Lệnh gốc</label>
                            <div class="p-3 bg-light rounded">
                                <i class="bi bi-terminal me-2 text-primary"></i>
                                <strong>{{ $command->message }}</strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Hành động</label>
                            <div>
                                @if($command->parsed_action)
                                    <span class="badge bg-primary fs-6">{{ $command->parsed_action }}</span>
                                @else
                                    <span class="text-muted">Chưa phân tích</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Trạng thái</label>
                            <div>
                                @switch($command->status)
                                    @case('pending')
                                        <span class="badge bg-secondary fs-6">Chờ xử lý</span>
                                        @break
                                    @case('parsed')
                                        <span class="badge bg-info fs-6">Đã phân tích</span>
                                        @break
                                    @case('executed')
                                        <span class="badge bg-success fs-6">Đã thực thi</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-success fs-6">Đã xác nhận</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger fs-6">Lỗi</span>
                                        @break
                                    @case('blocked')
                                        <span class="badge bg-dark fs-6">Chặn</span>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold">Payload (Tham số)</label>
                            <div class="p-3 bg-dark rounded" style="background: #1e293b !important;">
                                <pre class="mb-0 text-light"><code>{{ $command->parsed_payload ? json_encode($command->parsed_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}' }}</code></pre>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold">Kết quả</label>
                            <div class="p-3 {{ $command->status === 'executed' || $command->status === 'confirmed' ? 'bg-success-subtle' : 'bg-danger-subtle' }} rounded">
                                <i class="bi {{ $command->status === 'executed' || $command->status === 'confirmed' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                {{ $command->result_message ?? 'Chưa có kết quả' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Cần xác nhận</label>
                            <div>
                                @if($command->requires_confirmation)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Có
                                    </span>
                                    @if($command->confirmed_at)
                                        <br><small class="text-success">Đã xác nhận lúc: {{ $command->confirmed_at->format('d/m/Y H:i:s') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Không</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Thời gian</label>
                            <div class="small">
                                <div><strong>Tạo:</strong> {{ $command->created_at->format('d/m/Y H:i:s') }}</div>
                                @if($command->executed_at)
                                    <div><strong>Thực thi:</strong> {{ $command->executed_at->format('d/m/Y H:i:s') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($command->requires_confirmation && !$command->confirmed_at && $command->status === 'parsed')
                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Lệnh này cần xác nhận trước khi thực thi.</strong>
                            <hr>
                            <form action="{{ route('admin.ai-agent.execute') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="command_id" value="{{ $command->id }}">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-check-circle me-1"></i>Xác nhận và thực thi
                                </button>
                            </form>
                            <a href="{{ route('admin.ai-agent.history') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
