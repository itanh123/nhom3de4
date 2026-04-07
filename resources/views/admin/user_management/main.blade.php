@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2>User Management</h2><p class="text-muted mb-0">Create, update, lock and delete system users (except admin).</p></div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Create New User</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}" class="row g-3">
            @csrf
            <div class="col-md-4"><input name="name" placeholder="Name" class="form-control" required></div>
            <div class="col-md-4"><input name="email" type="email" placeholder="Email" class="form-control" required></div>
            <div class="col-md-2"><input name="password" type="password" placeholder="Password" class="form-control" required></div>
            <div class="col-md-2">
                <select name="role" class="form-select" required>
                    <option value="teacher">Teacher</option>
                    <option value="student" selected>Student</option>
                </select>
            </div>
            <div class="col-12 d-flex align-items-center gap-3">
                <div class="form-check"><input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="newUserActive"><label for="newUserActive" class="form-check-label">Active</label></div>
                <button class="btn btn-primary" type="submit">Create User</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-4"><input name="search" value="{{ request('search') }}" placeholder="Search name or email" class="form-control"></div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All roles</option>
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
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="ps-3 fw-medium">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td><span class="badge {{ $user->role === 'admin' ? 'bg-info' : ($user->role === 'teacher' ? 'bg-primary' : 'bg-success') }}">{{ ucfirst($user->role) }}</span></td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Active' : 'Locked' }}</span></td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <details>
                                <summary class="btn btn-sm btn-outline-primary">Edit</summary>
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="row g-2 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-auto"><input name="name" value="{{ $user->name }}" class="form-control form-control-sm" required></div>
                                        <div class="col-auto"><input name="email" type="email" value="{{ $user->email }}" class="form-control form-control-sm" required></div>
                                        <div class="col-auto"><input name="password" type="password" placeholder="New password" class="form-control form-control-sm"></div>
                                        <div class="col-auto">
                                            <select name="role" class="form-select form-select-sm">
                                                <option value="teacher" @selected($user->role === 'teacher')>Teacher</option>
                                                <option value="student" @selected($user->role === 'student')>Student</option>
                                            </select>
                                        </div>
                                        <div class="col-auto"><div class="form-check"><input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="form-check-input"><label class="form-check-label small">Active</label></div></div>
                                        <div class="col-auto"><button class="btn btn-sm btn-primary" type="submit">Update</button></div>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure?')" class="mt-2">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center mt-4">{{ $users->links() }}</div>
@endsection
