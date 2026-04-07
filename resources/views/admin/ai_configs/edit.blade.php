@extends('admin.layout')

@section('title', 'Sửa cấu hình AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square me-2"></i>Sửa cấu hình AI</h2>
    <a href="{{ route('admin.ai-configs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.ai-configs.update', $aiConfig) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" class="form-select @error('provider') is-invalid @enderror" required>
                            <option value="openrouter" {{ $aiConfig->provider == 'openrouter' ? 'selected' : '' }}>OpenRouter (Recommended - Free models)</option>
                            <option value="groq" {{ $aiConfig->provider == 'groq' ? 'selected' : '' }}>Groq (Fast - Free tier)</option>
                            <option value="openai" {{ $aiConfig->provider == 'openai' ? 'selected' : '' }}>OpenAI (GPT models)</option>
                            <option value="anthropic" {{ $aiConfig->provider == 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                            <option value="google" {{ $aiConfig->provider == 'google' ? 'selected' : '' }}>Google (Gemini)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Model Name <span class="text-danger">*</span></label>
                        <input type="text" name="model_name" class="form-control @error('model_name') is-invalid @enderror" value="{{ old('model_name', $aiConfig->model_name) }}" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mục đích <span class="text-danger">*</span></label>
                <select name="purpose" class="form-select @error('purpose') is-invalid @enderror" required>
                    <option value="question_generation" {{ $aiConfig->purpose == 'question_generation' ? 'selected' : '' }}>Tạo câu hỏi</option>
                    <option value="answer_explanation" {{ $aiConfig->purpose == 'answer_explanation' ? 'selected' : '' }}>Giải thích đáp án</option>
                    <option value="result_evaluation" {{ $aiConfig->purpose == 'result_evaluation' ? 'selected' : '' }}>Đánh giá kết quả</option>
                    <option value="learning_path" {{ $aiConfig->purpose == 'learning_path' ? 'selected' : '' }}>Lộ trình học tập</option>
                    <option value="general" {{ $aiConfig->purpose == 'general' ? 'selected' : '' }}>Tổng quát</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">API Key mới</label>
                <input type="password" name="api_key" class="form-control" placeholder="Để trống nếu không đổi">
                <small class="text-muted">Chỉ nhập nếu muốn thay đổi API key</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Base URL</label>
                <input type="url" name="base_url" class="form-control" value="{{ old('base_url', $aiConfig->base_url) }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Temperature</label>
                        <input type="number" name="temperature" class="form-control" value="{{ old('temperature', $aiConfig->temperature) }}" step="0.1" min="0" max="2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Max Tokens</label>
                        <input type="number" name="max_tokens" class="form-control" value="{{ old('max_tokens', $aiConfig->max_tokens) }}" min="100" max="100000">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Default Prompt</label>
                <textarea name="default_prompt" class="form-control" rows="3">{{ old('default_prompt', $aiConfig->default_prompt) }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    @if($aiConfig->is_active)
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đang Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>

        {{-- Toggle form OUTSIDE the edit form to avoid nested form issue --}}
        @if(!$aiConfig->is_active)
        <div class="mt-3">
            <form action="{{ route('admin.ai-configs.toggle', $aiConfig) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-success btn-sm">Bật cấu hình này</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
