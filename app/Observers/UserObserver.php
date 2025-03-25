<?php

namespace App\Observers;

use App\Enums\LogAction;
use App\Models\User;
use App\Services\ActionLogger;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $userInSession = request()->user();

        if (isset($userInSession)) {
            $updaterEmployee = $userInSession->employee;
            $updaterEmployeeId = $updaterEmployee->id;
            $updaterName = $updaterEmployee->getFullName();
    
            ActionLogger::log(
                employeeId: $updaterEmployeeId,
                action: LogAction::CREATE,
                description: "$updaterName created a user with ID #{$user->id}",
                model: $user,
            );
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::UPDATE,
            description: "$updaterName updated a user with ID #{$user->id}",
            model: $user,
        );
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted a user with ID #{$user->id}",
            model: $user,
        );
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
