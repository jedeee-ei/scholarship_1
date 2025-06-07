<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the admin panel.');
        }

        // Check if user has administrator role
        $user = Auth::user();
        if ($user->role !== 'administrator') {
            $message = "🚫 Access Denied: Administrator privileges required. You are currently logged in as a {$user->role}. Please log in with an administrator account to access the admin panel.";
            return redirect()->route('student.dashboard')->with('error', $message);
        }

        return $next($request);
    }
}
