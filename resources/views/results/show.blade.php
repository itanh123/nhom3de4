@extends('admin.layout')

@section('title', 'Chi tiết Kết quả')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clipboard-data me-2"></i>Chi tiết Kết quả</h2>
    <a href="{{ route('results.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div><h4 class="fw-bold">{{ $result->exam?->title ?? 'N/A' }}</h4><p class="text-muted mb-0">{{ $result->exam?->topic?->name ?? '' }}</p></div>
            <div class="d-flex align-items-center gap-2">
                @php $hasAiConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_RESULT_EVALUATION)->exists(); @endphp
                @if($hasAiConfig)
                <form action="{{ route('results.ai-evaluate', $result) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-stars me-1"></i>AI Đánh giá</button></form>
                @endif
                <span class="badge fs-6 {{ $result->passed ? 'bg-success' : 'bg-danger' }}">{{ $result->passed ? 'ĐẠT' : 'KHÔNG ĐẠT' }}</span>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3"><div class="bg-primary bg-opacity-10 rounded p-3 text-center"><small class="text-primary">Điểm</small><p class="fs-3 fw-bold text-primary mb-0">{{ $result->score_pct }}%</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-success bg-opacity-10 rounded p-3 text-center"><small class="text-success">Đúng</small><p class="fs-3 fw-bold text-success mb-0">{{ $result->correct_count }}</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-danger bg-opacity-10 rounded p-3 text-center"><small class="text-danger">Sai</small><p class="fs-3 fw-bold text-danger mb-0">{{ $result->total_questions - $result->correct_count }}</p></div></div>
            <div class="col-6 col-md-3"><div class="bg-secondary bg-opacity-10 rounded p-3 text-center"><small class="text-secondary">Tổng</small><p class="fs-3 fw-bold text-secondary mb-0">{{ $result->total_questions }}</p></div></div>
        </div>
        <div class="row g-3 small">
            <div class="col-md-3"><span class="text-muted">Học sinh</span><br><strong>{{ $result->student?->name ?? 'N/A' }}</strong></div>
            <div class="col-md-3"><span class="text-muted">Email</span><br><strong>{{ $result->student?->email ?? '' }}</strong></div>
            <div class="col-md-3"><span class="text-muted">Bắt đầu</span><br><strong>{{ $result->started_at?->format('d/m/Y H:i:s') }}</strong></div>
            <div class="col-md-3"><span class="text-muted">Nộp bài</span><br><strong>{{ $result->submitted_at?->format('d/m/Y H:i:s') }}</strong></div>
        </div>
    </div>
</div>
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
                            @elseif(isset($answer->answer_id) && $answer->answer_id == $ans->id && !$answer->is_correct) <span class="text-danger fw-semibold small">Học sinh chọn</span> @endif
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
                    <small class="fw-bold text-info"><i class="bi bi-lightbulb me-1"></i>Giải thích:</small>
                    <p class="mb-0">{{ $question->explanation }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
