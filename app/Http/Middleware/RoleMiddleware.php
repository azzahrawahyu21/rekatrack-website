<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        $userRole = strtolower($user->role->name ?? '');

        $normalizedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

        if (!in_array($userRole, $normalizedRoles)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
