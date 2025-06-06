<?php

namespace Database\Factories;

use App\Models\ScholarshipApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScholarshipApplication>
 */
class ScholarshipApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scholarshipTypes = ['ched', 'presidents', 'employees', 'private'];
        $statuses = ['Pending Review', 'Under Committee Review', 'Approved', 'Rejected'];

        return [
            'application_id' => 'SCH-' . strtoupper(substr(uniqid(), -6)),
            'scholarship_type' => fake()->randomElement($scholarshipTypes),
            'student_id' => '2023-' . fake()->randomNumber(5, true),
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'sex' => fake()->randomElement(['Male', 'Female']),
            'birthdate' => fake()->date(),
            'education_stage' => 'College',
            'department' => fake()->randomElement(['SITE', 'SASTE', 'SBAHM', 'SNAHS']),
            'course' => fake()->randomElement([
                'Bachelor of Science in Information Technology',
                'Bachelor of Science in Computer Science',
                'Bachelor of Science in Civil Engineering',
                'Bachelor of Science in Psychology',
                'Bachelor of Elementary Education',
                'Bachelor of Science in Business Administration',
                'Bachelor of Science in Nursing'
            ]),
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'gwa' => fake()->randomFloat(2, 1, 1.5),
            'semester' => fake()->randomElement(['1st Semester', '2nd Semester']),
            'academic_year' => '2024-2025',
            'email' => fake()->email(),
            'contact_number' => fake()->phoneNumber(),
            'status' => fake()->randomElement($statuses),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Pending Review',
        ]);
    }

    /**
     * Indicate that the application is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Approved',
        ]);
    }

    /**
     * Indicate that the application is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Rejected',
        ]);
    }
}
