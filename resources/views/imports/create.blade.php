@extends('layouts.app')

@section('title', 'Nhập câu hỏi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-upload me-2"></i>Nhập câu hỏi từ file</h2>
    <a href="{{ route('imports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" class="form-select @error('topic_id') is-invalid @enderror" required>
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File câu hỏi <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".csv,.txt,.xlsx" required>
                        <small class="text-muted">Hỗ trợ: CSV, TXT, XLSX (tối đa 10MB)</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Nhập dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h5><i class="bi bi-info-circle me-2"></i>Hướng dẫn</h5>
                <p class="small">Tải file mẫu để biết định dạng đúng:</p>
                <a href="{{ route('imports.template') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-download me-2"></i>Tải file mẫu CSV
                </a>
                
                <hr>
                
                <h6>Cấu trúc file CSV:</h6>
                <ul class="small">
                    <li><code>content</code> - Nội dung câu hỏi</li>
                    <li><code>type</code> - single_choice, multiple_choice, fill_in_blank</li>
                    <li><code>difficulty</code> - easy, medium, hard</li>
                    <li><code>explanation</code> - Giải thích (tùy chọn)</li>
                    <li><code>answer_1</code> - Đáp án 1</li>
                    <li><code>answer_1_correct</code> - true/false</li>
                    <li>answer_2, answer_3, answer_4...</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
