<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Topic;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AiExamController extends Controller
{
    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        return view('student.exams.ai-generator', compact('topics'));
    }

    public function generate(Request $request, AiService $aiService)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'number' => 'required|integer|min:5|max:20',
            'difficulty' => ['required', Rule::in([
                Question::DIFFICULTY_EASY,
                Question::DIFFICULTY_MEDIUM,
                Question::DIFFICULTY_HARD
            ])],
            'prompt' => 'nullable|string|max:500',
        ]);

        $topic = Topic::findOrFail($validated['topic_id']);

        $payload = [
            'topic' => $topic->name,
            'number' => $validated['number'],
            'difficulty' => $validated['difficulty'],
            'type' => Question::TYPE_SINGLE_CHOICE, // Default to single choice for smooth mobile UX
            'prompt' => $validated['prompt'] ?? '',
            'document' => null,
        ];

        $result = $aiService->generateQuestions($payload);

        if (isset($result['error'])) {
            return back()->withInput()->withErrors(['error' => $result['error']]);
        }

        $questionsData = $result['content'];

        DB::beginTransaction();
        try {
            // 1. Create a personal exam
            $exam = Exam::create([
                'title' => 'Luyện tập: ' . $topic->name . ' (AI/'.strtoupper($validated['difficulty']).')',
                'description' => 'Bài thi được tạo tự động bằng AI theo yêu cầu của học sinh.',
                'topic_id' => $topic->id,
                'created_by' => Auth::id(), // Student created and owns this exam
                'duration_mins' => $validated['number'] * 2, // 2 minutes per question roughly
                'pass_mark_pct' => 50,
                'status' => Exam::STATUS_OPEN, // Ready to take
                'is_published' => false, // Private! Only creator can see it
                'is_active' => true,
            ]);

            // 2. Insert questions & answers, then map to exam
            $displayOrder = 1;

            foreach ($questionsData as $qData) {
                // Ensure there is at least one correct answer
                $hasCorrect = false;
                foreach ($qData['answers'] as $ans) {
                    if (!empty($ans['is_correct'])) {
                        $hasCorrect = true;
                        break;
                    }
                }
                
                // If AI hallucinated and gave no correct answers, skip or force the first to be correct
                if (!$hasCorrect && !empty($qData['answers'])) {
                    $qData['answers'][0]['is_correct'] = true;
                }

                $question = Question::create([
                    'topic_id' => $topic->id,
                    'created_by' => Auth::id(), // Attributed to student temporarily
                    'type' => Question::TYPE_SINGLE_CHOICE,
                    'difficulty' => $validated['difficulty'],
                    'content' => $qData['content'],
                    'ai_generated' => true,
                    'is_active' => false, // Hidden from global bank until 5-star rating
                ]);

                foreach ($qData['answers'] as $ansOrder => $answer) {
                    $question->answers()->create([
                        'option_text' => $answer['option_text'],
                        'is_correct' => !empty($answer['is_correct']),
                        'display_order' => $ansOrder + 1,
                    ]);
                }

                // Map to exam
                $exam->examQuestions()->create([
                    'question_id' => $question->id,
                    'display_order' => $displayOrder++,
                    'marks' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('student.exams.show', $exam)
                ->with('success', "Đã tạo thành công bài thi luyện tập với {$validated['number']} câu hỏi từ AI! Hãy bắt đầu thi.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Lỗi khi lưu bài thi: ' . $e->getMessage()]);
        }
    }
}
