<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\OjtInformation;
use App\Services\ActionLogger;

class OjtInformationObserver
{
    /**
     * Handle the OjtInformation "created" event.
     */
    public function created(OjtInformation $ojtInformation): void
    {
        $employeeName = $ojtInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $ojtInformation->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created an OJT info entry with ID #{$ojtInformation->id}",
            model: $ojtInformation,
        );
    }

    /**
     * Handle the OjtInformation "updated" event.
     */
    public function updated(OjtInformation $ojtInformation): void
    {
        $employeeName = $ojtInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $ojtInformation->employee_id,
            action: LogAction::UPDATE,
            description: "$employeeName updated an OJT info entry with ID #{$ojtInformation->id}",
            model: $ojtInformation,
        );
    }

    /**
     * Handle the OjtInformation "deleted" event.
     */
    public function deleted(OjtInformation $ojtInformation): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $ojtInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted OJT info of $ownerName with ID #{$ojtInformation->id}",
            model: $ojtInformation,
        );
    }

    /**
     * Handle the OjtInformation "restored" event.
     */
    public function restored(OjtInformation $ojtInformation): void
    {
        //
    }

    /**
     * Handle the OjtInformation "force deleted" event.
     */
    public function forceDeleted(OjtInformation $ojtInformation): void
    {
        //
    }
}
