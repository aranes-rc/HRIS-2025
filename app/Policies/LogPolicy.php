<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function create(User $user)
    {
        return true;
    }

    public function view(User $user, Log $log)
    {
        return $user->isAdmin() || 
               $user->isHr() || 
               $log->employee_id === $user->employee->id;
    }

    public function update(User $user, Log $log)
    {
        return $user->isAdmin() || 
               $user->isHr() || 
               $log->employee_id === $user->employee->id;
    }

    public function delete(User $user, Log $log)
    {
        return $user->isAdmin() || 
               $user->isHr() || 
               $log->employee_id === $user->employee->id;
    }
}
