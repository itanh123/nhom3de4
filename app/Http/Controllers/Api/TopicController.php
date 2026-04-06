<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $roots = Topic::query()
            ->whereNull('parent_id')
            ->with('children.children.children')
            ->orderBy('name')
            ->get();

        return response()->json($roots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'created_by' => ['required', 'exists:users,id'],
            'parent_id' => ['nullable', 'exists:topics,id'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $creator = User::query()->findOrFail($validated['created_by']);
        if (! in_array($creator->role, ['teacher', 'admin'], true)) {
            return response()->json(['message' => 'Only teacher/admin can create topics.'], 422);
        }

        $topic = Topic::create($validated);

        return response()->json($topic->load('children'), 201);
    }

    public function show(Topic $topic)
    {
        return response()->json($topic->load('children'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:topics,id'],
            'name' => ['sometimes', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('parent_id', $validated) && $validated['parent_id'] === $topic->id) {
            return response()->json(['message' => 'Topic cannot be parent of itself.'], 422);
        }

        $topic->update($validated);

        return response()->json($topic->refresh()->load('children'));
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return response()->json(['message' => 'Topic deleted successfully.']);
    }
}
