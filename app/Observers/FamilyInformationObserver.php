<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\FamilyInformation;
use App\Services\ActionLogger;

class FamilyInformationObserver
{
    /**
     * Handle the FamilyInformation "created" event.
     */
    public function created(FamilyInformation $familyInformation): void
    {
        $employeeName = $familyInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $familyInformation->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created an family info entry with ID #{$familyInformation->id}",
            model: $familyInformation,
        );
    }

    /**
     * Handle the FamilyInformation "updated" event.
     */
    public function updated(FamilyInformation $familyInformation): void
    {
        $employeeName = $familyInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $familyInformation->employee_id,
            action: LogAction::UPDATE,
            description: "$employeeName updated an family info entry with ID #{$familyInformation->id}",
            model: $familyInformation,
        );
    }

    /**
     * Handle the FamilyInformation "deleted" event.
     */
    public function deleted(FamilyInformation $familyInformation): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $familyInformation->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted family info of $ownerName with ID #{$familyInformation->id}",
            model: $familyInformation,
        );
    }

    /**
     * Handle the FamilyInformation "restored" event.
     */
    public function restored(FamilyInformation $familyInformation): void
    {
        //
    }

    /**
     * Handle the FamilyInformation "force deleted" event.
     */
    public function forceDeleted(FamilyInformation $familyInformation): void
    {
        //
    }
}
