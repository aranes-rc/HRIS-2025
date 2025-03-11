<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Enums\AttendanceType;
use App\Enums\ShiftType;
use App\Enums\WorkMode;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'updated_by' => Employee::factory(),
            'shift_type' => $this->faker->randomElement(ShiftType::cases()),
            'type' => $this->faker->randomElement(AttendanceType::cases()),
            'work_mode' => $this->faker->randomElement(WorkMode::cases()),
            'screenshot_workstation_selfie' => $this->faker->optional()->imageUrl(),
            'screenshot_cgc_chat' => $this->faker->optional()->imageUrl(),
            'screenshot_department_chat' => $this->faker->optional()->imageUrl(),
            'screenshot_team_chat' => $this->faker->optional()->imageUrl(),
            'screenshot_group_chat' => $this->faker->optional()->imageUrl(),
        ];
    }
}
