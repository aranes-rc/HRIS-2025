<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\RequestStatus;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
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
            'document_type' => $this->faker->randomElement(DocumentType::cases()),
            'status' => $this->faker->randomElement(RequestStatus::cases()),
            'file_path' => $this->faker->filePath(),
        ];
    }
}
