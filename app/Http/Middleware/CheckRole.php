<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Check if user has any of the required roles
        if (!in_array($userRole, $roles)) {
            // Redirect to appropriate dashboard or show forbidden
            if (in_array($userRole, ['admin', 'super_admin', 'head_hr'])) {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'hr') {
                return redirect()->route('hr.dashboard');
            } else {
                return redirect()->route('user.home');
            }
        }

        return $next($request);
    }
}
