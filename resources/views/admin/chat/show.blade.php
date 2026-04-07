@extends('admin.layout')

@section('title', 'Chi tiết Chat - ' . ($session->title ?? 'Cuộc trò chuyện'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="d-inline">
            <i class="bi bi-chat-dots me-2"></i>
            {{ $session->title ?? 'Cuộc trò chuyện' }}
        </h4>
        @if($session->is_starred)
            <span class="badge bg-warning ms-2"><i class="bi bi-star-fill"></i></span>
        @endif
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleStar()">
            <i class="bi bi-star{{ $session->is_starred ? '-fill text-warning' : '' }}"></i>
        </button>
        <a href="{{ route('admin.chat.export', $session) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download"></i> Xuất
        </a>
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearMessages()">
            <i class="bi bi-trash"></i> Xóa tin nhắn
        </button>
        <form action="{{ route('admin.chat.destroy', $session) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('Xóa toàn bộ cuộc trò chuyện này?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-x-circle"></i> Xóa cuộc trò chuyện
            </button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Chat Info -->
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Người dùng</h6>
            </div>
            <div class="card-body text-center">
                <div class="avatar avatar-lg bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span class="text-white fs-3">{{ substr($session->user->name ?? 'N', 0, 1) }}</span>
                </div>
                <h5>{{ $session->user->name ?? 'N/A' }}</h5>
                <p class="text-muted mb-1">{{ $session->user->email ?? '' }}</p>
                <span class="badge bg-{{ $session->user->role === 'admin' ? 'danger' : ($session->user->role === 'teacher' ? 'warning' : 'info') }}">
                    {{ ucfirst($session->user->role ?? 'user') }}
                </span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <small class="text-muted">Bắt đầu:</small><br>
                    {{ $session->created_at->format('d/m/Y H:i') }}
                </li>
                <li class="list-group-item">
                    <small class="text-muted">Tin nhắn:</small><br>
                    {{ $session->messages->count() }}
                </li>
                <li class="list-group-item">
                    <small class="text-muted">Tin nhắn gần nhất:</small><br>
                    {{ $session->last_message_at?->diffForHumans() ?? 'Chưa có' }}
                </li>
            </ul>
        </div>

        <!-- Edit Title -->
        <div class="card shadow-sm mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pencil me-2"></i>Chỉnh sửa</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.chat.update', $session) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small">Tiêu đề</label>
                        <input type="text" name="title" class="form-control form-control-sm" 
                               value="{{ $session->title }}" placeholder="Nhập tiêu đề...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-save me-1"></i>Lưu
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Tin nhắn</h6>
                <span class="badge bg-secondary">{{ $session->messages->count() }} tin nhắn</span>
            </div>
            <div class="card-body chat-messages" style="height: 500px; overflow-y: auto;" id="chatMessages">
                @forelse($session->messages as $message)
                    <div class="message mb-3 {{ $message->sender_type === 'admin' ? 'text-end' : '' }}">
                        <div class="d-inline-block {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : ($message->sender_type === 'assistant' ? 'bg-success text-white' : 'bg-light') }} rounded-3 p-3" style="max-width: 80%;">
                            <div class="small mb-1">
                                @if($message->sender_type === 'admin')
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                @elseif($message->sender_type === 'assistant')
                                    <i class="bi bi-robot me-1"></i>AI
                                @else
                                    <i class="bi bi-person me-1"></i>{{ $session->user->name }}
                                @endif
                            </div>
                            <div class="message-content" style="white-space: pre-wrap;">{!! nl2br(e($message->content)) !!}</div>
                            <div class="small mt-1 opacity-75">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-dots fs-1 d-block mb-3"></i>
                        Chưa có tin nhắn nào
                    </div>
                @endforelse
            </div>
            <div class="card-footer bg-white pt-3 border-0">
                <form action="{{ route('admin.chat.send', $session) }}" method="POST" id="messageForm">
                    @csrf
                    <div class="position-relative">
                        <textarea name="message" class="form-control border-0 bg-light p-3 pe-5" rows="2" 
                                  placeholder="Nhập tin nhắn trả lời..." 
                                  style="border-radius: 1rem; resize: none;"
                                  required maxlength="5000" id="messageInput"></textarea>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center position-absolute" 
                                style="bottom: 10px; right: 10px; width: 40px; height: 40px; border-radius: 12px; z-index: 10;">
                            <i class="bi bi-send-fill fs-5"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeAi" name="include_ai">
                            <label class="form-check-label small text-muted" for="includeAi">
                                <i class="bi bi-robot me-1"></i> Kèm phản hồi từ AI
                            </label>
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">
                            Nhấn <strong>Enter</strong> để gửi, <strong>Shift + Enter</strong> để xuống dòng
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    const messageInput = document.getElementById('messageInput');
    const messageForm = document.getElementById('messageForm');

    // Handle Enter to send
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });

    // Form submit
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const msg = messageInput.value.trim();
        if (!msg) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.message || 'Lỗi máy chủ') }).catch(() => { throw new Error(res.statusText) });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Có lỗi xảy ra');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Lỗi: ' + (err.message || 'Không thể kết nối đến máy chủ'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        });
    });
});

function toggleStar() {
    fetch('{{ route("admin.chat.star", $session) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function clearMessages() {
    if (confirm('Xóa tất cả tin nhắn trong cuộc trò chuyện này?')) {
        fetch('{{ route("admin.chat.clear", $session) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
