@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2>Reports</h2><p class="text-muted mb-0">Live analytics from current database records.</p></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Total Users</small><h3 class="fw-bold mb-0">{{ $overview['total_users'] }}</h3></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Total Topics</small><h3 class="fw-bold mb-0">{{ $overview['total_topics'] }}</h3></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Average Score</small><h3 class="fw-bold mb-0">{{ $overview['avg_score'] }}%</h3></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Users by Role</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Role</th><th>Count</th></tr></thead>
                <tbody>
                    @forelse ($userByRole as $row)
                    <tr><td class="ps-3 text-capitalize">{{ $row->role }}</td><td>{{ $row->total }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted py-4">No report data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
