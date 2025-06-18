<?php

namespace App\Helpers;

class ScholarshipDataHelper
{
    /**
     * Get all departments with their codes and names
     */
    public static function getDepartments()
    {
        return [
            'SITE' => 'School of Information Technology and Engineering',
            'SBA' => 'School of Business Administration',
            'SHANS' => 'School of Health and Natural Sciences',
            'SHSS' => 'School of Humanities and Social Sciences',
            'STE' => 'School of Teacher Education'
        ];
    }

    /**
     * Get courses by department
     */
    public static function getCoursesByDepartment($departmentCode = null)
    {
        $courses = [
            'SITE' => [
                'BSIT' => 'Bachelor of Science in Information Technology',
                'BSCS' => 'Bachelor of Science in Computer Science',
                'BSCpE' => 'Bachelor of Science in Computer Engineering',
                'BSECE' => 'Bachelor of Science in Electronics and Communications Engineering',
                'BSEE' => 'Bachelor of Science in Electrical Engineering',
                'BSCE' => 'Bachelor of Science in Civil Engineering',
                'BSME' => 'Bachelor of Science in Mechanical Engineering'
            ],
            'SBA' => [
                'BSBA-MM' => 'Bachelor of Science in Business Administration Major in Marketing Management',
                'BSBA-FM' => 'Bachelor of Science in Business Administration Major in Financial Management',
                'BSBA-HRM' => 'Bachelor of Science in Business Administration Major in Human Resource Management',
                'BSA' => 'Bachelor of Science in Accountancy',
                'BSMA' => 'Bachelor of Science in Management Accounting',
                'BSTM' => 'Bachelor of Science in Tourism Management',
                'BSHM' => 'Bachelor of Science in Hospitality Management'
            ],
            'SHANS' => [
                'BSN' => 'Bachelor of Science in Nursing',
                'BSMT' => 'Bachelor of Science in Medical Technology',
                'BSPT' => 'Bachelor of Science in Physical Therapy',
                'BSRT' => 'Bachelor of Science in Radiologic Technology',
                'BSPsych' => 'Bachelor of Science in Psychology',
                'BSBio' => 'Bachelor of Science in Biology',
                'BSChem' => 'Bachelor of Science in Chemistry',
                'BSMath' => 'Bachelor of Science in Mathematics'
            ],
            'SHSS' => [
                'AB-Eng' => 'Bachelor of Arts in English',
                'AB-Pol' => 'Bachelor of Arts in Political Science',
                'AB-Hist' => 'Bachelor of Arts in History',
                'AB-Phil' => 'Bachelor of Arts in Philosophy',
                'BSDC' => 'Bachelor of Science in Development Communication',
                'BSS' => 'Bachelor of Science in Sociology'
            ],
            'STE' => [
                'BEEd' => 'Bachelor of Elementary Education',
                'BSEd-Eng' => 'Bachelor of Secondary Education Major in English',
                'BSEd-Math' => 'Bachelor of Secondary Education Major in Mathematics',
                'BSEd-Sci' => 'Bachelor of Secondary Education Major in Science',
                'BSEd-SS' => 'Bachelor of Secondary Education Major in Social Studies',
                'BSEd-Fil' => 'Bachelor of Secondary Education Major in Filipino',
                'BPED' => 'Bachelor of Physical Education'
            ]
        ];

        if ($departmentCode && isset($courses[$departmentCode])) {
            return $courses[$departmentCode];
        }

        return $departmentCode ? [] : $courses;
    }

    /**
     * Get all courses (flattened)
     */
    public static function getAllCourses()
    {
        $allCourses = [];
        $coursesByDept = self::getCoursesByDepartment();

        foreach ($coursesByDept as $courses) {
            $allCourses = array_merge($allCourses, $courses);
        }

        return $allCourses;
    }

    /**
     * Get department by course code
     */
    public static function getDepartmentByCourse($courseCode)
    {
        $coursesByDept = self::getCoursesByDepartment();

        foreach ($coursesByDept as $deptCode => $courses) {
            if (array_key_exists($courseCode, $courses)) {
                return $deptCode;
            }
        }

        return null;
    }

