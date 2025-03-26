<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\EducationInformation;
use App\Services\ActionLogger;

class EducationInformationObserver
{
    /**
     * Handle the EducationInformation "created" event.
     */
    public function created(EducationInformation $educationInformation): void
    {
        $employeeName = $educationInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $educationInformation->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created an education info entry with ID #{$educationInformation->id}",
            model: $educationInformation,
        );
    }

    /**
     * Handle the EducationInformation "updated" event.
     */
    public function updated(EducationInformation $educationInformation): void
    {
        $employeeName = $educationInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $educationInformation->employee_id,
            action: LogAction::UPDATE,
            description: "$employeeName updated an education info entry with ID #{$educationInformation->id}",
            model: $educationInformation,
        );
    }

    /**
     * Handle the EducationInformation "deleted" event.
     */
    public function deleted(EducationInformation $educationInformation): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $educationInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted education info of $ownerName with ID #{$educationInformation->id}",
            model: $educationInformation,
        );
    }

    /**
     * Handle the EducationInformation "restored" event.
     */
    public function restored(EducationInformation $educationInformation): void
    {
        //
    }

    /**
     * Handle the EducationInformation "force deleted" event.
     */
    public function forceDeleted(EducationInformation $educationInformation): void
    {
        //
    }
}
