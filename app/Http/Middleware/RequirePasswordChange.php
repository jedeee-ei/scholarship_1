<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is a student who needs to change password
        if ($user && $user->isStudent() && $user->needsPasswordChange()) {
            // Allow access to password change routes and logout
            $allowedRoutes = [
                'student.profile',
                'student.change-password',
                'student.update-password',
                'logout'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('student.profile')
                    ->with('warning', 'You must change your default password before accessing other features.');
            }
        }

        return $next($request);
    }
}
