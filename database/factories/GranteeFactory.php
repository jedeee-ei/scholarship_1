<?php

namespace Database\Factories;

use App\Models\Grantee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grantee>
 */
class GranteeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Grantee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scholarshipTypes = ['government', 'academic', 'employees', 'private'];
        $scholarshipType = fake()->randomElement($scholarshipTypes);

        return [
            'grantee_id' => 'GRT-' . strtoupper(substr(uniqid(), -6)),
            'application_id' => 'SCH-' . strtoupper(substr(uniqid(), -6)),
            'scholarship_type' => $scholarshipType,
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
                'Bachelor of Science in Accountancy',
                'Bachelor of Science in Nursing',
            ]),
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'gwa' => fake()->randomFloat(2, 1.0, 3.0),
            'semester' => fake()->randomElement(['1st Semester', '2nd Semester']),
            'academic_year' => '2023-2024',
            'contact_number' => '09' . fake()->randomNumber(9, true),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'approved_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'approved_by' => 'Admin User',
            'status' => fake()->randomElement(['Active', 'Inactive', 'Graduated']),
            'scholarship_start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'scholarship_end_date' => fake()->optional()->dateTimeBetween('now', '+4 years'),
            'scholarship_amount' => fake()->optional()->randomFloat(2, 5000, 50000),
            'is_renewable' => fake()->boolean(70), // 70% chance of being renewable
            'renewal_count' => fake()->numberBetween(0, 3),
            'current_gwa' => fake()->randomFloat(2, 1.0, 3.0),
        ];
    }

    /**
     * Indicate that the grantee is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Active',
        ]);
    }

    /**
     * Indicate that the grantee is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Inactive',
        ]);
    }

    /**
     * Indicate that the grantee has graduated.
     */
    public function graduated(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Graduated',
            'scholarship_end_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the scholarship is renewable.
     */
    public function renewable(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_renewable' => true,
            'next_renewal_date' => fake()->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Create a Government scholarship grantee.
     */
    public function government(): static
    {
        return $this->state(fn(array $attributes) => [
            'scholarship_type' => 'government',
            'father_last_name' => fake()->lastName(),
            'father_first_name' => fake()->firstName(),
            'father_middle_name' => fake()->optional()->lastName(),
            'mother_last_name' => fake()->lastName(),
            'mother_first_name' => fake()->firstName(),
            'mother_middle_name' => fake()->optional()->lastName(),
            'street' => fake()->streetAddress(),
            'barangay' => 'Brgy. ' . fake()->word(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'zipcode' => fake()->postcode(),
            'is_renewable' => true,
        ]);
    }

    /**
     * Create an employee scholarship grantee.
     */
    public function employee(): static
    {
        return $this->state(fn(array $attributes) => [
            'scholarship_type' => 'employees',
            'employee_name' => fake()->name(),
            'employee_relationship' => fake()->randomElement(['Son', 'Daughter', 'Spouse']),
            'employee_department' => fake()->randomElement(['SITE', 'SASTE', 'SBAHM', 'SNAHS']),
            'employee_position' => fake()->jobTitle(),
            'is_renewable' => false,
        ]);
    }

    /**
     * Create a private scholarship grantee.
     */
    public function private(): static
    {
        return $this->state(fn(array $attributes) => [
            'scholarship_type' => 'private',
            'scholarship_name' => fake()->company() . ' Scholarship',
            'other_scholarship' => fake()->paragraph(),
            'is_renewable' => fake()->boolean(30), // 30% chance for private scholarships
        ]);
    }
}
