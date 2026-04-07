@extends('admin.layout')

@section('title', 'Chi tiết Bài thi')

@section('content')
<div class="mb-4"><a href="{{ route('student.exams.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Quay lại danh sách</a></div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="fw-bold">{{ $exam->title }}</h3>
                    @if($exam->topic)<span class="badge bg-primary">{{ $exam->topic->name }}</span>@endif
                </div>
                @if($exam->description)<div class="mb-4"><h6 class="fw-semibold text-muted">Mô tả</h6><p>{{ $exam->description }}</p></div>@endif
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3 text-center"><small class="text-muted d-block mb-1">Số câu hỏi</small><span class="fs-4 fw-bold">{{ $exam->examQuestions->count() }}</span></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3 text-center"><small class="text-muted d-block mb-1">Thời gian</small><span class="fs-4 fw-bold">{{ $exam->duration_mins ?? '∞' }}</span><br><small>phút</small></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3 text-center"><small class="text-muted d-block mb-1">Điểm đạt</small><span class="fs-4 fw-bold">{{ $exam->pass_score }}%</span></div></div>
                    <div class="col-6 col-md-3"><div class="bg-light rounded p-3 text-center"><small class="text-muted d-block mb-1">Xáo trộn</small><span class="fs-4 fw-bold">{{ $exam->shuffle_q ? 'Có' : 'Không' }}</span></div></div>
                </div>
                @if($alreadyTaken)
                <div class="alert alert-success text-center mb-0"><i class="bi bi-check-circle fs-1 d-block mb-2"></i><p class="fw-semibold mb-3">Bạn đã hoàn thành bài thi này!</p><a href="{{ route('student.results.index') }}" class="btn btn-success">Xem kết quả</a></div>
                @else
                <div class="alert alert-primary text-center mb-0"><i class="bi bi-lightning-charge fs-1 d-block mb-2"></i><p class="fw-semibold mb-3">Sẵn sàng làm bài?</p><form action="{{ route('student.exams.start', $exam) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-primary btn-lg">Bắt đầu làm bài</button></form></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
