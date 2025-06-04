<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Update the student's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'student_id' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->student_id = $request->student_id;
        $user->save();
        
        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update the student's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        
        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('student.profile')
                ->with('error', 'The current password is incorrect.')
                ->withInput();
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('student.profile')->with('success', 'Password updated successfully!');
    }
}