<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Enums\Department;
use App\Enums\DepartmentTeam;
use App\Enums\EmployeeStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'birthdate' => $this->faker->date(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(Gender::values()),
            'contact_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_number' => $this->faker->phoneNumber(),
            'hire_date' => $this->faker->date(),
            'employment_type' => $this->faker->randomElement(EmploymentType::values()),
            'position' => $this->faker->optional()->jobTitle(),
            'department' => $this->faker->randomElement(Department::values()),
            'status' => $this->faker->randomElement(EmployeeStatus::values()),
            'attendance_status' => $this->faker->randomElement(AttendanceStatus::cases()),
            'department_team' => $this->faker->randomElement(DepartmentTeam::cases()),
            'group_number' => $this->faker->randomDigit(),
            'date_of_start' => $this->faker->date(),
            'date_of_orientation_day' => $this->faker->date(),
            'e_signature_path' => $this->faker->imageUrl(),
        ];
    }
}
