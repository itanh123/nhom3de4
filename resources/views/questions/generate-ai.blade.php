@extends('admin.layout')

@section('title', 'Tạo câu hỏi bằng AI')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Tạo câu hỏi bằng AI</h1>
        <p class="text-slate-500 mt-1">Sử dụng AI để tạo câu hỏi trắc nghiệm tự động</p>
    </div>
    <a href="{{ route('questions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Quay lại
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <form method="POST" action="{{ route('questions.generate-ai') }}" id="aiForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Chủ đề <span class="text-red-500">*</span></label>
                        <select name="topic_id" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Tài liệu tham khảo</label>
                        <select name="document_id" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Không chọn --</option>
                            @foreach($documents as $doc)
                                <option value="{{ $doc->id }}" {{ old('document_id') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->file_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Tùy chọn: Cung cấp tài liệu để AI tạo câu hỏi chính xác hơn</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Số lượng câu hỏi <span class="text-red-500">*</span></label>
                        <input type="number" name="number" value="{{ old('number', 5) }}" min="1" max="50" 
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Độ khó <span class="text-red-500">*</span></label>
                        <select name="difficulty" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                            <option value="medium" {{ old('difficulty', 'medium') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Loại câu hỏi <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="single_choice" {{ old('type', 'single_choice') == 'single_choice' ? 'selected' : '' }}>Một lựa chọn</option>
                            <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Nhiều lựa chọn</option>
                            <option value="fill_in_blank" {{ old('type') == 'fill_in_blank' ? 'selected' : '' }}>Điền trống</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-600 mb-2">Yêu cầu bổ sung</label>
                    <textarea name="prompt" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Ví dụ: Tập trung vào từ vựng và ngữ pháp cơ bản...">{{ old('prompt') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">Tùy chọn: Thêm yêu cầu đặc biệt cho AI</p>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2" id="generateBtn">
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Tạo câu hỏi
                    </button>
                    <button type="button" class="px-6 py-3 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors" onclick="document.getElementById('aiForm').reset()">
                        Đặt lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-indigo-600">info</span>
                </div>
                <h3 class="font-bold text-slate-800">Hướng dẫn</h3>
            </div>
            
            <div class="space-y-3 text-sm text-slate-600">
                <p><strong>1. Chọn chủ đề:</strong> Chọn chủ đề chính cho câu hỏi</p>
                <p><strong>2. Tài liệu tham khảo:</strong> Cung cấp tài liệu để AI hiểu rõ hơn về nội dung</p>
                <p><strong>3. Số lượng:</strong> 1-50 câu hỏi mỗi lần tạo</p>
                <p><strong>4. Độ khó:</strong> Dễ / Trung bình / Khó</p>
                <p><strong>5. Xem trước:</strong> Bạn có thể xem và chọn câu hỏi muốn lưu</p>
            </div>

            <div class="mt-4 p-3 bg-white/50 rounded-xl">
                <p class="text-xs text-slate-500">
                    <span class="material-symbols-outlined text-sm align-middle">settings</span>
                    Đảm bảo đã có AI Config active cho "Tạo câu hỏi" trong 
                    <a href="{{ route('admin.ai-configs.index') }}" class="text-blue-600 hover:underline">AI Config</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
