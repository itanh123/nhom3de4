@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Chào mừng, {{ $user->name }}!</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Vai trò:</strong>
                            @if($user->isAdmin())
                                <span class="badge bg-danger">Quản trị viên</span>
                            @elseif($user->isTeacher())
                                <span class="badge bg-primary">Giáo viên</span>
                            @else
                                <span class="badge bg-success">Học sinh</span>
                            @endif
                        </p>
                        <p><strong>Ngày tham gia:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Đường dẫn nhanh</h5>
                                <ul class="list-unstyled">
                                    @if($user->isAdmin())
                                        <li class="mb-2">
                                            <a href="{{ route('topics.index') }}" class="btn btn-outline-primary w-100">
                                                Quản lý Chủ đề
                                            </a>
                                        </li>
                                    @endif
                                    <li class="mb-2">
                                        <a href="#" class="btn btn-outline-secondary w-100">
                                            Danh sách bài thi
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="#" class="btn btn-outline-secondary w-100">
                                            Lịch sử làm bài
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
