<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('layouts.login');
    }

    public function showLoginFormByType(Request $request, $type)
    {
        if (!in_array($type, ['administrator', 'student'])) {
            return redirect()->route('login');
        }

        return view('layouts.login-form', ['type' => $type]);
    }

    public function login(Request $request)
    {
        // Validate the form data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Log attempt for debugging
        Log::info('Login attempt', ['email' => $request->email]);

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // Regenerate the session
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();
            Log::info('User authenticated', ['user' => $user->id, 'role' => $user->role]);

            // Redirect based on user role
            if ($user->role === 'administrator') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        // Authentication failed
        Log::warning('Authentication failed', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }
}


