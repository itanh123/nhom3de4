@extends('admin.layout')

@section('title', 'Quản lý Chat AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Quản lý Chat AI</h2>
    <a href="{{ route('admin.chat.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tạo cuộc trò chuyện mới
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tìm kiếm theo tiêu đề, người dùng..." 
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <select name="starred" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ request('starred') == '1' ? 'selected' : '' }}>⭐ Đã đánh dấu</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            <div class="col-md-4 text-end">
                <span class="text-muted">
                    Tổng: <strong>{{ $sessions->total() }}</strong> cuộc trò chuyện
                </span>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <i class="bi bi-star{{ request('sort') === 'starred' ? '-fill text-warning' : '' }}"></i>
                        </th>
                        <th>Người dùng</th>
                        <th>Tiêu đề</th>
                        <th>Tin nhắn gần nhất</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr class="{{ $session->is_starred ? 'table-warning' : '' }}">
                        <td>
                            <button type="button" class="btn btn-sm btn-link p-0 star-btn" 
                                    data-id="{{ $session->id }}" data-starred="{{ $session->is_starred ? '1' : '0' }}">
                                <i class="bi bi-star{{ $session->is_starred ? '-fill text-warning' : '' }}"></i>
                            </button>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white">
                                    {{ substr($session->user->name ?? 'N', 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $session->user->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $session->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.chat.show', $session) }}" class="text-decoration-none">
                                {{ Str::limit($session->title ?? 'Cuộc trò chuyện', 40) }}
                            </a>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $session->last_message_at ? $session->last_message_at->diffForHumans() : 'Chưa có tin nhắn' }}
                            </small>
                        </td>
                        <td>
                            <small>{{ $session->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.chat.show', $session) }}" 
                                   class="btn btn-outline-primary" title="Xem chi tiết">
                                    <i class="bi bi-chat-dots"></i>
                                </a>
                                <a href="{{ route('admin.chat.export', $session) }}" 
                                   class="btn btn-outline-secondary" title="Xuất file">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-delete" 
                                        data-id="{{ $session->id }}" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Chưa có cuộc trò chuyện nào
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $sessions->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa cuộc trò chuyện này? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star toggle
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const isStarred = this.dataset.starred === '1';
            
            fetch(`/admin/chat/${id}/star`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    if (data.is_starred) {
                        icon.className = 'bi bi-star-fill text-warning';
                        this.dataset.starred = '1';
                        this.closest('tr').classList.add('table-warning');
                    } else {
                        icon.className = 'bi bi-star';
                        this.dataset.starred = '0';
                        this.closest('tr').classList.remove('table-warning');
                    }
                }
            });
        });
    });

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('deleteForm').action = `/admin/chat/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
@endpush
