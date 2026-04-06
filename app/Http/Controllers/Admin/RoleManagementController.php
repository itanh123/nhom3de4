<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    public function index()
    {
        $query = User::query()->select('id', 'name', 'email', 'role', 'is_active')->orderByDesc('id');

        if (request()->filled('search')) {
            $search = request()->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (request()->filled('role')) {
            $query->where('role', request()->string('role'));
        }

        if (request()->filled('status')) {
            $query->where('is_active', request()->string('status') === 'active');
        }

        $users = $query->paginate(20)->withQueryString();

        $roleStats = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->orderBy('role')
            ->get();

        return view('admin.role_management.main', compact('users', 'roleStats'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'role' => $validated['role'],
            'is_active' => (bool) ($validated['is_active'] ?? $user->is_active),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }
}
