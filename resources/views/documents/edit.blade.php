@extends('admin.layout')

@section('title', 'Chỉnh sửa tài liệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa tài liệu</h2>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('documents.update', $document) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Tên file</label>
                <input type="text" class="form-control" value="{{ $document->file_name }}" disabled>
                <small class="text-muted">Tên file không thể thay đổi</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                <select name="topic_id" class="form-select @error('topic_id') is-invalid @enderror" required>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ $document->topic_id == $topic->id ? 'selected' : '' }}>
                            {{ $topic->name }}
                        </option>
                    @endforeach
                </select>
                @error('topic_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
