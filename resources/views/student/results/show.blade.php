@extends('layouts.app')

@section('title', 'Kết quả Bài thi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('student.results.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Quay lại lịch sử
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 flex items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
    </div>
    @endif

    <!-- Score Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $result->exam?->title ?? 'N/A' }}</h1>
            <div class="inline-block px-6 py-3 rounded-xl {{ $result->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                <p class="text-lg font-bold">
                    {{ $result->passed ? 'CHÚC MỪNG! BẠN ĐÃ ĐẠT' : 'RẤT TIẾC! BẠN CHƯA ĐẠT' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-sm text-blue-600 mb-1">Điểm số</p>
                <p class="text-3xl font-bold text-blue-700">{{ $result->score_pct }}%</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-sm text-green-600 mb-1">Đúng</p>
                <p class="text-3xl font-bold text-green-700">{{ $result->correct_count }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-sm text-red-600 mb-1">Sai</p>
                <p class="text-3xl font-bold text-red-700">{{ $result->total_questions - $result->correct_count }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600 mb-1">Tổng câu</p>
                <p class="text-3xl font-bold text-gray-700">{{ $result->total_questions }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-4">
            <div>
                <span class="font-medium">Bắt đầu:</span> {{ $result->started_at->format('d/m/Y H:i:s') }}
            </div>
            <div>
                <span class="font-medium">Nộp bài:</span> {{ $result->submitted_at->format('d/m/Y H:i:s') }}
            </div>
            <div>
                <span class="font-medium">Thời gian làm:</span> {{ $result->started_at->diffForHumans($result->submitted_at, true) }}
            </div>
            <div>
                <span class="font-medium">Điểm đạt:</span> {{ $result->exam?->pass_score ?? 'N/A' }}%
            </div>
        </div>

        @php
            $hasExplanationConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_ANSWER_EXPLANATION)->exists();
            $hasLearningPathConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_LEARNING_PATH)->exists();
        @endphp

        @if($hasExplanationConfig || $hasLearningPathConfig)
        <div class="border-t pt-4 mt-4">
            <div class="flex flex-wrap gap-3">
                @if($hasLearningPathConfig)
                <form action="{{ route('student.results.ai-learning-path', $result) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 flex items-center gap-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        Nhận lộ trình học tập
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- AI Learning Path -->
    @php
        $suggestions = is_string($result->ai_suggestions) ? json_decode($result->ai_suggestions, true) : ($result->ai_suggestions ?? []);
        $learningPath = $suggestions['learning_path'] ?? null;
    @endphp

    @if($learningPath)
    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <h3 class="text-lg font-bold text-slate-800">Lộ trình học tập của bạn</h3>
        </div>

        @if(!empty($learningPath['overall_goal']))
        <div class="bg-white/60 rounded-lg p-4 mb-4">
            <p class="text-slate-700 font-medium">{{ $learningPath['overall_goal'] }}</p>
        </div>
        @endif

        @if(!empty($learningPath['weekly_plan']))
        <div class="space-y-3">
            <h4 class="font-semibold text-slate-700">Kế hoạch tuần:</h4>
            @foreach($learningPath['weekly_plan'] as $week)
            <div class="bg-white/60 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded">Tuần {{ $week['week'] ?? '' }}</span>
                    <span class="font-medium text-slate-700">{{ $week['focus'] ?? '' }}</span>
                    @if(!empty($week['estimated_time']))
                    <span class="text-xs text-slate-500 ml-auto">{{ $week['estimated_time'] }}</span>
                    @endif
                </div>
                @if(!empty($week['activities']))
                <ul class="text-sm text-slate-600 ml-4">
                    @foreach($week['activities'] as $activity)
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 mt-1">→</span> {{ $activity }}
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @if(empty($learningPath['weekly_plan']) && !empty($learningPath['raw']))
        <div class="bg-white/60 rounded-lg p-4">
            <pre class="whitespace-pre-wrap text-sm text-slate-700 font-sans">{{ $learningPath['raw'] }}</pre>
        </div>
        @endif

        @if(!empty($learningPath['recommended_resources']))
        <div class="mt-4">
            <h4 class="font-semibold text-slate-700 mb-2">Tài liệu gợi ý:</h4>
            <ul class="space-y-1">
                @foreach($learningPath['recommended_resources'] as $resource)
                <li class="flex items-start gap-2 text-sm text-slate-600">
                    <span class="text-emerald-500">📚</span> {{ $resource }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(!empty($learningPath['tips']))
        <div class="mt-4">
            <h4 class="font-semibold text-slate-700 mb-2">Mẹo học tập:</h4>
            <ul class="space-y-1">
                @foreach($learningPath['tips'] as $tip)
                <li class="flex items-start gap-2 text-sm text-slate-600">
                    <span class="text-emerald-500">💡</span> {{ $tip }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <!-- Answers Review -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-800">Chi tiết câu trả lời</h2>

        @php $qNum = 1; @endphp
        @foreach($result->examAnswers as $answer)
        @php $question = $answer->question; @endphp
        @if(!$question) @php $qNum++; @endphp @continue @endif
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex gap-4 mb-4">
                <span class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-white {{ $answer->is_correct ? 'bg-green-500' : 'bg-red-500' }}">
                    {{ $qNum++ }}
                </span>
                <div class="flex-1">
                    <p class="text-lg font-medium text-gray-800 mb-4">{{ $question->content }}</p>

                    @if($question->type === 'single_choice')
                        @foreach($question->answers as $ans)
                        <div class="p-3 rounded-lg mb-2 {{ $ans->is_correct ? 'bg-green-100 border-2 border-green-400' : (isset($answer->answer_id) && $answer->answer_id == $ans->id ? 'bg-red-100 border-2 border-red-400' : 'bg-gray-50') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ chr(64 + $loop->iteration) }}.</span>
                                    <span>{{ $ans->option_text }}</span>
                                </div>
                                @if($ans->is_correct)
                                    <span class="text-green-600 font-semibold">Đáp án đúng</span>
                                @elseif(isset($answer->answer_id) && $answer->answer_id == $ans->id && !$answer->is_correct)
                                    <span class="text-red-600 font-semibold">Bạn chọn</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @elseif($question->type === 'multiple_choice')
                        @foreach($question->answers as $ans)
                        <div class="p-3 rounded-lg mb-2 {{ $ans->is_correct ? 'bg-green-100 border-2 border-green-400' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ chr(64 + $loop->iteration) }}.</span>
                                    <span>{{ $ans->option_text }}</span>
                                </div>
                                @if($ans->is_correct)
                                    <span class="text-green-600 font-semibold">✓ Đáp án đúng</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="p-4 rounded-lg {{ $answer->is_correct ? 'bg-green-100 border-2 border-green-400' : 'bg-red-100 border-2 border-red-400' }}">
                            <p class="text-sm text-gray-600 mb-1">Câu trả lời của bạn:</p>
                            <p class="font-medium text-gray-800">{{ $answer->text_answer ?: 'Không trả lời' }}</p>
                            @if(!$answer->is_correct)
                                <p class="text-sm text-green-600 mt-2">
                                    <span class="font-medium">Đáp án đúng:</span> 
                                    {{ $question->answers->pluck('option_text')->implode(' | ') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($result->exam?->show_explain && $question->explanation)
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm font-semibold text-blue-700 mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Giải thích:
                            </p>
                            <p class="text-gray-700">{{ $question->explanation }}</p>
                        </div>
                    @endif

                    @if($hasExplanationConfig)
                        @if($answer->ai_explanation)
                        <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-indigo-700">Giải thích từ AI:</span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $answer->ai_explanation }}</p>
                        </div>
                        @else
                        <div class="mt-3">
                            <form action="{{ route('student.results.ai-explain', $result) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <button type="submit" class="text-sm px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    Giải thích bằng AI
                                </button>
                            </form>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('student.exams.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
            Làm bài thi khác
        </a>
    </div>
</div>
@endsection
