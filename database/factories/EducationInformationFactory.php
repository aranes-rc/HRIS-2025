<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EducationInformation>
 */
class EducationInformationFactory extends Factory
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
            'required_hours' => $this->faker->numberBetween(10, 500),
            'course' => $this->faker->sentence(3),
            'university_name' => $this->faker->company,
            'university_address' => $this->faker->streetAddress,
            'university_city' => $this->faker->city,
            'university_province' => $this->faker->state,
            'university_zip' => $this->faker->postcode,
        ];
    }
}
