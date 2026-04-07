@extends('admin.layout')

@section('title', 'Làm Bài thi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><h4 class="fw-bold mb-0">{{ $exam->title }}</h4><small class="text-muted">{{ $exam->examQuestions->count() }} câu | {{ $exam->duration_mins ? $exam->duration_mins . ' phút' : 'Không giới hạn' }}</small></div>
                @if($exam->duration_mins && isset($sessionData['expires_at']))
                <div class="bg-danger bg-opacity-10 text-danger px-3 py-2 rounded text-center"><small class="fw-medium d-block">Còn lại</small><span id="countdown" class="fs-4 fw-bold" data-expires="{{ $sessionData['expires_at'] }}">--:--</span></div>
                @endif
            </div>
        </div>
        @if($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('student.exams.submit', $exam) }}" method="POST" id="examForm">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
            @php $qNum = 1; @endphp
            @foreach($questions as $question)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">{{ $qNum++ }}</span>
                        <div class="flex-grow-1">
                            <p class="fw-medium mb-3">{{ $question->content }}</p>
                            @if($question->type === 'single_choice')
                            <div class="d-grid gap-2">
                                @foreach($question->answers as $answer)
                                <label class="d-flex align-items-center gap-3 p-3 bg-light rounded" style="cursor:pointer"><input type="radio" name="answers[{{ $question->id }}][answer_id]" value="{{ $answer->id }}" class="form-check-input"><span>{{ $answer->option_text }}</span></label>
                                @endforeach
                            </div>
                            @elseif($question->type === 'multiple_choice')
                            <div class="d-grid gap-2">
                                @foreach($question->answers as $answer)
                                <label class="d-flex align-items-center gap-3 p-3 bg-light rounded" style="cursor:pointer"><input type="checkbox" name="answers[{{ $question->id }}][answer_ids][]" value="{{ $answer->id }}" class="form-check-input"><span>{{ $answer->option_text }}</span></label>
                                @endforeach
                            </div><small class="text-muted mt-2 d-block">* Chọn tất cả đáp án đúng</small>
                            @else
                            <input type="text" name="answers[{{ $question->id }}][text]" class="form-control" placeholder="Nhập câu trả lời...">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="card shadow-sm"><div class="card-body text-center"><p class="text-muted mb-3">Khi nộp bài, bạn không thể thay đổi câu trả lời.</p><button type="submit" id="submitBtn" class="btn btn-success btn-lg"><i class="bi bi-check-circle me-1"></i>Nộp bài</button></div></div>
        </form>
    </div>
</div>
@endsection

@if($exam->duration_mins && isset($sessionData['expires_at']))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let remainingSeconds = Math.max(0, {{ \Carbon\Carbon::parse($sessionData['expires_at'])->getTimestamp() - time() }});
    const el = document.getElementById('countdown');
    const form = document.getElementById('examForm');
    
    function tick() {
        if (remainingSeconds <= 0) { 
            el.textContent = 'Hết giờ!'; 
            form.submit(); 
            return; 
        }
        
        const m = Math.floor(remainingSeconds / 60);
        const s = remainingSeconds % 60;
        el.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        remainingSeconds--;
        setTimeout(tick, 1000);
    }
    
    setTimeout(tick, 1000);
    tick(); // Run immediately for first render
    
    document.getElementById('submitBtn').addEventListener('click', function(e) { 
        if (!confirm('Bạn có chắc muốn nộp bài? Khuyến cáo: Các câu chưa trả lời sẽ không có điểm.')) {
            e.preventDefault(); 
        }
    });
});
</script>
@endpush
@endif
