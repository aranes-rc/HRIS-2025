<?php

use App\Enums\AttendanceStatus;
use App\Enums\Department;
use App\Enums\DepartmentTeam;
use App\Enums\EmployeeStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('birthdate');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', Gender::values());
            $table->string('contact_number', 20);
            $table->text('address');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number', 20);
            $table->date('hire_date');
            $table->enum('employment_type', EmploymentType::values());
            $table->enum('department', Department::values());
            $table->enum('status', EmployeeStatus::values())->default(EmployeeStatus::ACTIVE->value);
            $table->enum('attendance_status', AttendanceStatus::values());
            $table->enum('department_team', DepartmentTeam::values());
            $table->integer('group_number')->nullable();
            $table->date('date_of_start');
            $table->date('date_of_orientation_day');
            $table->string('e_signature_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
