<?php

use App\Enums\MaritalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('family_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->enum('marital_status', MaritalStatus::values());
            $table->string('spouse_name')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->integer('number_of_children')->default(0);
            $table->integer('number_of_siblings')->default(0);
            $table->timestamps();
        });

        Schema::create('education_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('required_hours');
            $table->string('course');
            $table->string('university_name');
            $table->string('university_address');
            $table->string('university_city');
            $table->string('university_province');
            $table->string('university_zip');
            $table->timestamps();
        });

        Schema::create('ojt_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('coordinator_name');
            $table->string('coordinator_email');
            $table->string('coordinator_phone');
            $table->timestamps();
        });

        Schema::create('character_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('contact_number', 20);
            $table->string('relationship');
            $table->string('position')->nullable();
            $table->string('name_of_employer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_information');
        Schema::dropIfExists('education_information');
        Schema::dropIfExists('ojt_information');
        Schema::dropIfExists('character_references');
    }
};
