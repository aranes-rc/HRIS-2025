<?php

namespace Database\Factories;

use App\Enums\MaritalStatus;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyInformation>
 */
class FamilyInformationFactory extends Factory
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
            'father_name' => $this->faker->optional()->name('male'),
            'father_occupation' => $this->faker->optional()->jobTitle(),
            'mother_name' => $this->faker->optional()->name('female'),
            'mother_occupation' => $this->faker->optional()->jobTitle(),
            'number_of_siblings' => $this->faker->randomDigit(),
            'marital_status' => $this->faker->randomElement(MaritalStatus::values()),
            'spouse_name' => $this->faker->optional()->name(),
            'spouse_occupation' => $this->faker->optional()->jobTitle(),
            'number_of_children' => $this->faker->randomDigit(),
        ];
    }
}
