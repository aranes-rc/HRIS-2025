<?php

namespace App\Models;

use App\Enums\ShiftType;
use App\Enums\AttendanceStatus;
use App\Enums\AttendanceType;
use App\Enums\WorkMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $casts = [
        'shift_type' => ShiftType::class,
        'type' => AttendanceType::class,
        'work_mode' => WorkMode::class,
    ];

    protected $attributes = [
        'work_mode' => WorkMode::ONSITE,
        'screenshot_workstation_selfie' => null,
        'screenshot_cgc_chat' => null,
        'screenshot_department_chat' => null,
        'screenshot_team_chat' => null,
        'screenshot_group_chat' => null,
    ];

    protected $fillable = [
        'employee_id',
        'shift_type',
        'type',
        'work_mode',
        'screenshot_workstation_selfie',
        'screenshot_cgc_chat',
        'screenshot_department_chat',
        'screenshot_team_chat',
        'screenshot_group_chat',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when(isset($filters['date_from']), fn($q) => 
                $q->where('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn($q) => 
                $q->where('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['employee_id']), fn($q) => 
                $q->where('employee_id', $filters['employee_id']))
            ->when(isset($filters['shift_type']), fn($q) => 
                $q->where('shift_type', $filters['shift_type']))
            ->when(isset($filters['type']), fn($q) => 
                $q->where('type', $filters['type']))
            ->when(isset($filters['work_mode']), fn($q) => 
                $q->where('work_mode', $filters['work_mode']));
    }

    public function scopeGroupedData($query, ?string $groupBy)
    {
        // TODO: Improve this shit
        return match ($groupBy) {
            'employee' => $query->select('employee_id', DB::raw('COUNT(*) as total_entries'))
                ->groupBy('employee_id'),
            'date' => $query->select('created_at', DB::raw('COUNT(*) as total_entries'))
                ->groupBy('created_at'),
            'shift_type' => $query->select('shift_type', DB::raw('COUNT(*) as total_entries'))
                ->groupBy('shift_type'),
            default => $query,
        };
    }
}
