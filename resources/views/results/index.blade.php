@extends('admin.layout')

@section('title', 'Quản lý Kết quả')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clipboard-data me-2"></i>Quản lý Kết quả</h2>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3"><label class="form-label small">Tìm kiếm</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Tên học sinh..." class="form-control"></div>
            <div class="col-md-3"><label class="form-label small">Bài thi</label><select name="exam_id" class="form-select"><option value="">Tất cả</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label small">Kết quả</label><select name="passed" class="form-select"><option value="">Tất cả</option><option value="1" {{ request('passed') === '1' ? 'selected' : '' }}>Đạt</option><option value="0" {{ request('passed') === '0' ? 'selected' : '' }}>Không đạt</option></select></div>
            <div class="col-md-3 d-flex align-items-end gap-2"><button type="submit" class="btn btn-secondary">Lọc</button><a href="{{ route('results.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Học sinh</th><th>Bài thi</th><th class="text-center">Điểm</th><th class="text-center">Kết quả</th><th class="text-center">Ngày nộp</th><th class="text-center">Hành động</th></tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;"><small class="fw-bold text-primary">{{ substr($result->student?->name ?? 'N', 0, 1) }}</small></div>
                                <div><div class="fw-medium small">{{ $result->student?->name ?? 'N/A' }}</div><small class="text-muted">{{ $result->student?->email ?? '' }}</small></div>
                            </div>
                        </td>
                        <td><div class="small fw-medium">{{ $result->exam?->title ?? 'N/A' }}</div></td>
                        <td class="text-center"><span class="fw-bold">{{ $result->score_pct }}%</span><br><small class="text-muted">{{ $result->correct_count }}/{{ $result->total_questions }}</small></td>
                        <td class="text-center">@if($result->passed) <span class="badge bg-success">Đạt</span> @else <span class="badge bg-danger">Không đạt</span> @endif</td>
                        <td class="text-center small text-muted">{{ $result->submitted_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                        <td class="text-center"><a href="{{ route('results.show', $result) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Xem</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Chưa có kết quả nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@if($results->hasPages())<div class="d-flex justify-content-center mt-4">{{ $results->appends(request()->query())->links() }}</div>@endif
@endsection
