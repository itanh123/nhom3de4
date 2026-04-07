@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2>Role Management</h2><p class="text-muted mb-0">Assign roles and activation state for existing users.</p></div>
</div>

<div class="row g-4 mb-4">
    @foreach ($roleStats as $stat)
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body"><small class="text-muted text-uppercase">{{ $stat->role }}</small><h3 class="fw-bold text-primary mb-0">{{ $stat->total }}</h3></div></div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-3">
            <div class="col-md-4"><input name="search" value="{{ request('search') }}" placeholder="Search name or email" class="form-control"></div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="teacher" @selected(request('role') === 'teacher')>Teacher</option>
                    <option value="student" @selected(request('role') === 'student')>Student</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="locked" @selected(request('status') === 'locked')>Locked</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-secondary" type="submit">Filter</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">User</th><th>Current Role</th><th>Status</th><th>Update</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="ps-3"><div class="fw-semibold">{{ $user->name }}</div><small class="text-muted">{{ $user->email }}</small></td>
                        <td><span class="text-capitalize">{{ $user->role }}</span></td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Active' : 'Locked' }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.roles.update', $user) }}" class="d-flex align-items-center gap-2">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select form-select-sm" style="width:auto">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="teacher" @selected($user->role === 'teacher')>Teacher</option>
                                    <option value="student" @selected($user->role === 'student')>Student</option>
                                </select>
                                <div class="form-check"><input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="form-check-input"><label class="form-check-label small">Active</label></div>
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center mt-4">{{ $users->links() }}</div>
@endsection
