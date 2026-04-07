@extends('layouts.app')

@section('title', 'Danh sách Bài thi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Danh sách Bài thi</h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-400 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 border border-red-400 text-red-700 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    @if($exams->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-4 text-lg">Hiện không có bài thi nào khả dụng.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($exams as $exam)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $exam->title }}</h3>
                    @if(in_array($exam->id, $completedExams))
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Đã làm</span>
                    @endif
                </div>
                
                @if($exam->topic)
                    <p class="text-sm text-gray-500 mb-2">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ $exam->topic->name }}
                        </span>
                    </p>
                @endif

                @if($exam->description)
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($exam->description, 100) }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-gray-500">Câu hỏi</p>
                        <p class="font-bold text-gray-800">{{ $exam->exam_questions_count ?? $exam->examQuestions->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-gray-500">Thời gian</p>
                        <p class="font-bold text-gray-800">{{ $exam->duration_mins ?? '∞' }} phút</p>
                    </div>
                </div>

                <div class="text-sm text-gray-500 mb-4">
                    @if($exam->start_time)
                        <p>Bắt đầu: {{ $exam->start_time->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($exam->end_time)
                        <p>Kết thúc: {{ $exam->end_time->format('d/m/Y H:i') }}</p>
                    @endif
                </div>

                @if(in_array($exam->id, $completedExams))
                    <a href="{{ route('student.exams.show', $exam) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Xem chi tiết
                    </a>
                @else
                    <a href="{{ route('student.exams.show', $exam) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Làm bài thi
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
