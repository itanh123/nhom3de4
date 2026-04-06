@extends('admin.layout')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Role Management</h1>
        <p class="text-slate-500">Assign roles and activation state for existing users.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach ($roleStats as $stat)
            <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ $stat->role }}</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stat->total }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.roles.index') }}" class="mb-6 rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search name or email" class="rounded-xl border-slate-300 md:col-span-2">
            <select name="role" class="rounded-xl border-slate-300">
                <option value="">All roles</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
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
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Reset</a>
            </div>
        </div>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3">User</th>
                    <th class="text-left px-4 py-3">Current Role</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $user->role }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Active' : 'Locked' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.roles.update', $user) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="rounded-xl border-slate-300 text-sm">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="teacher" @selected($user->role === 'teacher')>Teacher</option>
                                    <option value="student" @selected($user->role === 'student')>Student</option>
                                </select>
                                <label class="inline-flex items-center gap-1 text-xs text-slate-600">
                                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active)>
                                    Active
                                </label>
                                <button type="submit" class="px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
