<?php

namespace App\Http\Middleware;

use App\Models\ScholarshipApplication;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentOwnsApplication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $applicationId = $request->route('id') ?? $request->query('id') ?? $request->input('application_id');

        if ($applicationId) {
            $student = auth()->guard('web')->user();
            $application = ScholarshipApplication::where('application_id', $applicationId)->first();

            if (!$application || $application->student_id !== $student->student_id) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'You do not have permission to access this application.');
            }
        }

        return $next($request);
    }
}
