<?php

namespace App\Http\Controllers;

use App\Helpers\ScholarshipDataHelper;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * Get departments
     */
    public function getDepartments()
    {
        return response()->json(ScholarshipDataHelper::getDepartments());
    }

    /**
     * Get courses by department
     */
    public function getCoursesByDepartment($departmentCode)
    {
        return response()->json(ScholarshipDataHelper::getCoursesByDepartment($departmentCode));
    }

    /**
     * Get all courses
     */
    public function getAllCourses()
    {
        return response()->json(ScholarshipDataHelper::getAllCourses());
    }

    /**
     * Get course durations
     */
    public function getCourseDurations()
    {
        return response()->json(ScholarshipDataHelper::getCourseDuration());
    }

    /**
     * Get department-course mapping
     */
    public function getDepartmentCourseMapping()
    {
        return response()->json(ScholarshipDataHelper::getCoursesByDepartment());
    }

    /**
     * Get subjects for a specific course, year level, and semester
     */
    public function getSubjects($courseName, $yearLevel, $semester)
    {
        $subjects = ScholarshipDataHelper::getSubjects($courseName, $yearLevel, $semester);
        
        return response()->json([
            'subjects' => $subjects,
            'course' => $courseName,
            'year_level' => $yearLevel,
            'semester' => $semester
        ]);
    }

    /**
     * Get subjects for dashboard (simplified)
     */
    public function getSubjectsForDashboard(Request $request)
    {
        $course = $request->get('course');
        $yearLevel = $request->get('year_level');
        $semester = $request->get('semester');

        if (!$course || !$yearLevel || !$semester) {
            return response()->json(['subjects' => []]);
        }

        $subjects = ScholarshipDataHelper::getSubjects($course, $yearLevel, $semester);
        
        return response()->json(['subjects' => $subjects]);
    }

    /**
     * Get scholarship types
     */
    public function getScholarshipTypes()
    {
        return response()->json(ScholarshipDataHelper::getScholarshipTypes());
    }

    /**
     * Get government benefactor types
     */
    public function getGovernmentBenefactorTypes()
    {
        return response()->json(ScholarshipDataHelper::getGovernmentBenefactorTypes());
    }

    /**
     * Get academic scholarship subtypes
     */
    public function getAcademicScholarshipSubtypes()
    {
        return response()->json(ScholarshipDataHelper::getAcademicScholarshipSubtypes());
    }

    /**
     * Get BEU strands
     */
    public function getBEUStrands()
    {
        return response()->json(ScholarshipDataHelper::getBEUStrands());
    }

    /**
     * Get year levels for college
     */
    public function getCollegeYearLevels()
    {
        return response()->json(ScholarshipDataHelper::getCollegeYearLevels());
    }

    /**
     * Get grade levels for BEU
     */
    public function getBEUGradeLevels()
    {
        return response()->json(ScholarshipDataHelper::getBEUGradeLevels());
    }

    /**
     * Get semesters
     */
    public function getSemesters()
    {
        return response()->json(ScholarshipDataHelper::getSemesters());
    }
}
