<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\CharacterReference;
use App\Services\ActionLogger;

class CharacterReferenceObserver
{
    /**
     * Handle the CharacterReference "created" event.
     */
    public function created(CharacterReference $characterReference): void
    {
        $employeeName = $characterReference->employee->getFullName();

        ActionLogger::log(
            employeeId: $characterReference->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created a character reference entry with ID #{$characterReference->id}",
            model: $characterReference,
        );
    }

    /**
     * Handle the CharacterReference "updated" event.
     */
    public function updated(CharacterReference $characterReference): void
    {
        $employeeName = $characterReference->employee->getFullName();

        ActionLogger::log(
            employeeId: $characterReference->employee_id,
            action: LogAction::UPDATE,
            description: "$employeeName updated a character reference entry with ID #{$characterReference->id}",
            model: $characterReference,
        );
    }

    /**
     * Handle the CharacterReference "deleted" event.
     */
    public function deleted(CharacterReference $characterReference): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $characterReference->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted character reference entry of $ownerName with ID #{$characterReference->id}",
            model: $characterReference,
        );
    }

    /**
     * Handle the CharacterReference "restored" event.
     */
    public function restored(CharacterReference $characterReference): void
    {
        //
    }

    /**
     * Handle the CharacterReference "force deleted" event.
     */
    public function forceDeleted(CharacterReference $characterReference): void
    {
        //
    }
}
