@extends('admin.layout')

@section('title', 'Tạo cuộc trò chuyện mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Tạo cuộc trò chuyện mới</h2>
    <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.chat.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Chọn người dùng <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">-- Chọn người dùng --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role ?? 'user') }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Tiêu đề cuộc trò chuyện</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" placeholder="VD: Hỗ trợ tạo câu hỏi">
                <small class="text-muted">Để trống để tự động tạo tiêu đề</small>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Tin nhắn đầu tiên</label>
                <textarea name="first_message" class="form-control @error('first_message') is-invalid @enderror" 
                          rows="4" placeholder="Nhập tin nhắn đầu tiên bạn muốn gửi cho người dùng...">{{ old('first_message') }}</textarea>
                <small class="text-muted">Tin nhắn này sẽ được gửi ngay khi tạo cuộc trò chuyện</small>
                @error('first_message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary me-md-2">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-chat-dots me-2"></i>Tạo cuộc trò chuyện
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Hướng dẫn</h6>
    </div>
    <div class="card-body">
        <ul class="mb-0">
            <li>Chọn người dùng bạn muốn bắt đầu cuộc trò chuyện</li>
            <li>Tin nhắn đầu tiên sẽ được gửi ngay từ phía Admin</li>
            <li>Người dùng có thể trả lời và tiếp tục cuộc trò chuyện</li>
            <li>Bạn có thể kích hoạt phản hồi từ AI khi trả lời</li>
            <li>Cuộc trò chuyện sẽ được lưu trữ để xem lại sau</li>
        </ul>
    </div>
</div>
@endsection
