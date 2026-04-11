<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Admin có tất cả các quyền
        if ($request->user()->role === 'admin') {
            return $next($request);
        }

        if ($request->user()->role !== $role) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
