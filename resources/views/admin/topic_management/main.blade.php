@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2>Topic Management</h2><p class="text-muted mb-0">Manage root and child topics for the quiz domain tree.</p></div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Create Topic</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.topics.store') }}" class="row g-3">
            @csrf
            <div class="col-md-4"><input name="name" placeholder="Topic name" class="form-control" required></div>
            <div class="col-md-4">
                <select name="parent_id" class="form-select">
                    <option value="">No parent</option>
                    @foreach ($allTopics as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="created_by" class="form-select" required>
                    @foreach ($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->role }})</option>@endforeach
                </select>
            </div>
            <div class="col-md-8"><input name="description" placeholder="Description" class="form-control"></div>
            <div class="col-md-4 d-flex align-items-center gap-3">
                <div class="form-check"><input type="checkbox" name="is_public" value="1" checked class="form-check-input" id="topicPublic"><label for="topicPublic" class="form-check-label">Public</label></div>
                <button class="btn btn-primary" type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.topics.index') }}" class="row g-3">
            <div class="col-md-4"><input name="search" value="{{ request('search') }}" placeholder="Search topic name" class="form-control"></div>
            <div class="col-md-3">
                <select name="visibility" class="form-select">
                    <option value="">All visibility</option>
                    <option value="public" @selected(request('visibility') === 'public')>Public</option>
                    <option value="private" @selected(request('visibility') === 'private')>Private</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="creator_id" class="form-select">
                    <option value="">All creators</option>
                    @foreach ($teachers as $teacher)<option value="{{ $teacher->id }}" @selected((string) request('creator_id') === (string) $teacher->id)>{{ $teacher->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-secondary" type="submit">Filter</button>
                <a href="{{ route('admin.topics.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Name</th><th>Parent</th><th>Creator</th><th>Public</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($topics as $topic)
                    <tr>
                        <td class="ps-3 fw-medium">{{ $topic->name }}</td>
                        <td>{{ $topic->parent?->name ?? '-' }}</td>
                        <td>{{ $topic->creator?->name ?? '-' }}</td>
                        <td>@if($topic->is_public) <span class="badge bg-success">Yes</span> @else <span class="badge bg-secondary">No</span> @endif</td>
                        <td>
                            <details>
                                <summary class="btn btn-sm btn-outline-primary">Edit</summary>
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('admin.topics.update', $topic) }}" class="row g-2 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-auto"><input name="name" value="{{ $topic->name }}" class="form-control form-control-sm" required></div>
                                        <div class="col-auto">
                                            <select name="parent_id" class="form-select form-select-sm">
                                                <option value="">No parent</option>
                                                @foreach ($allTopics as $item)@if ($item->id !== $topic->id)<option value="{{ $item->id }}" @selected($topic->parent_id === $item->id)>{{ $item->name }}</option>@endif @endforeach
                                            </select>
                                        </div>
                                        <div class="col-auto"><input name="description" value="{{ $topic->description }}" class="form-control form-control-sm" placeholder="Description"></div>
                                        <div class="col-auto"><div class="form-check"><input type="checkbox" name="is_public" value="1" @checked($topic->is_public) class="form-check-input"><label class="form-check-label small">Public</label></div></div>
                                        <div class="col-auto"><button class="btn btn-sm btn-primary" type="submit">Update</button></div>
                                    </form>
                                    <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}" onsubmit="return confirm('Delete this topic?')" class="mt-2">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Delete</button></form>
                                </div>
                            </details>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No topics found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center mt-4">{{ $topics->links() }}</div>
@endsection
