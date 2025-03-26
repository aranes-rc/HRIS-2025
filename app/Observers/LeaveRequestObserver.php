<?php

namespace App\Observers;

use App\Enums\LeaveType;
use App\Enums\LogAction;
use App\Models\LeaveRequest;
use App\Services\ActionLogger;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "created" event.
     */
    public function created(LeaveRequest $leaveRequest): void
    {
        $employeeName = $leaveRequest->employee->getFullName();
        $leaveType = LeaveType::getLabel($leaveRequest->leave_type);

        ActionLogger::log(
            employeeId: $leaveRequest->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created $leaveType leave request entry with ID #{$leaveRequest->id}",
            model: $leaveRequest,
        );
    }

    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        $leaveType = LeaveType::getLabel($leaveRequest->leave_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $leaveRequest->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::UPDATE,
            description: "$updaterName updated $leaveType leave request of $ownerName with ID #{$leaveRequest->id}",
            model: $leaveRequest,
        );
    }

    /**
     * Handle the LeaveRequest "deleted" event.
     */
    public function deleted(LeaveRequest $leaveRequest): void
    {
        $leaveType = LeaveType::getLabel($leaveRequest->leave_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $leaveRequest->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted $leaveType leave request of $ownerName with ID #{$leaveRequest->id}",
            model: $leaveRequest,
        );
    }

    /**
     * Handle the LeaveRequest "restored" event.
     */
    public function restored(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "force deleted" event.
     */
    public function forceDeleted(LeaveRequest $leaveRequest): void
    {
        //
    }
}
