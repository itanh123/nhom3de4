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
                                <i class="bi bi-robot text-primary me-2"></i>AI Agent
                            </h4>
                            <p class="text-muted small mb-0">Nhập lệnh bằng ngôn ngữ tự nhiên để quản lý hệ thống</p>
                        </div>
                        <a href="{{ route('admin.ai-agent.history') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-clock-history me-1"></i>Lịch sử
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('pending_confirmation'))
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Cần xác nhận:</strong> {{ session('confirmation_message') }}
                            <hr>
                            <p class="mb-2"><strong>Hành động:</strong> {{ session('parsed_action') }}</p>
                            @if(session('parsed_payload'))
                                <p class="mb-2"><strong>Tham số:</strong></p>
                                <pre class="bg-light p-2 rounded small mb-0">{{ json_encode(session('parsed_payload'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                            <hr>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.ai-agent.execute') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="command_id" value="{{ session('command_id') }}">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-check-circle me-1"></i>Xác nhận thực thi
                                    </button>
                                </form>
                                <a href="{{ route('admin.ai-agent') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.ai-agent.chat') }}" method="POST" id="commandForm">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-terminal"></i>
                            </span>
                            <input type="text" 
                                   name="message" 
                                   id="commandInput"
                                   class="form-control border-start-0 border-end-0" 
                                   placeholder="VD: Tạo chủ đề PHP Basics dưới Công nghệ thông tin"
                                   autocomplete="off"
                                   required
                                   autofocus>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Gửi
                            </button>
                        </div>
                    </form>

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-lightning me-1"></i>Lệnh được hỗ trợ:
                        </h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-primary fw-semibold">Chủ đề</small>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>• Tạo chủ đề [tên]</li>
                                        <li>• Tạo chủ đề [tên] dưới [cha]</li>
                                        <li>• Xóa chủ đề [tên]</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-success fw-semibold">Câu hỏi</small>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>• Tạo 10 câu hỏi về Laravel</li>
                                        <li>• Tạo 5 câu hỏi dễ về PHP</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-warning fw-semibold">Bài thi</small>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>• Tạo bài thi [tên]</li>
                                        <li>• Công bố bài thi [tên]</li>
                                        <li>• Xóa bài thi [tên]</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-info fw-semibold">Người dùng</small>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>• Khóa tài khoản [email]</li>
                                        <li>• Mở khóa tài khoản [email]</li>
                                        <li>• Gán vai trò [email] thành teacher</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">
                        <i class="bi bi-list-check me-1"></i>Lệnh gần đây
                    </h5>
                    @if($recentCommands->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0">Chưa có lệnh nào được thực thi</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($recentCommands as $cmd)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-medium">{{ $cmd->message }}</p>
                                            <div class="small">
                                                @if($cmd->parsed_action)
                                                    <span class="badge bg-primary me-1">{{ $cmd->parsed_action }}</span>
                                                @endif
                                                @if($cmd->parsed_payload)
                                                    <span class="text-muted">
                                                        {{ json_encode($cmd->parsed_payload) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @switch($cmd->status)
                                                @case('pending')
                                                    <span class="badge bg-secondary">Chờ</span>
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
                                            <br>
                                            <small class="text-muted">{{ $cmd->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @if($cmd->result_message)
                                        <div class="mt-2 p-2 bg-light rounded small">
                                            <i class="bi bi-chat-left-text me-1"></i>
                                            {{ $cmd->result_message }}
                                        </div>
                                    @endif
                                    @if($cmd->requires_confirmation && !$cmd->confirmed_at && $cmd->status === 'parsed')
                                        <div class="mt-2">
                                            <form action="{{ route('admin.ai-agent.execute') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="command_id" value="{{ $cmd->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-check-circle me-1"></i>Xác nhận
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('commandInput');
    const form = document.getElementById('commandForm');
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });

    const commandExamples = [
        'Tạo chủ đề Python Basics dưới Công nghệ thông tin',
        'Tạo 10 câu hỏi dễ về Laravel',
        'Công bố bài thi Laravel Beginner Quiz',
        'Khóa tài khoản abc@gmail.com',
        'Xóa bài thi TOEIC'
    ];

    let exampleIndex = 0;
    setInterval(function() {
        if (document.activeElement !== input && input.value === '') {
            input.placeholder = commandExamples[exampleIndex];
            exampleIndex = (exampleIndex + 1) % commandExamples.length;
        }
    }, 5000);
});
</script>
@endpush
