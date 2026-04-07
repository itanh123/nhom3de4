@extends('admin.layout')

@section('title', 'Chi tiết nhật ký')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history me-2"></i>Chi tiết nhật ký</h2>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">ID:</th>
                        <td>{{ $activityLog->id }}</td>
                    </tr>
                    <tr>
                        <th>Thời gian:</th>
                        <td>{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Người dùng:</th>
                        <td>
                            @if($activityLog->user)
                                <span class="badge bg-primary">{{ $activityLog->user->name }}</span>
                                <small class="text-muted">({{ $activityLog->user->email }})</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Hành động:</th>
                        <td><code class="bg-light px-2 py-1 rounded">{{ $activityLog->action }}</code></td>
                    </tr>
                    <tr>
                        <th>Đối tượng:</th>
                        <td>
                            @if($activityLog->entity_type)
                                <span class="badge bg-secondary">{{ $activityLog->entity_type }}</span>
                                @if($activityLog->entity_id)
                                    #{{ $activityLog->entity_id }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Mô tả:</th>
                        <td>{{ $activityLog->description ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>IP Address:</th>
                        <td><code>{{ $activityLog->ip_address ?: '-' }}</code></td>
                    </tr>
                    <tr>
                        <th>User Agent:</th>
                        <td><small class="text-muted">{{ $activityLog->user_agent ?: '-' }}</small></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
