<?php

namespace App\Observers;

use App\Enums\AttendanceType;
use App\Enums\LogAction;
use App\Enums\ShiftType;
use App\Models\Attendance;
use App\Services\ActionLogger;

class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        $employeeName = $attendance->employee->getFullName();
        $attendanceType = AttendanceType::getLabel($attendance->type);
        $shiftType = ShiftType::getLabel($attendance->shiftType);

        ActionLogger::log(
            employeeId: $attendance->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created attendance #{$attendance->id} for $attendanceType $shiftType",
            model: $attendance,
        );
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function updated(Attendance $attendance): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $attendance->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::UPDATE,
            description: "$updaterName updated an attendance of $ownerName with ID #{$attendance->id}",
            model: $attendance,
        );
    }

    /**
     * Handle the Attendance "deleted" event.
     */
    public function deleted(Attendance $attendance): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $attendance->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted an attendance of $ownerName with ID #{$attendance->id}",
            model: $attendance,
        );
    }

    /**
     * Handle the Attendance "restored" event.
     */
    public function restored(Attendance $attendance): void
    {
        //
    }

    /**
     * Handle the Attendance "force deleted" event.
     */
    public function forceDeleted(Attendance $attendance): void
    {
        //
    }
}
