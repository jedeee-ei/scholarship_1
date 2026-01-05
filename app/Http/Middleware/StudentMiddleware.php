<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access student features.');
        }

        $user = Auth::user();
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Student access required.');
        }

        return $next($request);
    }
}
