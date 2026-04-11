@extends('admin.layout')

@section('title', 'Trang chủ')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold mb-3">Chinh Phục <span class="text-primary">Kiến Thức</span></h1>
    <p class="lead text-muted mb-4">Nâng cao trí tuệ mỗi ngày thông qua các bộ đề trắc nghiệm chuyên sâu.</p>
    @guest
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">Đăng ký ngay</a>
    </div>
    @else
    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-speedometer2 me-2"></i>Đi đến Dashboard</a>
    @endguest
</div>
<div class="row g-4 mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body p-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;"><i class="bi bi-journal-bookmark text-primary fs-4"></i></div>
                <h5 class="fw-bold">Quản lý Chủ đề</h5>
                <p class="text-muted small mb-0">Tạo và quản lý các chủ đề bài thi theo từng lĩnh vực kiến thức</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body p-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;"><i class="bi bi-database text-success fs-4"></i></div>
                <h5 class="fw-bold">Ngân hàng Câu hỏi</h5>
                <p class="text-muted small mb-0">Tạo và quản lý câu hỏi thi với hỗ trợ AI thông minh</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body p-4">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;"><i class="bi bi-file-earmark-check text-warning fs-4"></i></div>
                <h5 class="fw-bold">Tạo Bài Thi</h5>
                <p class="text-muted small mb-0">Sinh bài thi từ ngân hàng câu hỏi với nhiều tùy chọn linh hoạt</p>
            </div>
        </div>
    </div>
</div>
@endsection
