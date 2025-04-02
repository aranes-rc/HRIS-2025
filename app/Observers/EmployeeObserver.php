<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\Employee;
use App\Services\ActionLogger;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        $user = auth()->user();

        if (isset($user)) {
            $userId = $user->id;
            $employeeName = $employee->getFullName();
    
            ActionLogger::log(
                employeeId: $employee->id,
                action: LogAction::CREATE,
                description: "User [$userId] created an employee named $employeeName with ID #{$employee->id}",
                model: $employee,
            );
        }
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $updaterEmployee = auth()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $employeeName = $employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName updated an employee named $employeeName with ID #{$employee->id}",
            model: $employee,
        );
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        $updaterEmployee = auth()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $employeeName = $employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted an employee named $employeeName with ID #{$employee->id}",
            model: $employee,
        );
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
