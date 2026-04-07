@extends('admin.layout')

@section('title', 'Danh sách Bài thi')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 class="mb-0"><i class="bi bi-journal-text me-2"></i>Danh sách Bài thi</h2>
    <a href="{{ route('student.exams.ai-generator') }}" class="btn btn-info fw-bold text-white shadow-sm">
        <i class="bi bi-stars me-1"></i>Luyện tập với AI
    </a>
</div>
@if(session('success'))<div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger d-flex align-items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>@endif
@if($exams->isEmpty())
<div class="card shadow-sm"><div class="card-body text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-3"></i><p class="fs-5">Hiện không có bài thi nào.</p></div></div>
@else
<div class="row g-4">
    @foreach($exams as $exam)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="fw-bold">{{ $exam->title }}</h5>
                    @if(in_array($exam->id, $completedExams))<span class="badge bg-success">Đã làm</span>@endif
                </div>
                @if($exam->topic)<p class="text-muted small mb-2"><i class="bi bi-tag me-1"></i>{{ $exam->topic->name }}</p>@endif
                @if($exam->description)<p class="text-muted small mb-3">{{ Str::limit($exam->description, 100) }}</p>@endif
                <div class="row g-2 mb-3">
                    <div class="col-6"><div class="bg-light rounded p-2 text-center"><small class="text-muted d-block">Câu hỏi</small><strong>{{ $exam->exam_questions_count ?? $exam->examQuestions->count() }}</strong></div></div>
                    <div class="col-6"><div class="bg-light rounded p-2 text-center"><small class="text-muted d-block">Thời gian</small><strong>{{ $exam->duration_mins ?? '∞' }} phút</strong></div></div>
                </div>
                @if(in_array($exam->id, $completedExams))
                    <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-outline-secondary w-100">Xem chi tiết</a>
                @else
                    <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary w-100">Làm bài thi</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
