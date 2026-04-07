<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Services\AiService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        $results = ExamResult::with(['exam', 'exam.topic'])
            ->where('student_id', Auth::id())
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('student.results.index', compact('results'));
    }

    public function show(ExamResult $result)
    {
        if ($result->student_id !== Auth::id() && !optional(Auth::user())->isAdmin() && !optional(Auth::user())->isTeacher()) {
            abort(403, 'Bạn không có quyền xem kết quả này.');
        }

        $result->load(['exam', 'examAnswers.question.answers']);

        return view('student.results.show', compact('result'));
    }

    public function rateAi(ExamResult $result, Request $request)
    {
        if ($result->student_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $result->update(['ai_rating' => $validated['rating']]);

        if ($validated['rating'] > 4) {
            $questionIds = $result->exam->examQuestions()->pluck('question_id');
            \App\Models\Question::whereIn('id', $questionIds)->update(['is_active' => true]);
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! ' . ($validated['rating'] > 4 ? 'Các câu hỏi này đã được đưa vào hệ thống chung.' : ''));
    }

    public function generateExplanation(ExamResult $result, Request $request)
    {
        if ($result->student_id !== Auth::id() && !optional(Auth::user())->isAdmin() && !optional(Auth::user())->isTeacher()) {
            abort(403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        $aiService = new AiService();

        $question = \App\Models\Question::with('answers')->findOrFail($request->question_id);
        $examAnswer = $result->examAnswers()->where('question_id', $question->id)->first();

        $correctAnswer = $question->answers->where('is_correct', true)->first();
        $studentAnswer = null;

        if ($examAnswer) {
            if ($examAnswer->answer_id) {
                $studentAnswer = \App\Models\Answer::find($examAnswer->answer_id);
            }
        }

        $payload = [
            'question' => $question->content,
            'correct_answer' => $correctAnswer?->option_text ?? '',
            'student_answer' => $studentAnswer?->option_text ?? ($examAnswer?->text_answer ?? ''),
            'is_correct' => $examAnswer?->is_correct ?? false,
        ];

        $aiResult = $aiService->explainAnswer($payload);

        if (isset($aiResult['error'])) {
            return back()->withErrors(['error' => $aiResult['error']]);
        }

        if ($examAnswer) {
            $examAnswer->update(['ai_explanation' => $aiResult['content']]);
        }

        ActivityLogger::aiExplainAnswer($question->id);

        return back()->with('success', 'Đã tạo giải thích bằng AI!');
    }
    public function explainDeeper(ExamResult $result, Request $request)
    {
        if ($result->student_id !== Auth::id() && !optional(Auth::user())->isAdmin() && !optional(Auth::user())->isTeacher()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        $aiService = new AiService();
        $question = \App\Models\Question::findOrFail($request->question_id);
        
        $correctAnswer = $question->answers->where('is_correct', true)->first();

        $payload = [
            'question' => $question->content,
            'correct_answer' => $correctAnswer?->option_text ?? 'N/A',
            'current_explanation' => $question->explanation,
        ];

        $aiResult = $aiService->explainDeeper($payload);

        if (isset($aiResult['error'])) {
            return response()->json(['error' => $aiResult['error']], 500);
        }

        ActivityLogger::aiExplainAnswer($question->id);

        return response()->json([
            'content' => $aiResult['content']
        ]);
    }

    public function generateLearningPath(ExamResult $result)
    {
        if ($result->student_id !== Auth::id() && !optional(Auth::user())->isAdmin() && !optional(Auth::user())->isTeacher()) {
            abort(403);
        }

        $aiService = new AiService();

        $examAnswers = $result->examAnswers()->with('question.topic')->get();
        $weakTopics = [];

        $topicStats = [];
        foreach ($examAnswers as $ea) {
            $topicName = $ea->question->topic?->name ?? 'Khác';
            if (!isset($topicStats[$topicName])) {
                $topicStats[$topicName] = ['total' => 0, 'correct' => 0];
            }
            $topicStats[$topicName]['total']++;
            if ($ea->is_correct) {
                $topicStats[$topicName]['correct']++;
            }
        }

        foreach ($topicStats as $name => $stat) {
            $pct = $stat['total'] > 0 ? ($stat['correct'] / $stat['total']) * 100 : 0;
            if ($pct < 70) {
                $weakTopics[] = $name;
            }
        }

        $payload = [
            'student_name' => Auth::user()->name,
            'score_pct' => $result->score_pct,
            'weak_topics' => $weakTopics,
            'exam_title' => $result->exam?->title ?? '',
            'topic_name' => $result->exam?->topic?->name ?? '',
        ];

        $aiResult = $aiService->generateLearningPath($payload);

        if (isset($aiResult['error'])) {
            return back()->withErrors(['error' => $aiResult['error']]);
        }

        $learningPath = $this->parseLearningPath($aiResult['content']);

        $currentSuggestions = is_string($result->ai_suggestions) ? json_decode($result->ai_suggestions, true) : ($result->ai_suggestions ?? []);
        $currentSuggestions['learning_path'] = $learningPath;
        $result->update(['ai_suggestions' => json_encode($currentSuggestions)]);

        ActivityLogger::aiGenerateLearningPath($result->id);

        return back()->with('success', 'Đã tạo lộ trình học tập!');
    }

    protected function parseLearningPath(string $content): array
    {
        $content = trim($content);

        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        return ['raw' => substr($content, 0, 2000)];
    }
}
