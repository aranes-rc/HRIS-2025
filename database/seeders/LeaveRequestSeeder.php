<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            LeaveRequest::factory()
                ->count(3)
                ->create(['employee_id' => $employee->id]);
        }
    }
}
