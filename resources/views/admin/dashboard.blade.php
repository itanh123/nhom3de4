@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.users.index') }}" class="card shadow-sm h-100 text-decoration-none">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Người dùng</small>
                    <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-people text-primary fs-4"></i></div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small class="text-muted">Quản lý tài khoản người dùng</small></div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.topics.index') }}" class="card shadow-sm h-100 text-decoration-none">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Chủ đề</small>
                    <h3 class="fw-bold mb-0">{{ $stats['total_topics'] }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-diagram-3 text-success fs-4"></i></div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small class="text-muted">Quản lý chủ đề bài thi</small></div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('questions.index') }}" class="card shadow-sm h-100 text-decoration-none">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Câu hỏi</small>
                    <h3 class="fw-bold mb-0">{{ $stats['total_questions'] }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-question-circle text-info fs-4"></i></div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small class="text-muted">Ngân hàng câu hỏi</small></div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('exams.index') }}" class="card shadow-sm h-100 text-decoration-none">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Bài thi</small>
                    <h3 class="fw-bold mb-0">{{ $stats['total_exams'] }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-file-earmark-text text-warning fs-4"></i></div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small class="text-muted">Quản lý bài thi trắc nghiệm</small></div>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>Người dùng theo vai trò</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><div class="d-flex align-items-center gap-2"><span class="bg-info rounded-circle d-inline-block" style="width:10px;height:10px;"></span> <span class="small">Admin</span></div><strong>{{ $userByRole['admin'] ?? 0 }}</strong></div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><div class="d-flex align-items-center gap-2"><span class="bg-primary rounded-circle d-inline-block" style="width:10px;height:10px;"></span> <span class="small">Giáo viên</span></div><strong>{{ $userByRole['teacher'] ?? 0 }}</strong></div>
                <div class="d-flex justify-content-between align-items-center py-2"><div class="d-flex align-items-center gap-2"><span class="bg-success rounded-circle d-inline-block" style="width:10px;height:10px;"></span> <span class="small">Học sinh</span></div><strong>{{ $userByRole['student'] ?? 0 }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between small"><span class="text-muted">Tổng cộng</span><strong>{{ $stats['total_users'] }}</strong></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-person-plus text-success me-2"></i>Người dùng mới</h6></div>
            <div class="card-body">
                @if($recentUsers->isEmpty())
                    <p class="text-muted text-center py-4">Chưa có người dùng nào</p>
                @else
                    @foreach($recentUsers as $u)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;"><small class="fw-bold">{{ substr($u->name, 0, 1) }}</small></div>
                            <div><div class="small fw-medium">{{ $u->name }}</div><small class="text-muted">{{ $u->email }}</small></div>
                        </div>
                        <span class="badge {{ $u->role === 'admin' ? 'bg-info' : ($u->role === 'teacher' ? 'bg-primary' : 'bg-success') }}">{{ ucfirst($u->role) }}</span>
                    </div>
                    @endforeach
                    <a href="{{ route('admin.users.index') }}" class="btn btn-link btn-sm d-block text-center mt-3">Xem tất cả →</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-bookmark text-info me-2"></i>Chủ đề mới</h6></div>
            <div class="card-body">
                @if($recentTopics->isEmpty())
                    <p class="text-muted text-center py-4">Chưa có chủ đề nào</p>
                @else
                    @foreach($recentTopics as $topic)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div><div class="small fw-medium">{{ $topic->name }}</div><small class="text-muted">@ {{ $topic->creator?->name ?? 'Unknown' }}</small></div>
                        <small class="text-muted">{{ $topic->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                    <a href="{{ route('admin.topics.index') }}" class="btn btn-link btn-sm d-block text-center mt-3">Xem tất cả →</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4 bg-primary text-white">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2"></i>Thao tác nhanh</h5>
        <div class="row g-3">
            <div class="col-6 col-md-3"><a href="{{ route('admin.users.index') }}" class="btn btn-outline-light w-100"><i class="bi bi-person-plus d-block fs-4 mb-1"></i><small>Thêm người dùng</small></a></div>
            <div class="col-6 col-md-3"><a href="{{ route('admin.topics.index') }}" class="btn btn-outline-light w-100"><i class="bi bi-folder-plus d-block fs-4 mb-1"></i><small>Tạo chủ đề</small></a></div>
            <div class="col-6 col-md-3"><a href="{{ route('admin.roles.index') }}" class="btn btn-outline-light w-100"><i class="bi bi-shield-check d-block fs-4 mb-1"></i><small>Phân quyền</small></a></div>
            <div class="col-6 col-md-3"><a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light w-100"><i class="bi bi-bar-chart d-block fs-4 mb-1"></i><small>Xem báo cáo</small></a></div>
        </div>
    </div>
</div>
@endsection
