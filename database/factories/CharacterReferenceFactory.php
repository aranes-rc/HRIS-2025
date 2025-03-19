<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CharacterReference>
 */
class CharacterReferenceFactory extends Factory
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
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'contact_number' => $this->faker->phoneNumber(),
            'relationship' => $this->faker->sentence(1),
            'position' => $this->faker->optional()->jobTitle(),
            'name_of_employer' => $this->faker->optional()->name(),
        ];
    }
}
