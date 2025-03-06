<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Enums\ShiftRequest;
use App\Enums\WorkType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkRequest>
 */
class WorkRequestFactory extends Factory
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
            'request_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'work_type' => $this->faker->randomElement(WorkType::values()),
            'shift_request' => $this->faker->randomElement(ShiftRequest::values()),
            'reason' => $this->faker->sentence,
            'status' => $this->faker->randomElement(RequestStatus::values()),
            'proof_of_team_leader_approval' => $this->faker->imageUrl(),
            'proof_of_group_leader_approval' => $this->faker->imageUrl(),
            'proof_of_school_approval' => $this->faker->imageUrl(),
        ];
    }
}
