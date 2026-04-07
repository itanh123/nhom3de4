<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('topic')
            ->where('is_published', true)
            ->where('status', Exam::STATUS_OPEN)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_time')
                    ->orWhere('start_time', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>=', now());
            })
            ->withCount('examQuestions')
            ->orderByDesc('created_at')
            ->get();

        $completedExams = ExamResult::where('student_id', Auth::id())
            ->pluck('exam_id')
            ->toArray();

        return view('student.exams.index', compact('exams', 'completedExams'));
    }

    public function show(Exam $exam)
    {
        if (!$exam->canTake()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Bài thi này không khả dụng.');
        }

        $alreadyTaken = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', Auth::id())
            ->exists();

        return view('student.exams.show', compact('exam', 'alreadyTaken'));
    }

    public function start(Request $request, Exam $exam)
    {
        if (!$exam->canTake()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Bài thi này không khả dụng.');
        }

        $alreadyTaken = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', Auth::id())
            ->exists();

        if ($alreadyTaken) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Bạn đã làm bài thi này.');
        }

        $startedAt = now();
        $expiresAt = $exam->duration_mins ? $startedAt->copy()->addMinutes($exam->duration_mins) : null;

        $request->session()->put('exam_' . $exam->id, [
            'started_at' => $startedAt->toDateTimeString(),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
            'answered' => [],
        ]);

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        $sessionData = Session::get('exam_' . $exam->id);

        if (!$sessionData) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Vui lòng bắt đầu bài thi trước.');
        }

        $questions = $exam->examQuestions()
            ->with(['question.answers' => function ($q) {
                $q->orderBy('display_order');
            }])
            ->orderBy('display_order')
            ->get()
            ->pluck('question');

        if ($exam->shuffle_q) {
            $questions = $questions->shuffle()->values();
        }

        if ($exam->shuffle_a) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('answers', $q->answers->shuffle()->values());
                return $q;
            });
        }

        $questionIds = $questions->pluck('id')->toArray();
        Session::put('exam_' . $exam->id . '_order', $questionIds);

        return view('student.exams.take', compact('exam', 'questions', 'sessionData'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $sessionData = Session::get('exam_' . $exam->id);

        if (!$sessionData) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Phiên làm bài không hợp lệ.');
        }

        $answers = $request->except('_token', 'exam_id');
        $questionOrder = Session::get('exam_' . $exam->id . '_order', []);

        DB::beginTransaction();
        try {
            $result = ExamResult::create([
                'exam_id' => $exam->id,
                'student_id' => Auth::id(),
                'started_at' => $sessionData['started_at'],
                'submitted_at' => now(),
                'total_questions' => 0,
                'correct_count' => 0,
                'score_pct' => 0,
                'passed' => false,
            ]);

            $totalQuestions = 0;
            $correctCount = 0;

            foreach ($questionOrder as $questionId) {
                $question = Question::with('answers')->find($questionId);
                if (!$question) continue;

                $totalQuestions++;
                $answerData = $answers['answers'][$questionId] ?? null;
                $isCorrect = false;

                if ($question->type === Question::TYPE_SINGLE_CHOICE) {
                    $selectedAnswerId = $answerData['answer_id'] ?? null;
                    $correctAnswer = $question->answers->where('is_correct', true)->first();

                    if ($selectedAnswerId && $correctAnswer && (int)$selectedAnswerId === $correctAnswer->id) {
                        $isCorrect = true;
                        $correctCount++;
                    }

                    $result->examAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => $selectedAnswerId,
                        'is_correct' => $isCorrect,
                    ]);
                } elseif ($question->type === Question::TYPE_MULTIPLE_CHOICE) {
                    $selectedIds = $answerData['answer_ids'] ?? [];
                    $correctIds = $question->answers->where('is_correct', true)->pluck('id')->toArray();
                    sort($selectedIds);
                    sort($correctIds);

                    if ($selectedIds === $correctIds && !empty($correctIds)) {
                        $isCorrect = true;
                        $correctCount++;
                    }

                    $result->examAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => $selectedIds[0] ?? null,
                        'is_correct' => $isCorrect,
                    ]);
                } elseif ($question->type === Question::TYPE_FILL_IN_BLANK) {
                    $textAnswer = trim(strtolower($answerData['text'] ?? ''));
                    $correctTexts = $question->answers->pluck('option_text')
                        ->map(fn($t) => trim(strtolower($t)))
                        ->toArray();

                    if ($textAnswer && in_array($textAnswer, $correctTexts)) {
                        $isCorrect = true;
                        $correctCount++;
                    }

                    $result->examAnswers()->create([
                        'question_id' => $questionId,
                        'text_answer' => $textAnswer,
                        'is_correct' => $isCorrect,
                    ]);
                }
            }

            $scorePct = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
            $passed = $scorePct >= $exam->pass_score;

            $result->update([
                'total_questions' => $totalQuestions,
                'correct_count' => $correctCount,
                'score_pct' => $scorePct,
                'passed' => $passed,
            ]);

            DB::commit();
            ActivityLogger::takeExam($result);

            Session::forget('exam_' . $exam->id);
            Session::forget('exam_' . $exam->id . '_order');

            return redirect()->route('student.results.show', $result)
                ->with('success', 'Bài thi đã được nộp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }
}
