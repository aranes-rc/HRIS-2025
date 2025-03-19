<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\EducationInformation;
use App\Models\Employee;
use App\Models\FamilyInformation;
use App\Models\OjtInformation;
use App\Models\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $employee = Employee::factory()->create(['user_id' => $user->id]);
            FamilyInformation::factory()->create(['employee_id' => $employee->id]);
            EducationInformation::factory()->create(['employee_id' => $employee->id]);
            OjtInformation::factory()->create(['employee_id' => $employee->id]);
        }
    }
}
