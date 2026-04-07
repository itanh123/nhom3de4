@extends('admin.layout')

@section('title', 'Chi tiết cấu hình AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-robot me-2"></i>Chi tiết cấu hình AI</h2>
    <a href="{{ route('admin.ai-configs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Provider:</th>
                        <td><strong class="text-uppercase">{{ $aiConfig->provider }}</strong></td>
                    </tr>
                    <tr>
                        <th>Model:</th>
                        <td>{{ $aiConfig->model_name }}</td>
                    </tr>
                    <tr>
                        <th>Mục đích:</th>
                        <td>
                            @switch($aiConfig->purpose)
                                @case('question_generation') <span class="badge bg-primary">Tạo câu hỏi</span> @break
                                @case('answer_explanation') <span class="badge bg-info">Giải thích đáp án</span> @break
                                @case('result_evaluation') <span class="badge bg-warning">Đánh giá kết quả</span> @break
                                @case('learning_path') <span class="badge bg-secondary">Lộ trình học tập</span> @break
                                @default <span class="badge bg-dark">Tổng quát</span>
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <th>API Key:</th>
                        <td><code>{{ substr(decrypt($aiConfig->api_key), 0, 15) }}...</code></td>
                    </tr>
                    <tr>
                        <th>Base URL:</th>
                        <td>{{ $aiConfig->base_url ?: 'Mặc định' }}</td>
                    </tr>
                    <tr>
                        <th>Temperature:</th>
                        <td>{{ $aiConfig->temperature }}</td>
                    </tr>
                    <tr>
                        <th>Max Tokens:</th>
                        <td>{{ $aiConfig->max_tokens }}</td>
                    </tr>
                    <tr>
                        <th>Default Prompt:</th>
                        <td><small>{{ $aiConfig->default_prompt ?: 'Không có' }}</small></td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if($aiConfig->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Người tạo:</th>
                        <td>{{ $aiConfig->creator->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Người sửa:</th>
                        <td>{{ $aiConfig->updater->name ?? 'Chưa sửa' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo:</th>
                        <td>{{ $aiConfig->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Cập nhật:</th>
                        <td>{{ $aiConfig->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>

                <hr>

                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.ai-configs.edit', $aiConfig) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Sửa
                        </a>
                        <form action="{{ route('admin.ai-configs.destroy', $aiConfig) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa cấu hình này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                    <form action="{{ route('admin.ai-configs.toggle', $aiConfig) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $aiConfig->is_active ? 'btn-secondary' : 'btn-success' }}">
                            <i class="bi bi-toggle-{{ $aiConfig->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $aiConfig->is_active ? 'Tắt' : 'Bật' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
