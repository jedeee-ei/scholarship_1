<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get BSIT course
        $bsit = Course::where('code', 'BSIT')->first();

        if (!$bsit) {
            $this->command->error('BSIT course not found. Please run CourseSeeder first.');
            return;
        }

        // BSIT 1st Year 1st Semester subjects
        $firstSemesterSubjects = [
            [
                'course_id' => $bsit->id,
                'code' => 'PED101',
                'title' => 'Wellness and Fitness',
                'units' => 2,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Physical education focusing on wellness and fitness',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GE ELE101',
                'title' => 'Walisadong Komunikasyon sa Filipino',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Effective communication in Filipino',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 105',
                'title' => 'Art Appreciation',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Understanding and appreciation of various art forms',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 106',
                'title' => 'Ethics',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Study of moral principles and ethical behavior',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'INT REL101',
                'title' => 'Revelation of God in the Old Testament',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Study of God\'s revelation in the Old Testament',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC107',
                'title' => 'Readings in Philippine History',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Study of Philippine history through various readings',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'ITE 102',
                'title' => 'Programming',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Introduction to programming concepts and languages',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'ITE 101',
                'title' => 'Introduction to Computing',
                'units' => 3,
                'year_level' => 1,
                'semester' => '1st Semester',
                'description' => 'Basic concepts of computing and computer systems',
                'prerequisites' => null,
                'is_active' => true
            ]
        ];

        // BSIT 1st Year 2nd Semester subjects
        $secondSemesterSubjects = [
            [
                'course_id' => $bsit->id,
                'code' => 'INT REL102',
                'title' => 'Revelation of God in the New Testament',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Study of God\'s revelation in the New Testament',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 104',
                'title' => 'Understanding the Self',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Understanding personal identity and self-awareness',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 103',
                'title' => 'Mathematics in the Modern World',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Application of mathematics in contemporary society',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GE ELE 102',
                'title' => 'Filipino sa Iba\'t Ibang Disiplina',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Filipino language in various disciplines',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 102',
                'title' => 'Science, Technology and Society',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Relationship between science, technology and society',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'GEC 101',
                'title' => 'Purposive Communication',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Effective communication for specific purposes',
                'prerequisites' => null,
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'ITE103',
                'title' => 'Programming 2',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Advanced programming concepts and techniques',
                'prerequisites' => ['ITE 102'],
                'is_active' => true
            ],
            [
                'course_id' => $bsit->id,
                'code' => 'ITE104',
                'title' => 'Information Management',
                'units' => 3,
                'year_level' => 1,
                'semester' => '2nd Semester',
                'description' => 'Fundamentals of information management systems',
                'prerequisites' => null,
                'is_active' => true
            ]
        ];

        // Combine all subjects
        $subjects = array_merge($firstSemesterSubjects, $secondSemesterSubjects);

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                [
                    'course_id' => $subject['course_id'],
                    'code' => $subject['code'],
                    'year_level' => $subject['year_level'],
                    'semester' => $subject['semester']
                ],
                $subject
            );
        }

        $this->command->info('BSIT 1st Year subjects (1st and 2nd Semester) seeded successfully.');
    }
}
