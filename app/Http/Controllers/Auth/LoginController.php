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
        $userType = $request->input('user_type', 'student');

        // Different validation rules based on user type
        if ($userType === 'administrator') {
            // Admin login with email
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $loginField = 'email';
            $loginValue = $request->email;

            Log::info('Admin login attempt', ['email' => $request->email]);
        } else {
            // Student login with student ID
            $request->validate([
                'student_id' => 'required|string',
                'password' => 'required',
            ]);

            $credentials = [
                'student_id' => $request->student_id,
                'password' => $request->password,
                'role' => 'student' // Ensure we only authenticate students
            ];

            $loginField = 'student_id';
            $loginValue = $request->student_id;

            Log::info('Student login attempt', ['student_id' => $request->student_id]);
        }

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
        Log::warning('Authentication failed', [$loginField => $loginValue]);

        $errorField = $userType === 'administrator' ? 'email' : 'student_id';
        $errorMessage = $userType === 'administrator'
            ? 'The provided email and password do not match our records.'
            : 'The provided Student ID and password do not match our records.';

        return back()->withErrors([
            $errorField => $errorMessage,
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


