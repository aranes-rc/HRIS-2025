<?php

namespace Database\Factories;

use App\Enums\AttendanceType;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\ShiftType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
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
            'leave_type' => $this->faker->randomElement(LeaveType::cases()),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 days', '+1 month'),
            'reason' => $this->faker->paragraph,
            'shift_covered' => $this->faker->randomElements(ShiftType::values(), 1),
            'status' => $this->faker->randomElement(LeaveStatus::cases()),
            'proof_of_leader_approval' => $this->faker->imageUrl(),
            'proof_of_confirmed_designatory_tasks' => $this->faker->imageUrl(),
            'proof_of_leave' => $this->faker->optional()->imageUrl(),
        ];
    }
}
