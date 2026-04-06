@extends('layouts.app')

@section('title', 'Quản lý Chủ đề')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Danh sách Chủ đề</h2>
    <a href="{{ route('topics.create') }}" class="btn btn-primary">Tạo Chủ đề mới</a>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($topics->isEmpty())
            <p class="text-muted text-center">Chưa có chủ đề nào.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên chủ đề</th>
                            <th>Mô tả</th>
                            <th>Người tạo</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topics as $topic)
                            <tr>
                                <td>{{ $topic->id }}</td>
                                <td>{{ $topic->name }}</td>
                                <td>{{ Str::limit($topic->description, 50) }}</td>
                                <td>{{ $topic->creator->name ?? 'N/A' }}</td>
                                <td>{{ $topic->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('topics.edit', $topic) }}" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('topics.destroy', $topic) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
