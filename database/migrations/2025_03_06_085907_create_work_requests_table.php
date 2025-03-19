<?php

use App\Enums\RequestStatus;
use App\Enums\ShiftRequest;
use App\Enums\WorkType;
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
        Schema::create('work_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('request_date');
            $table->enum('work_type', WorkType::values());
            $table->enum('shift_request', ShiftRequest::values());
            $table->text('reason');
            $table->enum('status', RequestStatus::values())->default(RequestStatus::PENDING->value);
            $table->string('proof_of_team_leader_approval');
            $table->string('proof_of_group_leader_approval');
            $table->string('proof_of_school_approval');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_requests');
    }
};
