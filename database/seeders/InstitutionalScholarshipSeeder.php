<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;

class InstitutionalScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Departments
        $departments = [
            [
                'code' => 'SITE',
                'name' => 'School of Information Technology and Engineering',
                'description' => 'Offers programs in Information Technology, Engineering, and related fields'
            ],
            [
                'code' => 'SASTE',
                'name' => 'School of Arts, Sciences and Teacher Education',
                'description' => 'Offers programs in Liberal Arts, Sciences, and Education'
            ],
            [
                'code' => 'SBAHM',
                'name' => 'School of Business Administration and Hospitality Management',
                'description' => 'Offers programs in Business, Accounting, and Hospitality Management'
            ],
            [
                'code' => 'SNAHS',
                'name' => 'School of Nursing and Allied Health Sciences',
                'description' => 'Offers programs in Nursing and Health Sciences'
            ]
        ];

        foreach ($departments as $deptData) {
            Department::create($deptData);
        }

        // Get department IDs
        $site = Department::where('code', 'SITE')->first();
        $saste = Department::where('code', 'SASTE')->first();
        $sbahm = Department::where('code', 'SBAHM')->first();
        $snahs = Department::where('code', 'SNAHS')->first();

        // Create Courses
        $courses = [
            // SITE Department
            [
                'department_id' => $site->id,
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'duration_years' => 4
            ],
            [
                'department_id' => $site->id,
                'code' => 'BLIS',
                'name' => 'Bachelor of Library and Information Science',
                'duration_years' => 4
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSCE',
                'name' => 'Bachelor of Science in Civil Engineering',
                'duration_years' => 5
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSENSE',
                'name' => 'Bachelor of Science in Environmental and Sanitary Engineering',
                'duration_years' => 4
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSCpE',
                'name' => 'Bachelor of Science in Computer Engineering',
                'duration_years' => 4
            ],
            // SASTE Department
            [
                'department_id' => $saste->id,
                'code' => 'BAELS',
                'name' => 'Bachelor of Arts in English Language Studies',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSPsych',
                'name' => 'Bachelor of Science in Psychology',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSBio',
                'name' => 'Bachelor of Science in Biology',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSSW',
                'name' => 'Bachelor of Science in Social Work',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSPA',
                'name' => 'Bachelor of Science in Public Administration',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSBioMicro',
                'name' => 'Bachelor of Science in Biology Major in Microbiology',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSEd',
                'name' => 'Bachelor of Secondary Education',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BEEd',
                'name' => 'Bachelor of Elementary Education',
                'duration_years' => 4
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BPEd',
                'name' => 'Bachelor of Physical Education',
                'duration_years' => 4
            ],
            // SBAHM Department
            [
                'department_id' => $sbahm->id,
                'code' => 'BSA',
                'name' => 'Bachelor of Science in Accountancy',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSEntrep',
                'name' => 'Bachelor of Science in Entrepreneurship',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSBA',
                'name' => 'Bachelor of Science in Business Administration major in: Marketing Management, Financial Management and Operations Management',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSMA',
                'name' => 'Bachelor of Science in Management Accounting',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSHM',
                'name' => 'Bachelor of Science in Hospitality Management',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSTM',
                'name' => 'Bachelor of Science in Tourism Management',
                'duration_years' => 4
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSPDMI',
                'name' => 'Bachelor of Science in Product Design and Marketing Innovation',
                'duration_years' => 4
            ],
            // SNAHS Department
            [
                'department_id' => $snahs->id,
                'code' => 'BSN',
                'name' => 'Bachelor of Science in Nursing',
                'duration_years' => 5
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSPharm',
                'name' => 'Bachelor of Science in Pharmacy',
                'duration_years' => 5
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSMT',
                'name' => 'Bachelor of Science in Medical Technology',
                'duration_years' => 4
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSPT',
                'name' => 'Bachelor of Science in Physical Therapy',
                'duration_years' => 5
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSRT',
                'name' => 'Bachelor of Science in Radiologic Technology',
                'duration_years' => 4
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSM',
                'name' => 'Bachelor of Science in Midwifery',
                'duration_years' => 4
            ]
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        // Get BSIT course for subjects
        $bsit = Course::where('code', 'BSIT')->first();

        // Create BSIT Subjects
        $this->createBSITSubjects($bsit);
    }

    private function createBSITSubjects($course)
    {
        $subjects = [
            // 1st Year 1st Semester
            ['code' => 'PED101', 'title' => 'Wellness and Fitness', 'units' => 2, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'GE ELE101', 'title' => 'Kontekstwalisasaong Komunikasyon sa Filipino', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'GEC 105', 'title' => 'Art Appreciation', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'GEC 106', 'title' => 'Ethics', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'INT REL101', 'title' => 'Revelation of God in the Old Testament', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'GEC107', 'title' => 'Readings in Philippine History', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'ITE 102', 'title' => 'Programming', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],
            ['code' => 'ITE 101', 'title' => 'Introduction to Computing', 'units' => 3, 'year_level' => 1, 'semester' => '1st Semester'],

            // 1st Year 2nd Semester
            ['code' => 'INT REL102', 'title' => 'Revelation of God in the New Testament', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'GEC 104', 'title' => 'Understanding the Self', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'GEC 103', 'title' => 'Mathematics in the Modern World', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'GE ELE 102', 'title' => 'Filipino sa Iba\'t Ibang Disiplina', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'GEC 102', 'title' => 'Science, Technology and Society', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'GEC 101', 'title' => 'Purposive Communication', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'ITE103', 'title' => 'Programming 2', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],
            ['code' => 'ITE104', 'title' => 'Information Management', 'units' => 3, 'year_level' => 1, 'semester' => '2nd Semester'],

            // 2nd Year 1st Semester
            ['code' => 'INT REL103', 'title' => 'The Church', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'GEC 108', 'title' => 'Contemporary World', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'ITE 106', 'title' => 'Data Structures and Algorithm', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'ITE114', 'title' => 'Free Elective 1 (Accounting Process)', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'ITE 107', 'title' => 'Object Oriented Programming', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'ITE 108', 'title' => 'Web Systems and Technologies', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'ITE 109', 'title' => 'Advanced Database System', 'units' => 3, 'year_level' => 2, 'semester' => '1st Semester'],
            ['code' => 'PED 104', 'title' => 'Physical Activities Towards Health and Fitness 2', 'units' => 2, 'year_level' => 2, 'semester' => '1st Semester'],

            // 2nd Year 2nd Semester
            ['code' => 'INT REL104', 'title' => 'Sacraments & Liturgy', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'GEC 109', 'title' => 'Life and Works of Rizal', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'ITE 110', 'title' => 'Rich Media Development', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'PED 103', 'title' => 'Physical Activities Towards Health and Fitness 1', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'ITE 111', 'title' => 'Application Development and Emerging Technologies', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'ITE 112', 'title' => 'Quantitative Methods', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'ITE 113', 'title' => 'Human Computer Interaction', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],
            ['code' => 'LIT101', 'title' => 'Panitikang Panlipunan', 'units' => 3, 'year_level' => 2, 'semester' => '2nd Semester'],

            // 3rd Year 1st Semester
            ['code' => 'ITE 201', 'title' => 'Systems Analysis and Design', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 202', 'title' => 'Network Administration', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 203', 'title' => 'Software Engineering', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 204', 'title' => 'Mobile Application Development', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 205', 'title' => 'Database Administration', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'GEC 110', 'title' => 'The Entrepreneurial Mind', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 206', 'title' => 'IT Project Management', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],
            ['code' => 'ITE 207', 'title' => 'Cybersecurity Fundamentals', 'units' => 3, 'year_level' => 3, 'semester' => '1st Semester'],

            // 3rd Year 2nd Semester
            ['code' => 'ITE 208', 'title' => 'Web Application Development', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 209', 'title' => 'Data Analytics and Visualization', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 210', 'title' => 'Cloud Computing', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 211', 'title' => 'Artificial Intelligence', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 212', 'title' => 'IT Service Management', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 213', 'title' => 'Digital Forensics', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 214', 'title' => 'Enterprise Resource Planning', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],
            ['code' => 'ITE 215', 'title' => 'IT Ethics and Professional Practice', 'units' => 3, 'year_level' => 3, 'semester' => '2nd Semester'],

            // 4th Year 1st Semester
            ['code' => 'ITE 301', 'title' => 'Capstone Project 1', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 302', 'title' => 'Machine Learning', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 303', 'title' => 'DevOps and Automation', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 304', 'title' => 'Blockchain Technology', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 305', 'title' => 'Internet of Things (IoT)', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 306', 'title' => 'Advanced Cybersecurity', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 307', 'title' => 'IT Governance and Compliance', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],
            ['code' => 'ITE 308', 'title' => 'Emerging Technologies', 'units' => 3, 'year_level' => 4, 'semester' => '1st Semester'],

            // 4th Year 2nd Semester
            ['code' => 'ITE 309', 'title' => 'Capstone Project 2', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 310', 'title' => 'IT Internship/Practicum', 'units' => 6, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 311', 'title' => 'Business Intelligence', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 312', 'title' => 'Advanced Database Systems', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 313', 'title' => 'IT Audit and Risk Management', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 314', 'title' => 'Digital Transformation', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
            ['code' => 'ITE 315', 'title' => 'IT Leadership and Management', 'units' => 3, 'year_level' => 4, 'semester' => '2nd Semester'],
        ];

        foreach ($subjects as $subjectData) {
            $subjectData['course_id'] = $course->id;
            Subject::create($subjectData);
        }
    }
}
