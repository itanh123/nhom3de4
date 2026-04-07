@extends('admin.layout')

@section('title', 'Tự tạo Đề thi AI')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('student.exams.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Trở về</a>
            <h2 class="mb-0"><i class="bi bi-robot text-primary me-2"></i>Tạo Đề Luyện Tập AI</h2>
        </div>

        @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="text-muted mb-4">Chọn chủ đề và cấu hình để AI thiết kế cho bạn một đề quiz hoàn toàn mới. Hệ thống sẽ tự động cấu trúc bài làm và căn thời gian hợp lý (2 phút/câu).</p>
                <form action="{{ route('student.exams.ai-generator.submit') }}" method="POST" id="aiForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="topic_id" class="form-label fw-bold">Chủ đề kiến thức <span class="text-danger">*</span></label>
                        <select class="form-select @error('topic_id') is-invalid @enderror" id="topic_id" name="topic_id" required>
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="number" class="form-label fw-bold">Số lượng câu hỏi <span class="text-danger">*</span></label>
                            <select class="form-select" id="number" name="number" required>
                                <option value="5">5 câu (Mini test)</option>
                                <option value="10" selected>10 câu (Tiêu chuẩn)</option>
                                <option value="15">15 câu (Luyện tập sâu)</option>
                                <option value="20">20 câu (Full test)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label for="difficulty" class="form-label fw-bold">Độ khó <span class="text-danger">*</span></label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="easy">Dễ (Ôn tập kiến thức nền)</option>
                                <option value="medium" selected>Trung bình (Chuẩn đầu ra)</option>
                                <option value="hard">Khó (Vận dụng cao)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="prompt" class="form-label fw-bold">Yêu cầu bổ sung cho AI <span class="text-muted fw-normal">(Tuỳ chọn)</span></label>
                        <textarea class="form-control" id="prompt" name="prompt" rows="3" placeholder="Ví dụ: Chỉ tập trung vào Eloquent ORM và Query Builder, không hỏi phần Blade...">{{ old('prompt') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" id="submitBtn">
                            <i class="bi bi-magic me-2"></i> Sinh Đề & Làm Bài Ngay
                        </button>
                    </div>
                </form>

                <!-- Loading state -->
                <div id="loadingState" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="fw-bold text-primary">AI đang thiết kế đề thi...</h5>
                    <p class="text-muted mb-0">Quá trình này có thể mất từ 10 - 20 giây tuỳ thuộc vào độ dài nội dung. Vui lòng không đóng trang.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aiForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingState = document.getElementById('loadingState');

    form.addEventListener('submit', function(e) {
        if(form.checkValidity()) {
            form.style.display = 'none';
            loadingState.classList.remove('d-none');
        }
    });
});
</script>
@endpush
