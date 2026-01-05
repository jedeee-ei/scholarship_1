<?php

namespace App\Http\Controllers;

use App\Helpers\ScholarshipDataHelper;

class DataController extends Controller
{


    /**
     * Get department-course mapping
     */
    public function getDepartmentCourseMapping()
    {
        return response()->json(ScholarshipDataHelper::getCoursesByDepartment());
    }



}
