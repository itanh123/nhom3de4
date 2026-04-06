<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Topic;
use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_users' => User::count(),
            'total_topics' => Topic::count(),
            'total_questions' => Question::count(),
            'total_exams' => Exam::count(),
        ];

        $recentUsers = User::orderByDesc('created_at')->limit(5)->get();

        $recentTopics = Topic::with('creator')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $userByRole = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.dashboard', compact('user', 'stats', 'recentUsers', 'recentTopics', 'userByRole'));
    }
}
