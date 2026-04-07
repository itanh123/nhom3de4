<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Topic;
use App\Models\Question;
use App\Models\ExamQuestion;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['topic', 'creator'])->withCount('examQuestions');

        // Filter by role: teacher chỉ thấy bài thi của mình
        if (optional(Auth::user())->isTeacher()) {
            $query->where('created_by', Auth::id());
        }

        // Search by title
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by topic
        if ($request->has('topic_id') && $request->topic_id) {
            $query->where('topic_id', $request->topic_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by published
        if ($request->has('is_published')) {
            $query->where('is_published', $request->is_published);
        }

        $exams = $query->orderByDesc('created_at')->paginate(15);
        $topics = Topic::orderBy('name')->get();

        return view('exams.index', compact('exams', 'topics'));
    }

    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        $questions = collect();
        
        return view('exams.create', compact('topics', 'questions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_mins' => 'nullable|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'shuffle_q' => 'boolean',
            'shuffle_a' => 'boolean',
            'show_explain' => 'boolean',
            'status' => ['required', Rule::in(Exam::statuses())],
            'is_published' => 'boolean',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'topic_id' => $validated['topic_id'],
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration_mins' => $validated['duration_mins'] ?? null,
                'pass_score' => $validated['pass_score'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'shuffle_q' => $validated['shuffle_q'] ?? false,
                'shuffle_a' => $validated['shuffle_a'] ?? false,
                'show_explain' => $validated['show_explain'] ?? false,
                'status' => $validated['status'],
                'is_published' => $validated['is_published'] ?? false,
                'is_active' => true,
            ]);

            // Save exam questions
            foreach ($request->question_ids as $index => $questionId) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'display_order' => $index + 1,
                    'point' => 1.00,
                ]);
            }

            DB::commit();
            ActivityLogger::createExam($exam);

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Bài thi đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function show(Exam $exam)
    {
        $exam->load(['topic', 'creator', 'examQuestions.question.answers' => function ($q) {
            $q->orderBy('display_order');
        }]);

        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        // Teacher chỉ sửa được bài thi của mình
        if (optional(Auth::user())->isTeacher() && $exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa bài thi này.');
        }

        $topics = Topic::orderBy('name')->get();
        
        // Get questions from the exam's topic
        $questions = Question::where('topic_id', $exam->topic_id)
            ->where('is_active', true)
            ->with(['answers' => function ($q) {
                $q->orderBy('display_order');
            }])
            ->get();

        return view('exams.edit', compact('exam', 'topics', 'questions'));
    }

    public function update(Request $request, Exam $exam)
    {
        // Teacher chỉ sửa được bài thi của mình
        if (optional(Auth::user())->isTeacher() && $exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa bài thi này.');
        }

        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_mins' => 'nullable|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'shuffle_q' => 'boolean',
            'shuffle_a' => 'boolean',
            'show_explain' => 'boolean',
            'status' => ['required', Rule::in(Exam::statuses())],
            'is_published' => 'boolean',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        DB::beginTransaction();
        try {
            $exam->update([
                'topic_id' => $validated['topic_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration_mins' => $validated['duration_mins'] ?? null,
                'pass_score' => $validated['pass_score'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'shuffle_q' => $validated['shuffle_q'] ?? false,
                'shuffle_a' => $validated['shuffle_a'] ?? false,
                'show_explain' => $validated['show_explain'] ?? false,
                'status' => $validated['status'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            // Delete existing exam questions
            $exam->examQuestions()->delete();

            // Create new exam questions
            foreach ($request->question_ids as $index => $questionId) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'display_order' => $index + 1,
                    'point' => 1.00,
                ]);
            }

            DB::commit();
            ActivityLogger::updateExam($exam);

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Bài thi đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function destroy(Exam $exam)
    {
        // Teacher chỉ xóa được bài thi của mình
        if (optional(Auth::user())->isTeacher() && $exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa bài thi này.');
        }

        ActivityLogger::deleteExam($exam);
        $exam->examQuestions()->delete();
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Bài thi đã được xóa!');
    }

    public function togglePublish(Exam $exam)
    {
        if (optional(Auth::user())->isTeacher() && $exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái bài thi này.');
        }

        $updates = ['is_published' => !$exam->is_published];

        // Nếu chuyển sang công khai và đang ở trạng thái nháp -> tự động chuyển thành Mở
        if ($updates['is_published'] && $exam->status === Exam::STATUS_DRAFT) {
            $updates['status'] = Exam::STATUS_OPEN;
        }

        $exam->update($updates);

        return back()->with('success', $exam->is_published ? 'Bài thi đã được công bố!' : 'Bài thi đã được ẩn!');
    }

    public function getQuestionsByTopic(Request $request)
    {
        $topicId = $request->topic_id;
        
        $questions = Question::where('topic_id', $topicId)
            ->where('is_active', true)
            ->with(['answers' => function ($q) {
                $q->orderBy('display_order');
            }])
            ->get();

        return response()->json($questions);
    }
}
