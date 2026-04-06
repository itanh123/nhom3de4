@extends('admin.layout')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Topic Management</h1>
        <p class="text-slate-500">Manage root and child topics for the quiz domain tree.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-6 shadow-sm">
        <h3 class="font-bold mb-3">Create Topic</h3>
        <form method="POST" action="{{ route('admin.topics.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input name="name" placeholder="Topic name" class="rounded-xl border-slate-300 md:col-span-2" required>
            <select name="parent_id" class="rounded-xl border-slate-300">
                <option value="">No parent</option>
                @foreach ($allTopics as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
            <select name="created_by" class="rounded-xl border-slate-300" required>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->role }})</option>
                @endforeach
            </select>
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_public" value="1" checked>
                <span class="text-sm">Public</span>
            </label>
            <input name="description" placeholder="Description" class="rounded-xl border-slate-300 md:col-span-6">
            <div class="md:col-span-6">
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700" type="submit">Create</button>
            </div>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.topics.index') }}" class="mb-6 rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search topic name" class="rounded-xl border-slate-300 md:col-span-2">
            <select name="visibility" class="rounded-xl border-slate-300">
                <option value="">All visibility</option>
                <option value="public" @selected(request('visibility') === 'public')>Public</option>
                <option value="private" @selected(request('visibility') === 'private')>Private</option>
            </select>
            <select name="creator_id" class="rounded-xl border-slate-300">
                <option value="">All creators</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected((string) request('creator_id') === (string) $teacher->id)>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700" type="submit">Filter</button>
                <a href="{{ route('admin.topics.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Reset</a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Parent</th>
                <th class="px-4 py-3 text-left">Creator</th>
                <th class="px-4 py-3 text-left">Public</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($topics as $topic)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3 font-medium">{{ $topic->name }}</td>
                    <td class="px-4 py-3">{{ $topic->parent?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $topic->creator?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $topic->is_public ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3">
                        <details>
                            <summary class="cursor-pointer text-blue-700 font-medium">Edit</summary>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.topics.update', $topic) }}" class="flex flex-wrap gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input name="name" value="{{ $topic->name }}" class="rounded-xl border-slate-300" required>
                                    <select name="parent_id" class="rounded-xl border-slate-300">
                                        <option value="">No parent</option>
                                        @foreach ($allTopics as $item)
                                            @if ($item->id !== $topic->id)
                                                <option value="{{ $item->id }}" @selected($topic->parent_id === $item->id)>{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <input name="description" value="{{ $topic->description }}" class="rounded-xl border-slate-300" placeholder="Description">
                                    <label class="inline-flex items-center gap-2 px-2 text-xs text-slate-600">
                                        <input type="checkbox" name="is_public" value="1" @checked($topic->is_public)>
                                        <span>Public</span>
                                    </label>
                                    <button class="px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700" type="submit">Update</button>
                                </form>
                                <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}" onsubmit="return confirm('Delete this topic?')">
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
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No topics found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $topics->links() }}
    </div>
@endsection
