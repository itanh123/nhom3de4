@extends('layouts.app')

@section('title', 'Chi tiết Bài thi')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('student.exams.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Quay lại danh sách bài thi
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $exam->title }}</h1>
            @if($exam->topic)
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                    {{ $exam->topic->name }}
                </span>
            @endif
        </div>

        @if($exam->description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Mô tả</h3>
                <p class="text-gray-700">{{ $exam->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500 mb-1">Số câu hỏi</p>
                <p class="text-2xl font-bold text-gray-800">{{ $exam->examQuestions->count() }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500 mb-1">Thời gian</p>
                <p class="text-2xl font-bold text-gray-800">{{ $exam->duration_mins ?? '∞' }}</p>
                <p class="text-xs text-gray-500">phút</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500 mb-1">Điểm đạt</p>
                <p class="text-2xl font-bold text-gray-800">{{ $exam->pass_score }}%</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500 mb-1">Xáo trộn</p>
                <p class="text-2xl font-bold text-gray-800">{{ $exam->shuffle_q ? 'Có' : 'Không' }}</p>
            </div>
        </div>

        @if($exam->start_time || $exam->end_time)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="text-sm font-semibold text-yellow-700 mb-2">Lịch thi</h3>
                <div class="grid grid-cols-2 gap-4 text-sm text-yellow-800">
                    @if($exam->start_time)
                        <div>
                            <span class="font-medium">Bắt đầu:</span> {{ $exam->start_time->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    @if($exam->end_time)
                        <div>
                            <span class="font-medium">Kết thúc:</span> {{ $exam->end_time->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($alreadyTaken)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-700 font-semibold">Bạn đã hoàn thành bài thi này!</p>
                <a href="{{ route('student.results.index') }}" class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Xem kết quả
                </a>
            </div>
        @else
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
                <svg class="mx-auto h-12 w-12 text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <p class="text-blue-700 font-semibold mb-4">Sẵn sàng làm bài?</p>
                <form action="{{ route('student.exams.start', $exam) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Bắt đầu làm bài
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
