@extends('admin.layout')

@section('title', 'Trang chủ')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 mb-4">Quiz Generator</h1>
    <p class="lead mb-4">Hệ thống sinh câu hỏi thi trắc nghiệm</p>
    @guest
        <div class="mt-4">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Đăng nhập</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Đăng ký</a>
        </div>
    @else
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Đi đến Dashboard</a>
        </div>
    @endguest
</div>
<div class="row mt-5">
    <div class="col-md-4"><div class="card h-100"><div class="card-body text-center"><h5 class="card-title">Quản lý Chủ đề</h5><p class="card-text text-muted">Tạo và quản lý các chủ đề bài thi</p></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body text-center"><h5 class="card-title">Ngân hàng Câu hỏi</h5><p class="card-text text-muted">Tạo và quản lý câu hỏi thi</p></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body text-center"><h5 class="card-title">Tạo Bài Thi</h5><p class="card-text text-muted">Sinh bài thi từ ngân hàng câu hỏi</p></div></div></div>
</div>
@endsection
