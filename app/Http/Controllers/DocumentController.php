<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Topic;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Document::with(['topic', 'uploader']);

        if ($user->role === 'teacher') {
            $query->where('uploaded_by', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($user->role === 'admin' && $request->filled('user_id')) {
            $query->where('uploaded_by', $request->user_id);
        }

        $documents = $query->orderByDesc('created_at')->paginate(15);
        $topics = Topic::orderBy('name')->get();

        return view('documents.index', compact('documents', 'topics'));
    }

    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        return view('documents.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'topic_id' => $request->topic_id,
            'uploaded_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'created_at' => now(),
        ]);

        ActivityLogger::uploadDocument($document);

        return redirect()->route('documents.index')->with('success', 'Tải lên tài liệu thành công!');
    }

    public function show(Document $document)
    {
        $this->authorizeAccess($document);
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->authorizeAccess($document);
        $topics = Topic::orderBy('name')->get();
        return view('documents.edit', compact('document', 'topics'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorizeAccess($document);

        $request->validate([
            'topic_id' => 'required|exists:topics,id',
        ]);

        $document->update(['topic_id' => $request->topic_id]);

        return redirect()->route('documents.index')->with('success', 'Cập nhật tài liệu thành công!');
    }

    public function destroy(Document $document)
    {
        $this->authorizeAccess($document);

        Storage::disk('public')->delete($document->file_path);
        ActivityLogger::deleteDocument($document);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Xóa tài liệu thành công!');
    }

    public function download(Document $document)
    {
        $this->authorizeAccess($document);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File không tồn tại!');
        }

        ActivityLogger::log('download_document', 'documents', $document->id, "Tải xuống tài liệu: {$document->file_name}");

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    protected function authorizeAccess(Document $document)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $document->uploaded_by !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }
    }
}
