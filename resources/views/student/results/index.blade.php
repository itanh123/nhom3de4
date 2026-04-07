@extends('admin.layout')

@section('title', 'Lịch sử kết quả')

@section('content')
<div class="mb-4"><h2><i class="bi bi-clock-history me-2"></i>Lịch sử kết quả</h2></div>
@if(session('success'))<div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
@if($results->isEmpty())
<div class="card shadow-sm"><div class="card-body text-center py-5 text-muted"><i class="bi bi-clipboard fs-1 d-block mb-3"></i><p class="fs-5">Bạn chưa có kết quả nào.</p><a href="{{ route('student.exams.index') }}" class="btn btn-primary mt-2">Làm bài thi</a></div></div>
@else
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Bài thi</th><th class="text-center">Điểm</th><th class="text-center">Kết quả</th><th class="text-center">Ngày nộp</th><th class="text-center">Thao tác</th></tr></thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td class="ps-3"><div class="fw-medium">{{ $result->exam?->title ?? 'N/A' }}</div><small class="text-muted">{{ $result->exam?->topic?->name }}</small></td>
                        <td class="text-center"><span class="fs-5 fw-bold">{{ $result->score_pct }}%</span><br><small class="text-muted">{{ $result->correct_count }}/{{ $result->total_questions }} đúng</small></td>
                        <td class="text-center">@if($result->passed) <span class="badge bg-success">Đạt</span> @else <span class="badge bg-danger">Không đạt</span> @endif</td>
                        <td class="text-center small text-muted">{{ $result->submitted_at ? $result->submitted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td class="text-center"><a href="{{ route('student.results.show', $result) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@if($results->hasPages())<div class="d-flex justify-content-center mt-4">{{ $results->links() }}</div>@endif
@endif
@endsection
