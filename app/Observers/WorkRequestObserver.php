<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Enums\WorkType;
use App\Models\WorkRequest;
use App\Services\ActionLogger;

class WorkRequestObserver
{
    /**
     * Handle the WorkRequest "created" event.
     */
    public function created(WorkRequest $workRequest): void
    {
        $workType = WorkType::getLabel($workRequest->work_type);
        $employeeName = $workRequest->employee->getFullName();

        ActionLogger::log(
            employeeId: $workRequest->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created $workType work request with ID #{$workRequest->id}",
            model: $workRequest,
        );
    }

    /**
     * Handle the WorkRequest "updated" event.
     */
    public function updated(WorkRequest $workRequest): void
    {
        $workType = WorkType::getLabel($workRequest->work_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $workRequest->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::UPDATE,
            description: "$updaterName updated $workType work request of $ownerName with ID #{$workRequest->id}",
            model: $workRequest,
        );
    }

    /**
     * Handle the WorkRequest "deleted" event.
     */
    public function deleted(WorkRequest $workRequest): void
    {
        $workType = WorkType::getLabel($workRequest->work_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $workRequest->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted $workType work request of $ownerName with ID #{$workRequest->id}",
            model: $workRequest,
        );
    }

    /**
     * Handle the WorkRequest "restored" event.
     */
    public function restored(WorkRequest $workRequest): void
    {
        //
    }

    /**
     * Handle the WorkRequest "force deleted" event.
     */
    public function forceDeleted(WorkRequest $workRequest): void
    {
        //
    }
}
