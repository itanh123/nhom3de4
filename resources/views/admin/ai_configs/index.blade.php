@extends('admin.layout')

@section('title', 'Cấu hình AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-robot me-2"></i>Cấu hình AI</h2>
    <a href="{{ route('admin.ai-configs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm mới
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="provider" class="form-select">
                    <option value="">-- Provider --</option>
                    <option value="openrouter" {{ request('provider') == 'openrouter' ? 'selected' : '' }}>OpenRouter</option>
                    <option value="groq" {{ request('provider') == 'groq' ? 'selected' : '' }}>Groq</option>
                    <option value="openai" {{ request('provider') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                    <option value="anthropic" {{ request('provider') == 'anthropic' ? 'selected' : '' }}>Anthropic</option>
                    <option value="google" {{ request('provider') == 'google' ? 'selected' : '' }}>Google</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="purpose" class="form-select">
                    <option value="">-- Mục đích --</option>
                    <option value="question_generation" {{ request('purpose') == 'question_generation' ? 'selected' : '' }}>Tạo câu hỏi</option>
                    <option value="answer_explanation" {{ request('purpose') == 'answer_explanation' ? 'selected' : '' }}>Giải thích đáp án</option>
                    <option value="result_evaluation" {{ request('purpose') == 'result_evaluation' ? 'selected' : '' }}>Đánh giá kết quả</option>
                    <option value="learning_path" {{ request('purpose') == 'learning_path' ? 'selected' : '' }}>Lộ trình học tập</option>
                    <option value="general" {{ request('purpose') == 'general' ? 'selected' : '' }}>Tổng quát</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Lọc</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Provider</th>
                        <th>Model</th>
                        <th>Mục đích</th>
                        <th>API Key</th>
                        <th>Trạng thái</th>
                        <th>Người tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($configs as $config)
                    <tr class="{{ $config->is_active ? 'table-success' : '' }}">
                        <td><strong>{{ strtoupper($config->provider) }}</strong></td>
                        <td>{{ $config->model_name }}</td>
                        <td>
                            @switch($config->purpose)
                                @case('question_generation') <span class="badge bg-primary">Tạo câu hỏi</span> @break
                                @case('answer_explanation') <span class="badge bg-info">Giải thích</span> @break
                                @case('result_evaluation') <span class="badge bg-warning">Đánh giá</span> @break
                                @case('learning_path') <span class="badge bg-secondary">Lộ trình</span> @break
                                @default <span class="badge bg-dark">Tổng quát</span>
                            @endswitch
                        </td>
                        <td><code>{{ substr($config->api_key, 0, 10) }}...{{ substr($config->api_key, -4) }}</code></td>
                        <td>
                            @if($config->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $config->creator->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.ai-configs.show', $config) }}" class="btn btn-sm btn-outline-primary" title="Xem">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.ai-configs.edit', $config) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.ai-configs.toggle', $config) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $config->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $config->is_active ? 'Tắt' : 'Bật' }}">
                                    <i class="bi bi-toggle-{{ $config->is_active ? 'on' : 'off' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.ai-configs.destroy', $config) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa cấu hình này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có cấu hình AI nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $configs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
