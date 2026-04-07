@extends('admin.layout')

@section('title', 'Kết quả Bài thi')

@section('content')
<div class="mb-4"><a href="{{ route('student.results.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Quay lại lịch sử</a></div>
@if(session('success'))<div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="text-center mb-4">
            <h3 class="fw-bold">{{ $result->exam?->title ?? 'N/A' }}</h3>
            <div class="d-inline-block px-4 py-2 rounded {{ $result->passed ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                <span class="fw-bold fs-5">{{ $result->passed ? 'CHÚC MỪNG! BẠN ĐÃ ĐẠT' : 'RẤT TIẾC! CHƯA ĐẠT' }}</span>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3"><div class="bg-primary bg-opacity-10 rounded p-3 text-center"><small class="text-primary">Điểm</small><p class="fs-3 fw-bold text-primary mb-0">{{ $result->score_pct }}%</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-success bg-opacity-10 rounded p-3 text-center"><small class="text-success">Đúng</small><p class="fs-3 fw-bold text-success mb-0">{{ $result->correct_count }}</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-danger bg-opacity-10 rounded p-3 text-center"><small class="text-danger">Sai</small><p class="fs-3 fw-bold text-danger mb-0">{{ $result->total_questions - $result->correct_count }}</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-secondary bg-opacity-10 rounded p-3 text-center"><small class="text-secondary">Tổng</small><p class="fs-3 fw-bold text-secondary mb-0">{{ $result->total_questions }}</p></div></div>
        </div>
        <div class="row g-3 small text-muted">
            <div class="col-md-3"><strong>Bắt đầu:</strong> {{ $result->started_at->format('d/m/Y H:i:s') }}</div>
            <div class="col-md-3"><strong>Nộp bài:</strong> {{ $result->submitted_at->format('d/m/Y H:i:s') }}</div>
            <div class="col-md-3"><strong>Thời gian:</strong> {{ $result->started_at->diffForHumans($result->submitted_at, true) }}</div>
            <div class="col-md-3"><strong>Điểm đạt:</strong> {{ $result->exam?->pass_score ?? 'N/A' }}%</div>
        </div>
    </div>
</div>
@if($result->exam && $result->exam->created_by == auth()->id())
    @if(is_null($result->ai_rating))
        <div class="card shadow-sm border-warning mb-4">
            <div class="card-body text-center bg-warning bg-opacity-10 rounded">
                <h5 class="fw-bold text-warning-emphasis"><i class="bi bi-star-fill text-warning me-2"></i>Đánh giá đề luyện tập AI</h5>
                <form action="{{ route('student.results.rate-ai', $result) }}" method="POST" class="d-flex gap-2 justify-content-center flex-wrap">
                    @csrf
                    <button type="submit" name="rating" value="1" class="btn btn-outline-warning text-dark"><i class="bi bi-star-fill"></i> 1</button>
                    <button type="submit" name="rating" value="2" class="btn btn-outline-warning text-dark"><i class="bi bi-star-fill"></i> 2</button>
                    <button type="submit" name="rating" value="3" class="btn btn-outline-warning text-dark"><i class="bi bi-star-fill"></i> 3</button>
                    <button type="submit" name="rating" value="4" class="btn btn-outline-warning text-dark"><i class="bi bi-star-fill"></i> 4</button>
                    <button type="submit" name="rating" value="5" class="btn btn-warning text-dark fw-bold"><i class="bi bi-star-fill"></i> Tuyệt đỉnh (5 sao)</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center fw-bold shadow-sm">
            <i class="bi bi-stars"></i> Bạn đã chấm {{ $result->ai_rating }} sao cho đề AI này. 
            @if($result->ai_rating == 5) Hệ thống cảm ơn bạn đã đóng góp các câu hỏi hay vào ngân hàng chung! @endif
        </div>
    @endif
@endif
<h5 class="fw-bold mb-3">Chi tiết câu trả lời</h5>
@php $qNum = 1; @endphp
@foreach($result->examAnswers as $answer)
@php $question = $answer->question; @endphp
@if(!$question) @php $qNum++; @endphp @continue @endif
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex gap-3 mb-3">
            <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">{{ $qNum++ }}</span>
            <div class="flex-grow-1">
                <p class="fw-medium mb-3">{{ $question->content }}</p>
                @if($question->type === 'single_choice')
                    @foreach($question->answers as $ans)
                    <div class="p-2 rounded mb-1 {{ $ans->is_correct ? 'bg-success bg-opacity-10 border border-success' : (isset($answer->answer_id) && $answer->answer_id == $ans->id ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light') }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>{{ chr(64 + $loop->iteration) }}.</strong> {{ $ans->option_text }}</span>
                            @if($ans->is_correct) <span class="text-success fw-semibold small">Đáp án đúng</span>
                            @elseif(isset($answer->answer_id) && $answer->answer_id == $ans->id && !$answer->is_correct) <span class="text-danger fw-semibold small">Bạn chọn</span> @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="p-3 rounded {{ $answer->is_correct ? 'bg-success bg-opacity-10 border border-success' : 'bg-danger bg-opacity-10 border border-danger' }}">
                        <small class="text-muted">Câu trả lời:</small>
                        <p class="fw-medium mb-0">{{ $answer->text_answer ?: 'Không trả lời' }}</p>
                    </div>
                @endif
                @if($result->exam?->show_explain && $question->explanation)
                <div class="mt-3 p-3 bg-info bg-opacity-10 border border-info rounded">
                    <small class="fw-bold text-info"><i class="bi bi-info-circle me-1"></i>Giải thích:</small>
                    <p class="mb-0">{{ $question->explanation }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="text-center mt-4"><a href="{{ route('student.exams.index') }}" class="btn btn-primary btn-lg">Làm bài thi khác</a></div>
@endsection
