<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OjtInformation>
 */
class OjtInformationFactory extends Factory
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
            'coordinator_name' => $this->faker->name(),
            'coordinator_email' => $this->faker->unique()->safeEmail(),
            'coordinator_phone' => $this->faker->phoneNumber(),
        ];
    }
}
