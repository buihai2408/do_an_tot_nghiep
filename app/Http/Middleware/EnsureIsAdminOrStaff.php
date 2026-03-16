<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdminOrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (!$request->user()->isAdmin() && !$request->user()->isStaff())) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
