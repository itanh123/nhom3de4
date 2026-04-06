<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorizationController extends Controller
{
    private const ROLE_PERMISSIONS = [
        'admin' => [
            'users.manage',
            'users.assign_role',
            'topics.manage',
            'reports.view',
            'system.manage',
        ],
        'teacher' => [
            'topics.manage',
            'questions.manage',
            'exams.manage',
            'reports.view.class',
        ],
        'student' => [
            'exams.take',
            'results.view_self',
        ],
    ];

    public function getPermissions(Request $request)
    {
        $validated = $request->validate([
            'role' => ['nullable', Rule::in(['admin', 'teacher', 'student'])],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $role = $validated['role'] ?? null;

        if (! $role && array_key_exists('user_id', $validated)) {
            $role = User::query()->findOrFail($validated['user_id'])->role;
        }

        if (! $role && $request->user()) {
            $role = $request->user()->role;
        }

        if (! $role) {
            return response()->json(['message' => 'role hoặc user_id là bắt buộc.'], 422);
        }

        return response()->json([
            'role' => $role,
            'permissions' => self::ROLE_PERMISSIONS[$role] ?? [],
        ]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'Cập nhật vai trò thành công.',
            'user' => $user->refresh(),
            'permissions' => self::ROLE_PERMISSIONS[$user->role] ?? [],
        ]);
    }
}
