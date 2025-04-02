<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\Department;
use App\Enums\DepartmentTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Gender;
use App\Enums\EmploymentType;
use App\Enums\EmployeeStatus;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birthdate',
        'gender',
        'contact_number',
        'address',
        'emergency_contact_name',
        'emergency_contact_number',
        'hire_date',
        'employment_type',
        'department',
        'status',
        'attendance_status',
        'department_team',
        'group_number',
        'date_of_start',
        'date_of_orientation_day',
        'e_signature_path'
    ];

    protected $casts = [
        'department' => Department::class,
        'gender' => Gender::class,
        'employment_type' => EmploymentType::class,
        'status' => EmployeeStatus::class,
        'attendance_status' => AttendanceStatus::class,
        'department_team' => DepartmentTeam::class,
    ];

    protected $attributes = [
        'status' => EmployeeStatus::INACTIVE,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getFullName()
    {
        return $this->first_name." ".$this->last_name;
    }

    public function characterReferences()
    {
        return $this->hasOne(CharacterReference::class);
    }

    public function educationInformation()
    {
        return $this->hasOne(EducationInformation::class);
    }

    public function familyInformation()
    {
        return $this->hasOne(FamilyInformation::class);
    }

    public function ojtInformation()
    {
        return $this->hasOne(OjtInformation::class);
    }
}
