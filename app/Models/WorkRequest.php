<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\ShiftRequest;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkRequest extends Model
{
    use HasFactory;

    protected $casts = [
        'request_date' => 'date',
        'work_type' => WorkType::class,
        'status' => RequestStatus::class,
        'shift_request' => ShiftRequest::class,
    ];

    protected $fillable = [
        'employee_id',
        'updated_by',
        'request_date',
        'work_type',
        'shift_request',
        'reason',
        'status',
        'proof_of_team_leader_approval',
        'proof_of_group_leader_approval',
        'proof_of_school_approval'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    public function isPending()
    {
        return $this->status === RequestStatus::PENDING;
    }

    public function isApproved()
    {
        return $this->status === RequestStatus::APPROVED;
    }

    public function isRejected()
    {
        return $this->status === RequestStatus::REJECTED;
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