<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        $userRoleName = $user->role ? $user->role->name : null;

        if (! $userRoleName || ! in_array($userRoleName, $roles)) {
            abort(403, 'Unauthorized: Insufficient role permissions.');
        }

        return $next($request);
    }
}
