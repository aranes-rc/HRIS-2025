<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkRequest;

class WorkRequestPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, WorkRequest $workRequest)
    {
        return $user->isAdmin() || 
               $user->isHr() || 
               $workRequest->employee_id === $user->employee->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, WorkRequest $workRequest)
    {
        if ($user->isAdmin() || $user->isHr()) {
            return true;
        }

        return $workRequest->employee_id === $user->employee->id && 
               $workRequest->isPending();
    }

    public function delete(User $user, WorkRequest $workRequest)
    {
        return $this->update($user, $workRequest);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['status'] ?? null, fn($q, $status) =>
                $q->where('status', $status))
            ->when($filters['work_type'] ?? null, fn($q, $workType) =>
                $q->where('work_type', $workType))
            ->when($filters['date_from'] ?? null, fn($q, $dateFrom) =>
                $q->whereDate('request_date', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn($q, $dateTo) =>
                $q->whereDate('request_date', '<=', $dateTo));
    }

}