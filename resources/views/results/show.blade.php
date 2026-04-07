@extends('admin.layout')

@section('title', 'Chi tiết Kết quả')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Chi tiết Kết quả</h1>
    <a href="{{ route('results.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Quay lại
    </a>
</div>

<!-- Score Card -->
<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $result->exam?->title ?? 'N/A' }}</h2>
            <p class="text-slate-500">{{ $result->exam?->topic?->name ?? '' }}</p>
        </div>
        <div class="flex items-center gap-3">
            @php
                $hasAiConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_RESULT_EVALUATION)->exists();
            @endphp
            @if($hasAiConfig)
            <form action="{{ route('results.ai-evaluate', $result) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-lg">auto_awesome</span>
                    Đánh giá AI
                </button>
            </form>
            @endif
            <span class="px-4 py-2 rounded-xl text-lg font-bold {{ $result->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $result->passed ? 'ĐẠT' : 'KHÔNG ĐẠT' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-xl p-4 text-center">
            <p class="text-sm text-blue-600 mb-1">Điểm số</p>
            <p class="text-3xl font-bold text-blue-700">{{ $result->score_pct }}%</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <p class="text-sm text-green-600 mb-1">Đúng</p>
            <p class="text-3xl font-bold text-green-700">{{ $result->correct_count }}</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <p class="text-sm text-red-600 mb-1">Sai</p>
            <p class="text-3xl font-bold text-red-700">{{ $result->total_questions - $result->correct_count }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600 mb-1">Tổng câu</p>
            <p class="text-3xl font-bold text-gray-700">{{ $result->total_questions }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-slate-500">Học sinh</p>
            <p class="font-medium text-slate-800">{{ $result->student?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-slate-500">Email</p>
            <p class="font-medium text-slate-800">{{ $result->student?->email ?? '' }}</p>
        </div>
        <div>
            <p class="text-slate-500">Bắt đầu</p>
            <p class="font-medium text-slate-800">{{ $result->started_at?->format('d/m/Y H:i:s') }}</p>
        </div>
        <div>
            <p class="text-slate-500">Nộp bài</p>
            <p class="font-medium text-slate-800">{{ $result->submitted_at?->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</div>

<!-- Answers Review -->
<div class="space-y-4">
    <h3 class="text-lg font-bold text-slate-800">Chi tiết câu trả lời</h3>

    @php $qNum = 1; @endphp
    @foreach($result->examAnswers as $answer)
    @php $question = $answer->question; @endphp
    @if(!$question) @php $qNum++; @endphp @continue @endif
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex gap-4 mb-4">
            <span class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-white {{ $answer->is_correct ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $qNum++ }}
            </span>
            <div class="flex-1">
                <p class="text-lg font-medium text-slate-800 mb-4">{{ $question->content }}</p>

                @if($question->type === 'single_choice')
                    @foreach($question->answers as $ans)
                    <div class="p-3 rounded-lg mb-2 {{ $ans->is_correct ? 'bg-green-100 border-2 border-green-400' : (isset($answer->answer_id) && $answer->answer_id == $ans->id ? 'bg-red-100 border-2 border-red-400' : 'bg-gray-50') }}">
                        <span class="font-medium">{{ chr(64 + $loop->iteration) }}.</span> {{ $ans->option_text }}
                        @if($ans->is_correct)
                            <span class="float-right text-green-600 font-semibold">Đáp án đúng</span>
                        @elseif(isset($answer->answer_id) && $answer->answer_id == $ans->id && !$answer->is_correct)
                            <span class="float-right text-red-600 font-semibold">Học sinh chọn</span>
                        @endif
                    </div>
                    @endforeach
                @elseif($question->type === 'multiple_choice')
                    @foreach($question->answers as $ans)
                    <div class="p-3 rounded-lg mb-2 {{ $ans->is_correct ? 'bg-green-100 border-2 border-green-400' : 'bg-gray-50' }}">
                        <span class="font-medium">{{ chr(64 + $loop->iteration) }}.</span> {{ $ans->option_text }}
                        @if($ans->is_correct)
                            <span class="float-right text-green-600 font-semibold">✓</span>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="p-4 rounded-lg {{ $answer->is_correct ? 'bg-green-100 border-2 border-green-400' : 'bg-red-100 border-2 border-red-400' }}">
                        <p class="text-sm text-gray-600 mb-1">Câu trả lời:</p>
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
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <p class="text-sm font-semibold text-blue-700 mb-1">Giải thích:</p>
                        <p class="text-gray-700">{{ $question->explanation }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- AI Evaluation -->
@if($result->ai_summary || $result->ai_suggestions)
@php
    $suggestions = is_string($result->ai_suggestions) ? json_decode($result->ai_suggestions, true) : ($result->ai_suggestions ?? []);
@endphp
<div class="mt-6 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-6">
    <div class="flex items-center gap-3 mb-4">
        <span class="material-symbols-outlined text-indigo-600">auto_awesome</span>
        <h3 class="text-lg font-bold text-slate-800">Đánh giá từ AI</h3>
    </div>
    
    @if($result->ai_summary)
    <div class="bg-white/60 rounded-xl p-4 mb-4">
        <p class="text-slate-700 font-medium">{{ $result->ai_summary }}</p>
    </div>
    @endif

    @if(!empty($suggestions['strengths']))
    <div class="mb-3">
        <h4 class="text-sm font-semibold text-green-700 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">thumb_up</span> Điểm mạnh
        </h4>
        <ul class="space-y-1">
            @foreach($suggestions['strengths'] as $s)
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-green-500 mt-1">✓</span> {{ $s }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($suggestions['weaknesses']))
    <div class="mb-3">
        <h4 class="text-sm font-semibold text-red-700 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">warning</span> Điểm cần cải thiện
        </h4>
        <ul class="space-y-1">
            @foreach($suggestions['weaknesses'] as $w)
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-red-500 mt-1">!</span> {{ $w }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($suggestions['suggestions']))
    <div>
        <h4 class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">lightbulb</span> Gợi ý cải thiện
        </h4>
        <ul class="space-y-1">
            @foreach($suggestions['suggestions'] as $sug)
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-blue-500 mt-1">→</span> {{ $sug }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

<!-- Learning Path Suggestion -->
@php
    $hasLearningPathConfig = \App\Models\AiConfig::active()->byPurpose(\App\Models\AiConfig::PURPOSE_LEARNING_PATH)->exists();
@endphp
@if($hasLearningPathConfig && $result->ai_summary)
<div class="mt-4">
    <form action="{{ route('results.ai-learning-path', $result) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-lg">route</span>
            Tạo lộ trình học tập
        </button>
    </form>
</div>
@endif
@endsection
