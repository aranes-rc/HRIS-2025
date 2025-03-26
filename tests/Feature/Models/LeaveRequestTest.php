<?php

use App\Enums\LeaveType;
use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

describe('Leave Request Model', function() {
    beforeEach(function () {
        $user = User::factory()->create(['roles' => [UserRole::ADMIN->value]]);
        $this->actingAs($user);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
    });

    it('has proper casts', function () {
        $leave = LeaveRequest::factory()->create();
        
        expect($leave->start_date)->toBeInstanceOf(Carbon::class)
            ->and($leave->shift_covered)->toBeArray()
            ->and($leave->leave_type)->toBeInstanceOf(LeaveType::class);
    });
});