    /**
     * Get BEU strands for Basic Education Unit
     */
    public static function getBEUStrands()
    {
        return [
            'STEM' => 'Science, Technology, Engineering and Mathematics',
            'ABM' => 'Accountancy, Business and Management',
            'HUMSS' => 'Humanities and Social Sciences',
            'GAS' => 'General Academic Strand',
            'TVL-ICT' => 'Technical-Vocational-Livelihood - Information and Communications Technology',
            'TVL-HE' => 'Technical-Vocational-Livelihood - Home Economics',
            'TVL-IA' => 'Technical-Vocational-Livelihood - Industrial Arts'
        ];
    }

    /**
     * Get year levels for college
     */
    public static function getCollegeYearLevels()
    {
        return [
            '1st Year',
            '2nd Year',
            '3rd Year',
            '4th Year',
            '5th Year'
        ];
    }

    /**
     * Get grade levels for BEU
     */
    public static function getBEUGradeLevels()
    {
        return [
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
            'Grade 11',
            'Grade 12'
        ];
    }

    /**
     * Get semesters
     */
    public static function getSemesters()
    {
        return [
            '1st Semester',
            '2nd Semester',
            'Summer'
        ];
    }

    /**
     * Get scholarship types
     */
    public static function getScholarshipTypes()
    {
        return [
            'government' => 'Government Scholarship',
            'academic' => 'Academic Scholarship',
            'employees' => 'Employee Scholarship',
            'alumni' => 'Alumni Scholarship'
        ];
    }

    /**
     * Get government benefactor types
     */
    public static function getGovernmentBenefactorTypes()
    {
        return [
            'CHED' => 'Commission on Higher Education',
            'DOST' => 'Department of Science and Technology',
            'DSWD' => 'Department of Social Welfare and Development',
            'DOLE' => 'Department of Labor and Employment'
        ];
    }

    /**
     * Get academic scholarship subtypes
     */
    public static function getAcademicScholarshipSubtypes()
    {
        return [
            'PL' => "President's Lister (GWA 1.0-1.25)",
            'DL' => "Dean's Lister (GWA 1.26-1.74)"
        ];
    }

    /**
     * Get employee relationships
     */
    public static function getEmployeeRelationships()
    {
        return [
            'Son',
            'Daughter',
            'Spouse'
        ];
    }

