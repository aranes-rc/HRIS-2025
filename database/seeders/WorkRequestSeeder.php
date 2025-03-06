<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            WorkRequest::factory()
                ->count(3)
                ->create(['employee_id' => $employee->id]);
        }
    }
}
