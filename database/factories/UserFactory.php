<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'roles' => [UserRole::EMPLOYEE->value],
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user should have admin role.
     *
     * @return $this
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::ADMIN->value],
        ]);
    }

    /**
     * Indicate that the user should have HR role.
     *
     * @return $this
     */
    public function hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::HR->value],
        ]);
    }

    /**
     * Indicate that the user should have employee role.
     *
     * @return $this
     */
    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::EMPLOYEE->value],
        ]);
    }

    /**
     * Indicate that the user should have multiple roles.
     *
     * @param array $roles
     * @return $this
     */
    public function withRoles(array $roles): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => $roles,
        ]);
    }

    /**
     * Indicate that the user is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}