    /**
     * Get subjects by course, year level, and semester
     */
    public static function getSubjects($course = null, $yearLevel = null, $semester = null)
    {
        $subjects = [
            // BSIT Subjects
            'BSIT' => [
                1 => [
                    '1st Semester' => [
                        ['code' => 'GE 1', 'title' => 'Understanding the Self', 'units' => 3],
                        ['code' => 'GE 2', 'title' => 'Readings in Philippine History', 'units' => 3],
                        ['code' => 'GE 3', 'title' => 'The Contemporary World', 'units' => 3],
                        ['code' => 'GE 4', 'title' => 'Mathematics in the Modern World', 'units' => 3],
                        ['code' => 'IT 111', 'title' => 'Introduction to Computing', 'units' => 3],
                        ['code' => 'IT 112', 'title' => 'Computer Programming 1', 'units' => 3],
                        ['code' => 'PE 1', 'title' => 'Physical Fitness', 'units' => 2],
                        ['code' => 'NSTP 1', 'title' => 'National Service Training Program 1', 'units' => 3]
                    ],
                    '2nd Semester' => [
                        ['code' => 'GE 5', 'title' => 'Purposive Communication', 'units' => 3],
                        ['code' => 'GE 6', 'title' => 'Art Appreciation', 'units' => 3],
                        ['code' => 'GE 7', 'title' => 'Science, Technology and Society', 'units' => 3],
                        ['code' => 'IT 121', 'title' => 'Computer Programming 2', 'units' => 3],
                        ['code' => 'IT 122', 'title' => 'Discrete Mathematics', 'units' => 3],
                        ['code' => 'IT 123', 'title' => 'Digital Logic Design', 'units' => 3],
                        ['code' => 'PE 2', 'title' => 'Rhythmic Activities', 'units' => 2],
                        ['code' => 'NSTP 2', 'title' => 'National Service Training Program 2', 'units' => 3]
                    ]
                ],
                2 => [
                    '1st Semester' => [
                        ['code' => 'GE 8', 'title' => 'Ethics', 'units' => 3],
                        ['code' => 'IT 211', 'title' => 'Data Structures and Algorithms', 'units' => 3],
                        ['code' => 'IT 212', 'title' => 'Object Oriented Programming', 'units' => 3],
                        ['code' => 'IT 213', 'title' => 'Database Management Systems', 'units' => 3],
                        ['code' => 'IT 214', 'title' => 'Computer Networks', 'units' => 3],
                        ['code' => 'IT 215', 'title' => 'Web Development 1', 'units' => 3],
                        ['code' => 'PE 3', 'title' => 'Individual/Dual Sports', 'units' => 2]
                    ],
                    '2nd Semester' => [
                        ['code' => 'GE 9', 'title' => 'Life and Works of Rizal', 'units' => 3],
                        ['code' => 'IT 221', 'title' => 'Software Engineering', 'units' => 3],
                        ['code' => 'IT 222', 'title' => 'Web Development 2', 'units' => 3],
                        ['code' => 'IT 223', 'title' => 'Human Computer Interaction', 'units' => 3],
                        ['code' => 'IT 224', 'title' => 'Information Management', 'units' => 3],
                        ['code' => 'IT 225', 'title' => 'Systems Analysis and Design', 'units' => 3],
                        ['code' => 'PE 4', 'title' => 'Team Sports', 'units' => 2]
                    ]
                ],
                3 => [
                    '1st Semester' => [
                        ['code' => 'IT 311', 'title' => 'Advanced Database Systems', 'units' => 3],
                        ['code' => 'IT 312', 'title' => 'Mobile Application Development', 'units' => 3],
                        ['code' => 'IT 313', 'title' => 'Network Security', 'units' => 3],
                        ['code' => 'IT 314', 'title' => 'IT Project Management', 'units' => 3],
                        ['code' => 'IT 315', 'title' => 'Elective 1', 'units' => 3],
                        ['code' => 'IT 316', 'title' => 'Capstone Project 1', 'units' => 3]
                    ],
                    '2nd Semester' => [
                        ['code' => 'IT 321', 'title' => 'Enterprise Systems', 'units' => 3],
                        ['code' => 'IT 322', 'title' => 'Cloud Computing', 'units' => 3],
                        ['code' => 'IT 323', 'title' => 'Data Analytics', 'units' => 3],
                        ['code' => 'IT 324', 'title' => 'Elective 2', 'units' => 3],
                        ['code' => 'IT 325', 'title' => 'Capstone Project 2', 'units' => 3],
                        ['code' => 'IT 326', 'title' => 'IT Ethics and Professional Practice', 'units' => 3]
                    ]
                ],
                4 => [
                    '1st Semester' => [
                        ['code' => 'IT 411', 'title' => 'Practicum (486 hours)', 'units' => 6]
                    ],
                    '2nd Semester' => [
                        ['code' => 'IT 421', 'title' => 'Emerging Technologies', 'units' => 3],
                        ['code' => 'IT 422', 'title' => 'IT Research and Innovation', 'units' => 3],
                        ['code' => 'IT 423', 'title' => 'Elective 3', 'units' => 3]
                    ]
                ]
            ],
            // BSCS Subjects
            'BSCS' => [
                1 => [
                    '1st Semester' => [
                        ['code' => 'GE 1', 'title' => 'Understanding the Self', 'units' => 3],
                        ['code' => 'GE 2', 'title' => 'Readings in Philippine History', 'units' => 3],
                        ['code' => 'MATH 1', 'title' => 'Calculus 1', 'units' => 3],
                        ['code' => 'CS 111', 'title' => 'Introduction to Computer Science', 'units' => 3],
                        ['code' => 'CS 112', 'title' => 'Programming Fundamentals', 'units' => 3],
                        ['code' => 'CS 113', 'title' => 'Discrete Mathematics', 'units' => 3],
                        ['code' => 'PE 1', 'title' => 'Physical Fitness', 'units' => 2],
                        ['code' => 'NSTP 1', 'title' => 'National Service Training Program 1', 'units' => 3]
                    ],
                    '2nd Semester' => [
                        ['code' => 'GE 3', 'title' => 'The Contemporary World', 'units' => 3],
                        ['code' => 'GE 4', 'title' => 'Mathematics in the Modern World', 'units' => 3],
                        ['code' => 'MATH 2', 'title' => 'Calculus 2', 'units' => 3],
                        ['code' => 'CS 121', 'title' => 'Object Oriented Programming', 'units' => 3],
                        ['code' => 'CS 122', 'title' => 'Data Structures', 'units' => 3],
                        ['code' => 'CS 123', 'title' => 'Computer Organization', 'units' => 3],
                        ['code' => 'PE 2', 'title' => 'Rhythmic Activities', 'units' => 2],
                        ['code' => 'NSTP 2', 'title' => 'National Service Training Program 2', 'units' => 3]
                    ]
                ]
                // Add more years as needed
            ],
            // BSN Subjects
            'BSN' => [
                1 => [
                    '1st Semester' => [
                        ['code' => 'GE 1', 'title' => 'Understanding the Self', 'units' => 3],
                        ['code' => 'GE 2', 'title' => 'Readings in Philippine History', 'units' => 3],
                        ['code' => 'ANAT 101', 'title' => 'Anatomy and Physiology 1', 'units' => 4],
                        ['code' => 'CHEM 101', 'title' => 'General Chemistry', 'units' => 3],
                        ['code' => 'NURS 101', 'title' => 'Fundamentals of Nursing', 'units' => 3],
                        ['code' => 'PSYC 101', 'title' => 'General Psychology', 'units' => 3],
                        ['code' => 'PE 1', 'title' => 'Physical Fitness', 'units' => 2],
                        ['code' => 'NSTP 1', 'title' => 'National Service Training Program 1', 'units' => 3]
                    ]
                ]
                // Add more years and semesters as needed
            ]
            // Add more courses as needed
        ];

        // If specific parameters are provided, return filtered results
        if ($course && $yearLevel && $semester) {
            $courseKey = $course;
            $yearNum = (int) filter_var($yearLevel, FILTER_SANITIZE_NUMBER_INT);

            if (isset($subjects[$courseKey][$yearNum][$semester])) {
                return $subjects[$courseKey][$yearNum][$semester];
            }
            return [];
        }

        // If only course is provided, return all subjects for that course
        if ($course) {
            return $subjects[$course] ?? [];
        }

        return $subjects;
    }

