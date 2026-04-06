<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['topic', 'creator']);

        // Filter by role: teacher chỉ thấy câu hỏi của mình
        if (Auth::user()->isTeacher()) {
            $query->where('created_by', Auth::id());
        }

        // Search by keyword
        if ($request->has('search') && $request->search) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        // Filter by topic
        if ($request->has('topic_id') && $request->topic_id) {
            $query->where('topic_id', $request->topic_id);
        }

        // Filter by difficulty
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $questions = $query->orderByDesc('created_at')->paginate(15);
        $topics = Topic::orderBy('name')->get();

        return view('questions.index', compact('questions', 'topics'));
    }

    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        return view('questions.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'type' => ['required', Rule::in([
                Question::TYPE_SINGLE_CHOICE,
                Question::TYPE_MULTIPLE_CHOICE,
                Question::TYPE_FILL_IN_BLANK
            ])],
            'difficulty' => ['required', Rule::in([
                Question::DIFFICULTY_EASY,
                Question::DIFFICULTY_MEDIUM,
                Question::DIFFICULTY_HARD
            ])],
            'content' => 'required|string',
            'explanation' => 'nullable|string',
            'is_active' => 'boolean',
            'answers' => 'required|array|min:2',
            'answers.*.option_text' => 'required|string',
        ]);

        // Validate answers based on question type
        $this->validateAnswers($request);

        DB::beginTransaction();
        try {
            $question = Question::create([
                'topic_id' => $validated['topic_id'],
                'created_by' => Auth::id(),
                'type' => $validated['type'],
                'difficulty' => $validated['difficulty'],
                'content' => $validated['content'],
                'explanation' => $validated['explanation'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $this->saveAnswers($question, $request);

            DB::commit();
            return redirect()->route('questions.show', $question)
                ->with('success', 'Câu hỏi đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function show(Question $question)
    {
        $question->load(['topic', 'creator', 'answers' => function ($q) {
            $q->orderBy('display_order');
        }]);

        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        // Teacher chỉ sửa được câu hỏi của mình
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa câu hỏi này.');
        }

        $topics = Topic::orderBy('name')->get();
        $question->load(['answers' => function ($q) {
            $q->orderBy('display_order');
        }]);

        return view('questions.edit', compact('question', 'topics'));
    }

    public function update(Request $request, Question $question)
    {
        // Teacher chỉ sửa được câu hỏi của mình
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa câu hỏi này.');
        }

        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'type' => ['required', Rule::in([
                Question::TYPE_SINGLE_CHOICE,
                Question::TYPE_MULTIPLE_CHOICE,
                Question::TYPE_FILL_IN_BLANK
            ])],
            'difficulty' => ['required', Rule::in([
                Question::DIFFICULTY_EASY,
                Question::DIFFICULTY_MEDIUM,
                Question::DIFFICULTY_HARD
            ])],
            'content' => 'required|string',
            'explanation' => 'nullable|string',
            'is_active' => 'boolean',
            'answers' => 'required|array|min:2',
            'answers.*.option_text' => 'required|string',
        ]);

        $this->validateAnswers($request);

        DB::beginTransaction();
        try {
            $question->update([
                'topic_id' => $validated['topic_id'],
                'type' => $validated['type'],
                'difficulty' => $validated['difficulty'],
                'content' => $validated['content'],
                'explanation' => $validated['explanation'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $this->saveAnswers($question, $request);

            DB::commit();
            return redirect()->route('questions.show', $question)
                ->with('success', 'Câu hỏi đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function destroy(Question $question)
    {
        // Teacher chỉ xóa được câu hỏi của mình
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa câu hỏi này.');
        }

        $question->answers()->delete();
        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Câu hỏi đã được xóa!');
    }

    public function toggleActive(Question $question)
    {
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái câu hỏi này.');
        }

        $question->update(['is_active' => !$question->is_active]);

        return back()->with('success', 'Trạng thái đã được thay đổi!');
    }

    private function validateAnswers(Request $request)
    {
        $type = $request->type;
        $answers = $request->answers ?? [];

        $correctCount = 0;
        foreach ($answers as $answer) {
            if (isset($answer['is_correct']) && $answer['is_correct']) {
                $correctCount++;
            }
        }

        if ($type === Question::TYPE_SINGLE_CHOICE) {
            if ($correctCount !== 1) {
                return back()->withInput()->withErrors([
                    'answers' => 'Câu hỏi một lựa chọn phải có đúng 1 đáp án đúng.'
                ])->throwResponse();
            }
        }

        if ($type === Question::TYPE_MULTIPLE_CHOICE) {
            if ($correctCount < 1) {
                return back()->withInput()->withErrors([
                    'answers' => 'Câu hỏi nhiều lựa chọn phải có ít nhất 1 đáp án đúng.'
                ])->throwResponse();
            }
        }

        // fill_in_blank: tất cả answers đều là correct
        return true;
    }

    private function saveAnswers(Question $question, Request $request)
    {
        $answers = $request->answers ?? [];
        $type = $request->type;

        // Delete existing answers
        $question->answers()->delete();

        // Create new answers
        foreach ($answers as $index => $answerData) {
            $isCorrect = ($type === Question::TYPE_FILL_IN_BLANK)
                ? true
                : (isset($answerData['is_correct']) && $answerData['is_correct']);

            $question->answers()->create([
                'option_text' => $answerData['option_text'],
                'is_correct' => $isCorrect,
                'display_order' => $index + 1,
            ]);
        }
    }
}
