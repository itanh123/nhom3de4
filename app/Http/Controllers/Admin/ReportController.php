<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $overview = [
            'total_users' => DB::table('users')->count(),
            'total_topics' => DB::table('topics')->count(),
            'total_questions' => DB::table('questions')->count(),
            'total_exams' => DB::table('exams')->count(),
            'total_results' => DB::table('exam_results')->count(),
            'avg_score' => round((float) (DB::table('exam_results')->avg('score_pct') ?? 0), 2),
        ];

        $userByRole = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderBy('role')
            ->get();

        return view('admin.report.main', compact('overview', 'userByRole'));
    }
}
