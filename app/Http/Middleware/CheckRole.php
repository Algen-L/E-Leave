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

        $user = auth()->user();

        // Check if user has any of the required roles
        $hasRequiredRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRequiredRole = true;
                break;
            }
        }

        if (!$hasRequiredRole) {
            // Redirect to appropriate dashboard or show forbidden
            if ($user->hasRole(['admin', 'super_admin', 'head_hr'])) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('hr')) {
                return redirect()->route('hr.dashboard');
            } else {
                return redirect()->route('user.home');
            }
        }

        return $next($request);
    }
}
