@extends('layouts.app')

@section('title', 'Thêm cấu hình AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle me-2"></i>Thêm cấu hình AI</h2>
    <a href="{{ route('admin.ai-configs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.ai-configs.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" id="provider" class="form-select @error('provider') is-invalid @enderror" required>
                            <option value="">-- Chọn provider --</option>
                            <option value="openrouter" {{ old('provider') == 'openrouter' ? 'selected' : '' }}>OpenRouter (Recommended - Free models)</option>
                            <option value="groq" {{ old('provider') == 'groq' ? 'selected' : '' }}>Groq (Fast - Free tier)</option>
                            <option value="openai" {{ old('provider') == 'openai' ? 'selected' : '' }}>OpenAI (GPT models)</option>
                            <option value="anthropic" {{ old('provider') == 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                            <option value="google" {{ old('provider') == 'google' ? 'selected' : '' }}>Google (Gemini)</option>
                        </select>
                        @error('provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Model Name <span class="text-danger">*</span></label>
                        <input type="text" name="model_name" id="model_name" class="form-control @error('model_name') is-invalid @enderror" value="{{ old('model_name') }}" placeholder="Ví dụ: mistralai/mistral-7b-instruct" required>
                        <small class="text-muted">OpenRouter: mistralai/mistral-7b-instruct, Groq: llama-3.3-70b-versatile</small>
                        @error('model_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mục đích <span class="text-danger">*</span></label>
                <select name="purpose" class="form-select @error('purpose') is-invalid @enderror" required>
                    <option value="">-- Chọn mục đích --</option>
                    <option value="question_generation" {{ old('purpose') == 'question_generation' ? 'selected' : '' }}>Tạo câu hỏi</option>
                    <option value="answer_explanation" {{ old('purpose') == 'answer_explanation' ? 'selected' : '' }}>Giải thích đáp án</option>
                    <option value="result_evaluation" {{ old('purpose') == 'result_evaluation' ? 'selected' : '' }}>Đánh giá kết quả</option>
                    <option value="learning_path" {{ old('purpose') == 'learning_path' ? 'selected' : '' }}>Lộ trình học tập</option>
                    <option value="general" {{ old('purpose') == 'general' ? 'selected' : '' }}>Tổng quát</option>
                </select>
                @error('purpose')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">API Key <span class="text-danger">*</span></label>
                <input type="password" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key') }}" placeholder="sk-..." required>
                @error('api_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Base URL</label>
                <input type="url" name="base_url" class="form-control @error('base_url') is-invalid @enderror" value="{{ old('base_url') }}" placeholder="Tự động - để trống cho OpenRouter/Groq">
                <small class="text-muted">Để trống cho OpenRouter/Groq. Chỉ cần nhập nếu dùng proxy/custom endpoint.</small>
                @error('base_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Temperature</label>
                        <input type="number" name="temperature" class="form-control" value="{{ old('temperature', 0.7) }}" step="0.1" min="0" max="2">
                        <small class="text-muted">0-2, thấp hơn = nhất quán hơn</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Max Tokens</label>
                        <input type="number" name="max_tokens" class="form-control" value="{{ old('max_tokens', 2000) }}" min="100" max="100000">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Default Prompt</label>
                <textarea name="default_prompt" class="form-control" rows="3">{{ old('default_prompt') }}</textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Lưu cấu hình
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
