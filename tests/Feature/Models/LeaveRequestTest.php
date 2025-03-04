<?php

use App\Enums\LeaveType;
use App\Models\LeaveRequest;
use Carbon\Carbon;

describe('Leave Request Model', function() {
    it('has proper casts', function () {
        $leave = LeaveRequest::factory()->create();
        
        expect($leave->start_date)->toBeInstanceOf(Carbon::class)
            ->and($leave->shift_covered)->toBeArray()
            ->and($leave->leave_type)->toBeInstanceOf(LeaveType::class);
    });
});