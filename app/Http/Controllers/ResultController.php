<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\Topic;
use App\Services\AiService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamResult::with(['exam', 'student', 'exam.topic']);

        if (optional(Auth::user())->isTeacher()) {
            $query->whereHas('exam', function ($q) {
                $q->where('created_by', Auth::id());
            });
        }

        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->has('passed')) {
            $query->where('passed', $request->passed === '1');
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $results = $query->orderByDesc('submitted_at')->paginate(15);
        $exams = Exam::orderBy('title')->get();

        return view('results.index', compact('results', 'exams'));
    }

    public function show(ExamResult $result)
    {
        if (optional(Auth::user())->isTeacher()) {
            $exam = $result->exam;
            if ($exam && $exam->created_by !== Auth::id()) {
                abort(403, 'Bạn không có quyền xem kết quả này.');
            }
        }

        $result->load(['exam', 'student', 'examAnswers.question.answers']);

        return view('results.show', compact('result'));
    }

    public function aiEvaluate(ExamResult $result)
    {
        if (optional(Auth::user())->isTeacher()) {
            $exam = $result->exam;
            if ($exam && $exam->created_by !== Auth::id()) {
                abort(403);
            }
        }

        $aiService = new AiService();

        $examAnswers = $result->examAnswers()->with('question.answers')->get();
        $weakTopics = [];
        $strongTopics = [];

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
            if ($pct < 50) {
                $weakTopics[] = $name;
            } elseif ($pct >= 80) {
                $strongTopics[] = $name;
            }
        }

        $payload = [
            'result_id' => $result->id,
            'student_name' => $result->student?->name ?? 'N/A',
            'exam_title' => $result->exam?->title ?? '',
            'score_pct' => $result->score_pct,
            'correct_count' => $result->correct_count,
            'total_questions' => $result->total_questions,
            'passed' => $result->passed,
            'topic_name' => $result->exam?->topic?->name ?? '',
            'weak_topics' => $weakTopics,
            'strong_topics' => $strongTopics,
        ];

        $aiResult = $aiService->evaluateResult($payload);

        if (isset($aiResult['error'])) {
            return back()->withErrors(['error' => $aiResult['error']]);
        }

        $content = $aiResult['content'];
        $evaluation = $this->parseEvaluation($content);

        if (!empty($evaluation)) {
            $result->update([
                'ai_summary' => $evaluation['summary'] ?? null,
                'ai_suggestions' => json_encode([
                    'strengths' => $evaluation['strengths'] ?? [],
                    'weaknesses' => $evaluation['weaknesses'] ?? [],
                    'suggestions' => $evaluation['suggestions'] ?? [],
                ]),
            ]);
        }

        ActivityLogger::aiEvaluateResult($result->id);

        return redirect()->route('results.show', $result)->with('success', 'Đã đánh giá kết quả bằng AI!');
    }

    protected function parseEvaluation(string $content): array
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

        return [
            'summary' => $this->extractBetween($content, 'summary', ['"', ':', "\n"]) ?: substr($content, 0, 500),
            'strengths' => $this->extractList($content, ['strength', 'điểm mạnh']),
            'weaknesses' => $this->extractList($content, ['weakness', 'điểm yếu']),
            'suggestions' => $this->extractList($content, ['suggestion', 'gợi ý', 'cải thiện']),
        ];
    }

    protected function extractBetween(string $text, string $key, array $delimiters): ?string
    {
        $pattern = '/"' . $key . '"\s*:\s*"([^"]*(?:\\.[^"]*)*)"/i';
        if (preg_match($pattern, $text, $m)) {
            return stripcslashes($m[1]);
        }
        return null;
    }

    protected function extractList(string $text, array $keywords): array
    {
        $items = [];
        foreach ($keywords as $kw) {
            if (preg_match_all('/[-*]\s*(.+)/', $text, $m)) {
                foreach ($m[1] as $item) {
                    $item = trim($item);
                    if (strlen($item) > 3) {
                        $items[] = $item;
                    }
                }
            }
            if (count($items) > 0) break;
        }
        return array_unique($items);
    }

    public function aiLearningPath(ExamResult $result)
    {
        if (optional(Auth::user())->isTeacher()) {
            if ($result->exam?->created_by !== Auth::id()) {
                abort(403);
            }
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
            'student_name' => $result->student?->name ?? 'Học sinh',
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

        return redirect()->route('results.show', $result)->with('success', 'Đã tạo lộ trình học tập!');
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

        return [
            'raw' => substr($content, 0, 2000),
        ];
    }
}
