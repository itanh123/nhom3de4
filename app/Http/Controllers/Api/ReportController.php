<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function overview()
    {
        return response()->json([
            'users' => [
                'total' => DB::table('users')->count(),
                'active' => DB::table('users')->where('is_active', true)->count(),
                'locked' => DB::table('users')->where('is_active', false)->count(),
            ],
            'topics' => [
                'total' => DB::table('topics')->count(),
                'root_topics' => DB::table('topics')->whereNull('parent_id')->count(),
            ],
            'questions' => [
                'total' => DB::table('questions')->count(),
                'ai_generated' => DB::table('questions')->where('ai_generated', true)->count(),
            ],
            'exams' => [
                'total' => DB::table('exams')->count(),
                'active' => DB::table('exams')->where('is_active', true)->count(),
                'results' => DB::table('exam_results')->count(),
            ],
        ]);
    }

    public function users()
    {
        return response()->json([
            'by_role' => DB::table('users')
                ->select('role', DB::raw('COUNT(*) as total'))
                ->groupBy('role')
                ->orderBy('role')
                ->get(),
            'status' => [
                'active' => DB::table('users')->where('is_active', true)->count(),
                'locked' => DB::table('users')->where('is_active', false)->count(),
            ],
        ]);
    }

    public function topics()
    {
        return response()->json([
            'by_visibility' => DB::table('topics')
                ->select('is_public', DB::raw('COUNT(*) as total'))
                ->groupBy('is_public')
                ->orderByDesc('is_public')
                ->get(),
            'most_used_in_questions' => DB::table('questions')
                ->join('topics', 'topics.id', '=', 'questions.topic_id')
                ->select('topics.id', 'topics.name', DB::raw('COUNT(questions.id) as question_count'))
                ->groupBy('topics.id', 'topics.name')
                ->orderByDesc('question_count')
                ->limit(10)
                ->get(),
        ]);
    }

    public function exams()
    {
        return response()->json([
            'summary' => [
                'total_exams' => DB::table('exams')->count(),
                'total_attempts' => DB::table('exam_results')->count(),
                'pass_rate_pct' => (float) (DB::table('exam_results')->avg(DB::raw('CASE WHEN passed = 1 THEN 100 ELSE 0 END')) ?? 0),
                'avg_score_pct' => (float) (DB::table('exam_results')->avg('score_pct') ?? 0),
            ],
            'latest_results' => DB::table('exam_results')
                ->join('exams', 'exams.id', '=', 'exam_results.exam_id')
                ->join('users', 'users.id', '=', 'exam_results.student_id')
                ->select(
                    'exam_results.id',
                    'exams.title as exam_title',
                    'users.name as student_name',
                    'exam_results.score_pct',
                    'exam_results.passed',
                    'exam_results.started_at',
                    'exam_results.submitted_at'
                )
                ->orderByDesc('exam_results.id')
                ->limit(20)
                ->get(),
        ]);
    }
}
