@extends('admin.layout')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Reports</h1>
        <p class="text-slate-500">Live analytics from current database records.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-slate-500">Total Users</p>
            <p class="text-3xl font-bold">{{ $overview['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-slate-500">Total Topics</p>
            <p class="text-3xl font-bold">{{ $overview['total_topics'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-slate-500">Average Score</p>
            <p class="text-3xl font-bold">{{ $overview['avg_score'] }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Users by Role</h3>
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($userByRole as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 capitalize">{{ $row->role }}</td>
                        <td class="px-4 py-3">{{ $row->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-8 text-center text-slate-500">No report data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
