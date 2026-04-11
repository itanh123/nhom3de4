@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>Lịch sử lệnh AI Agent
                            </h4>
                            <p class="text-muted small mb-0">Tất cả các lệnh đã được thực thi</p>
                        </div>
                        <a href="{{ route('admin.ai-agent') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Tạo lệnh mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($commands->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Chưa có lệnh nào được thực thi</p>
                            <a href="{{ route('admin.ai-agent') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-terminal me-1"></i>Mở AI Agent
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Lệnh</th>
                                        <th>Hành động</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                        <th width="80">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commands as $cmd)
                                        <tr>
                                            <td class="text-muted">{{ $cmd->id }}</td>
                                            <td>
                                                <span class="text-break">{{ $cmd->message }}</span>
                                                @if($cmd->result_message)
                                                    <br><small class="text-success">{{ Str::limit($cmd->result_message, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cmd->parsed_action)
                                                    <code class="small">{{ $cmd->parsed_action }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($cmd->status)
                                                    @case('pending')
                                                        <span class="badge bg-secondary">Chờ xử lý</span>
                                                        @break
                                                    @case('parsed')
                                                        <span class="badge bg-info">Đã phân tích</span>
                                                        @break
                                                    @case('executed')
                                                        <span class="badge bg-success">Đã thực thi</span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="badge bg-success">Đã xác nhận</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">Lỗi</span>
                                                        @break
                                                    @case('blocked')
                                                        <span class="badge bg-dark">Chặn</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $cmd->created_at->format('d/m/Y H:i') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $cmd->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.ai-agent.show', $cmd->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $commands->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
