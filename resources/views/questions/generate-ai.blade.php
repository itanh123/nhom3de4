@extends('admin.layout')

@section('title', 'Tạo câu hỏi bằng AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-stars me-2"></i>Tạo câu hỏi bằng AI</h2>
        <p class="text-muted mb-0">Sử dụng AI để tạo câu hỏi trắc nghiệm tự động</p>
    </div>
    <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('questions.generate-ai') }}" id="aiForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                            <select name="topic_id" class="form-select" required>
                                <option value="">-- Chọn chủ đề --</option>
                                @foreach($topics as $topic)<option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tài liệu tham khảo</label>
                            <select name="document_id" class="form-select">
                                <option value="">-- Không chọn --</option>
                                @foreach($documents as $doc)<option value="{{ $doc->id }}" {{ old('document_id') == $doc->id ? 'selected' : '' }}>{{ $doc->file_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3"><label class="form-label">Số lượng</label><input type="number" name="number" value="{{ old('number', 5) }}" min="1" max="50" class="form-control" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Độ khó</label><select name="difficulty" class="form-select" required><option value="easy">Dễ</option><option value="medium" selected>TB</option><option value="hard">Khó</option></select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Loại</label><select name="type" class="form-select" required><option value="single_choice" selected>Một lựa chọn</option><option value="multiple_choice">Nhiều lựa chọn</option><option value="fill_in_blank">Điền trống</option></select></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Yêu cầu bổ sung</label>
                        <textarea name="prompt" rows="3" class="form-control" placeholder="Ví dụ: Tập trung vào từ vựng...">{{ old('prompt') }}</textarea>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-stars me-1"></i>Tạo câu hỏi</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('aiForm').reset()">Đặt lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-info-circle text-primary"></i></div>
                    <h6 class="fw-bold mb-0">Hướng dẫn</h6>
                </div>
                <div class="small text-muted">
                    <p><strong>1.</strong> Chọn chủ đề chính</p>
                    <p><strong>2.</strong> Cung cấp tài liệu tham khảo (tùy chọn)</p>
                    <p><strong>3.</strong> Chọn số lượng 1-50</p>
                    <p class="mb-0"><strong>4.</strong> Xem trước và lưu câu hỏi</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
