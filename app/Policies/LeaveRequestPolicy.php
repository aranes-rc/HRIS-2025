<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveRequest $leave): bool
    {
        $employee = $user->employee()->get()->first();
        return $user->isAdmin() || $user->isHr() || $leave->employee_id == $employee->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveRequest $leave): bool
    {
        $employee = $user->employee()->get()->first();
        return $user->isAdmin() || $user->isHr() || 
            ($leave->employee_id === $employee->id && $leave->isPending());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leave): bool
    {
        $employee = $user->employee()->get()->first();
        return $user->isAdmin() || ($leave->employee_id == $employee->id && $leave->isPending());
    }

    public function updateStatus(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
