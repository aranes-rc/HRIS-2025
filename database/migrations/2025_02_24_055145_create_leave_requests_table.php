<?php

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('leave_type', LeaveType::values());
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->json('shift_covered');
            $table->string('proof_of_leader_approval');
            $table->string('proof_of_confirmed_designatory_tasks');
            $table->string('proof_of_leave')->nullable();
            $table->enum('status', LeaveStatus::values())->default(LeaveStatus::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