    /**
     * Get course duration (number of years)
     */
    public static function getCourseDuration($course = null)
    {
        $durations = [
            // SITE Courses
            'BSIT' => 4,
            'BSCS' => 4,
            'BSCpE' => 5,
            'BSECE' => 5,
            'BSEE' => 5,
            'BSCE' => 5,
            'BSME' => 5,

            // SBA Courses
            'BSBA-MM' => 4,
            'BSBA-FM' => 4,
            'BSBA-HRM' => 4,
            'BSA' => 4,
            'BSMA' => 4,
            'BSTM' => 4,
            'BSHM' => 4,

            // SHANS Courses
            'BSN' => 4,
            'BSMT' => 4,
            'BSPT' => 4,
            'BSRT' => 4,
            'BSPsych' => 4,
            'BSBio' => 4,
            'BSChem' => 4,
            'BSMath' => 4,

            // SHSS Courses
            'AB-Eng' => 4,
            'AB-Pol' => 4,
            'AB-Hist' => 4,
            'AB-Phil' => 4,
            'BSDC' => 4,
            'BSS' => 4,

            // STE Courses
            'BEEd' => 4,
            'BSEd-Eng' => 4,
            'BSEd-Math' => 4,
            'BSEd-Sci' => 4,
            'BSEd-SS' => 4,
            'BSEd-Fil' => 4,
            'BPED' => 4
        ];

        if ($course) {
            return $durations[$course] ?? 4;
        }

        return $durations;
    }
}
