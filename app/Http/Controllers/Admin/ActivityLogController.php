<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $logs = $query->orderByDesc('created_at')->paginate(30);
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::actions();
        $entityTypes = ['users', 'topics', 'questions', 'exams', 'exam_results', 'documents', 'import_histories', 'ai_configs'];

        return view('admin.activity_logs.index', compact('logs', 'users', 'actions', 'entityTypes'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity_logs.show', compact('activityLog'));
    }
}
