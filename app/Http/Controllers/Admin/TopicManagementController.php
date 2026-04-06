<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class TopicManagementController extends Controller
{
    public function index()
    {
        $query = Topic::query()->with(['parent:id,name', 'creator:id,name'])->orderByDesc('id');

        if (request()->filled('search')) {
            $search = request()->string('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if (request()->filled('visibility')) {
            $query->where('is_public', request()->string('visibility') === 'public');
        }

        if (request()->filled('creator_id')) {
            $query->where('created_by', request()->integer('creator_id'));
        }

        $topics = $query->paginate(15)->withQueryString();
        $allTopics = Topic::query()->select('id', 'name')->orderBy('name')->get();
        $teachers = User::query()->whereIn('role', ['admin', 'teacher'])->select('id', 'name', 'role')->orderBy('name')->get();

        return view('admin.topic_management.main', compact('topics', 'allTopics', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'created_by' => ['required', 'exists:users,id'],
            'parent_id' => ['nullable', 'exists:topics,id'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $validated['is_public'] = (bool) ($validated['is_public'] ?? true);

        Topic::create($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:topics,id'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['parent_id']) && (int) $validated['parent_id'] === (int) $topic->id) {
            return redirect()->route('admin.topics.index')->withErrors(['parent_id' => 'A topic cannot be its own parent.']);
        }

        $validated['is_public'] = (bool) ($validated['is_public'] ?? false);
        $topic->update($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully.');
    }
}
