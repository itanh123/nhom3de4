<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Topic;
use App\Models\Document;
use App\Services\ActivityLogger;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['topic', 'creator']);

        // Filter by role: teacher chỉ thấy câu hỏi của mình
        if (optional(Auth::user())->isTeacher()) {
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
            ActivityLogger::createQuestion($question);

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
        if (optional(Auth::user())->isTeacher() && $question->created_by !== Auth::id()) {
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
        if (optional(Auth::user())->isTeacher() && $question->created_by !== Auth::id()) {
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
            ActivityLogger::updateQuestion($question);

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
        if (optional(Auth::user())->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa câu hỏi này.');
        }

        ActivityLogger::deleteQuestion($question);
        $question->answers()->delete();
        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Câu hỏi đã được xóa!');
    }

    public function toggleActive(Question $question)
    {
        if (optional(Auth::user())->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái câu hỏi này.');
        }

        $question->update(['is_active' => !$question->is_active]);

        return back()->with('success', 'Trạng thái đã được thay đổi!');
    }

    public function generateAiForm()
    {
        $topics = Topic::orderBy('name')->get();
        $documents = Document::orderBy('file_name')->get();

        return view('questions.generate-ai', compact('topics', 'documents'));
    }

    public function generateAi(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'number' => 'required|integer|min:1|max:50',
            'difficulty' => ['required', Rule::in([
                Question::DIFFICULTY_EASY,
                Question::DIFFICULTY_MEDIUM,
                Question::DIFFICULTY_HARD
            ])],
            'type' => ['required', Rule::in([
                Question::TYPE_SINGLE_CHOICE,
                Question::TYPE_MULTIPLE_CHOICE,
                Question::TYPE_FILL_IN_BLANK
            ])],
            'prompt' => 'nullable|string|max:500',
        ]);

        $topic = Topic::findOrFail($validated['topic_id']);
        $document = !empty($validated['document_id']) ? Document::find($validated['document_id']) : null;

        $aiService = new AiService();

        $documentText = '';
        if ($document) {
            if (Storage::disk('public')->exists($document->file_path)) {
                $mime = $document->mime_type ?? '';
                if (in_array($mime, ['text/plain', 'text/csv', 'text/html', 'text/markdown', 'application/json'])) {
                    $content = Storage::disk('public')->get($document->file_path);
                    $documentText = is_string($content) ? $content : '';
                } else {
                    $documentText = 'Tài liệu: ' . $document->file_name;
                }
            } else {
                $documentText = 'Tài liệu: ' . $document->file_name . ' (file không tồn tại trên server)';
            }
        }

        $payload = [
            'topic' => $topic->name,
            'number' => $validated['number'],
            'difficulty' => $validated['difficulty'],
            'type' => $validated['type'],
            'prompt' => $validated['prompt'] ?? '',
            'document' => $documentText,
        ];

        $result = $aiService->generateQuestions($payload);

        if (isset($result['error'])) {
            return back()->withInput()->withErrors(['error' => $result['error']]);
        }

        $questionsData = $result['content'];

        $request->session()->put('ai_questions_preview', [
            'questions' => $questionsData,
            'topic_id' => $validated['topic_id'],
            'difficulty' => $validated['difficulty'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('questions.generate-ai.preview')->with('ai_success', 'Đã tạo ' . count($questionsData) . ' câu hỏi!');
    }

    public function previewAiQuestions(Request $request)
    {
        $data = $request->session()->get('ai_questions_preview');

        if (!$data) {
            return redirect()->route('questions.generate-ai.form')->withErrors(['error' => 'Không có dữ liệu xem trước.']);
        }

        $topic = Topic::find($data['topic_id']);

        return view('questions.preview-ai', [
            'questions' => $data['questions'],
            'topic' => $topic,
            'difficulty' => $data['difficulty'],
            'type' => $data['type'],
        ]);
    }

    public function saveAiQuestions(Request $request)
    {
        $data = $request->session()->get('ai_questions_preview');

        if (!$data) {
            return redirect()->route('questions.generate-ai.form')->withErrors(['error' => 'Không có dữ liệu để lưu.']);
        }

        $validated = $request->validate([
            'selected_questions' => 'required|array|min:1',
            'selected_questions.*' => 'integer',
        ]);

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($data['questions'] as $index => $qData) {
                if (!in_array($index, $validated['selected_questions'])) {
                    continue;
                }

                $question = Question::create([
                    'topic_id' => $data['topic_id'],
                    'created_by' => Auth::id(),
                    'type' => $data['type'],
                    'difficulty' => $data['difficulty'],
                    'content' => $qData['content'],
                    'ai_generated' => true,
                    'is_active' => true,
                ]);

                foreach ($qData['answers'] as $order => $answer) {
                    $question->answers()->create([
                        'option_text' => $answer['option_text'],
                        'is_correct' => $answer['is_correct'] ?? false,
                        'display_order' => $order + 1,
                    ]);
                }

                $savedCount++;
            }

            DB::commit();

            ActivityLogger::aiGenerateQuestions($savedCount, $data['type']);
            $request->session()->forget('ai_questions_preview');

            return redirect()->route('questions.index')
                ->with('success', "Đã lưu {$savedCount} câu hỏi!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save AI Questions Error', ['message' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Lỗi khi lưu: ' . $e->getMessage()]);
        }
    }

    // parser functions moved to AiService

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
