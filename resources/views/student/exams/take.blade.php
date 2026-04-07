@extends('layouts.app')

@section('title', 'Làm Bài thi')

@section('content')
<div class="min-h-screen bg-gray-100 py-4">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">{{ $exam->title }}</h1>
                    <p class="text-sm text-gray-500">{{ $exam->examQuestions->count() }} câu hỏi | {{ $exam->duration_mins ? $exam->duration_mins . ' phút' : 'Không giới hạn' }}</p>
                </div>
                <div id="timer" class="text-right">
                    @if($exam->duration_mins && isset($sessionData['expires_at']))
                        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg">
                            <p class="text-xs font-medium">Thời gian còn lại</p>
                            <p id="countdown" class="text-2xl font-bold" data-expires="{{ $sessionData['expires_at'] }}">--:--</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions Form -->
        <form action="{{ route('student.exams.submit', $exam) }}" method="POST" id="examForm">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">

            @php $qNum = 1; @endphp
            @foreach($questions as $question)
            <div class="bg-white rounded-lg shadow mb-4 p-6">
                <div class="flex gap-4 mb-4">
                    <span class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        {{ $qNum++ }}
                    </span>
                    <div class="flex-1">
                        <p class="text-lg font-medium text-gray-800 mb-4">{{ $question->content }}</p>

                        @if($question->type === 'single_choice')
                            <div class="space-y-2">
                                @foreach($question->answers as $answer)
                                <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="answers[{{ $question->id }}][answer_id]" value="{{ $answer->id }}"
                                        class="w-5 h-5 text-blue-600 focus:ring-blue-500" required>
                                    <span class="flex-1">{{ $answer->option_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($question->answers as $answer)
                                <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="answers[{{ $question->id }}][answer_ids][]" value="{{ $answer->id }}"
                                        class="w-5 h-5 text-blue-600 focus:ring-blue-500 rounded">
                                    <span class="flex-1">{{ $answer->option_text }}</span>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">* Chọn tất cả đáp án đúng</p>
                        @else
                            <div>
                                <input type="text" name="answers[{{ $question->id }}][text]"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Nhập câu trả lời..." required>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Submit -->
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Khi nộp bài, bạn sẽ không thể thay đổi câu trả lời.</p>
                <button type="submit" id="submitBtn" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-lg">
                    Nộp bài
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($exam->duration_mins && isset($sessionData['expires_at']))
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const expiresAt = new Date('{{ $sessionData["expires_at"] }}').getTime();
    const countdownEl = document.getElementById('countdown');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('examForm');

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            countdownEl.textContent = 'Hết giờ!';
            countdownEl.classList.add('text-red-800');
            form.submit();
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        countdownEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

        if (distance < 60000) {
            countdownEl.parentElement.classList.add('animate-pulse');
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    submitBtn.addEventListener('click', function(e) {
        if (!confirm('Bạn có chắc muốn nộp bài?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
@endif
