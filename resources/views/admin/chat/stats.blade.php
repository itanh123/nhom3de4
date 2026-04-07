@extends('layouts.app')

@section('title', 'Thống kê Chat AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-bar-chart me-2"></i>Thống kê Chat AI</h2>
    <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-chat-dots fs-1 text-primary mb-3"></i>
                <h3 class="mb-1">{{ number_format($totalSessions) }}</h3>
                <p class="text-muted mb-0">Tổng cuộc trò chuyện</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-chat-left-text fs-1 text-success mb-3"></i>
                <h3 class="mb-1">{{ number_format($totalMessages) }}</h3>
                <p class="text-muted mb-0">Tổng tin nhắn</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-info mb-3"></i>
                <h3 class="mb-1">{{ number_format($userCount) }}</h3>
                <p class="text-muted mb-0">Người dùng đã chat</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-star-fill fs-1 text-warning mb-3"></i>
                <h3 class="mb-1">{{ number_format($starredCount) }}</h3>
                <p class="text-muted mb-0">Đã đánh dấu sao</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Cuộc trò chuyện gần đây</h6>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Tiêu đề</th>
                    <th>Tin nhắn gần nhất</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSessions as $session)
                <tr>
                    <td>{{ $session->user->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($session->title ?? 'Cuộc trò chuyện', 30) }}</td>
                    <td>{{ $session->last_message_at?->diffForHumans() ?? 'Chưa có' }}</td>
                    <td>{{ $session->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.chat.show', $session) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Chưa có cuộc trò chuyện nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
