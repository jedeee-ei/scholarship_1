<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;

class ScholarshipDataController extends Controller
{
    /**
     * Get all departments with their courses
     */
    public function getDepartments()
    {
        $departments = Department::active()
            ->with(['activeCourses' => function ($query) {
                $query->select('id', 'department_id', 'code', 'name', 'duration_years');
            }])
            ->select('id', 'code', 'name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Get courses for a specific department
     */
    public function getCoursesByDepartment($departmentCode)
    {
        $department = Department::where('code', $departmentCode)->first();

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        $courses = $department->activeCourses()
            ->select('id', 'code', 'name', 'duration_years')
            ->get();

        return response()->json($courses);
    }

    /**
     * Get course duration by course name
     */
    public function getCourseDuration($courseName)
    {
        $course = Course::where('name', $courseName)->first();

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return response()->json([
            'course_name' => $course->name,
            'duration_years' => $course->duration_years
        ]);
    }

    /**
     * Get subjects for a specific course, year level, and semester
     */
    public function getSubjects($courseName, $yearLevel, $semester)
    {
        $course = Course::where('name', $courseName)->first();

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $subjects = $course->getSubjectsForSemester($yearLevel, $semester);

        return response()->json([
            'course' => $course->name,
            'year_level' => $yearLevel,
            'semester' => $semester,
            'subjects' => $subjects->map(function ($subject) {
                return [
                    'code' => $subject->code,
                    'title' => $subject->title,
                    'units' => $subject->units
                ];
            })
        ]);
    }

    /**
     * Get all course durations for frontend mapping
     */
    public function getAllCourseDurations()
    {
        $courses = Course::active()
            ->select('name', 'duration_years')
            ->get();

        $courseDurations = [];
        foreach ($courses as $course) {
            $courseDurations[$course->name] = $course->duration_years;
        }

        return response()->json($courseDurations);
    }

    /**
     * Get department-course mapping for frontend
     */
    public function getDepartmentCourseMapping()
    {
        $departments = Department::active()
            ->with(['activeCourses' => function ($query) {
                $query->select('id', 'department_id', 'name');
            }])
            ->select('id', 'code', 'name')
            ->get();

        $mapping = [];
        foreach ($departments as $department) {
            $mapping[$department->code] = $department->activeCourses->pluck('name')->toArray();
        }

        return response()->json($mapping);
    }
}
