<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('creator')->latest()->paginate(10);
        return view('topics.index', compact('topics'));
    }

    public function create()
    {
        return view('topics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        Topic::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('topics.index')->with('success', 'Chủ đề đã được tạo thành công.');
    }

    public function edit(Topic $topic)
    {
        return view('topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        $topic->update($validated);

        return redirect()->route('topics.index')->with('success', 'Chủ đề đã được cập nhật.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return redirect()->route('topics.index')->with('success', 'Chủ đề đã được xóa.');
    }
}
