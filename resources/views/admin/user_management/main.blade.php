@extends('admin.layout')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">User Management</h1>
            <p class="text-slate-500">Create, update, lock and delete system users (except admin).</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 mb-6">
        <h2 class="font-bold text-slate-800 mb-3">Create New User</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input name="name" placeholder="Name" class="rounded-xl border-slate-300 md:col-span-2" required>
            <input name="email" type="email" placeholder="Email" class="rounded-xl border-slate-300 md:col-span-2" required>
            <input name="password" type="password" placeholder="Password" class="rounded-xl border-slate-300" required>
            <select name="role" class="rounded-xl border-slate-300" required>
                <option value="teacher">Teacher</option>
                <option value="student" selected>Student</option>
            </select>
            <label class="inline-flex items-center gap-2 md:col-span-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" checked>
                Active
            </label>
            <div class="md:col-span-6">
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700" type="submit">Create User</button>
            </div>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search name or email" class="rounded-xl border-slate-300 md:col-span-2">
            <select name="role" class="rounded-xl border-slate-300">
                <option value="">All roles</option>
                <option value="teacher" @selected(request('role') === 'teacher')>Teacher</option>
                <option value="student" @selected(request('role') === 'student')>Student</option>
            </select>
            <select name="status" class="rounded-xl border-slate-300">
                <option value="">All status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="locked" @selected(request('status') === 'locked')>Locked</option>
            </select>
            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700" type="submit">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Reset</a>
            </div>
        </div>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3">Name</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Role</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Created</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $user->role === 'teacher' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'student' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Active' : 'Locked' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <details>
                                <summary class="cursor-pointer text-blue-700 font-medium">Edit</summary>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ $user->name }}" class="rounded-xl border-slate-300" required>
                                        <input name="email" type="email" value="{{ $user->email }}" class="rounded-xl border-slate-300" required>
                                        <input name="password" type="password" placeholder="New password" class="rounded-xl border-slate-300">
                                        <select name="role" class="rounded-xl border-slate-300">
                                            <option value="teacher" @selected($user->role === 'teacher')>Teacher</option>
                                            <option value="student" @selected($user->role === 'student')>Student</option>
                                        </select>
                                        <label class="inline-flex items-center gap-1 px-2 text-xs text-slate-600">
                                            <input type="checkbox" name="is_active" value="1" @checked($user->is_active)>
                                            Active
                                        </label>
                                        <button class="px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700" type="submit">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-xl bg-red-600 text-white text-xs font-semibold hover:bg-red-700" type="submit">Delete</button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
