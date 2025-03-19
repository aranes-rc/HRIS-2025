<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationInformation extends Model
{
    /** @use HasFactory<\Database\Factories\EducationInformationFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'required_hours',
        'course',
        'university_name',
        'university_address',
        'university_city',
        'university_province',
        'university_zip',
        // Add any other education-related fields
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
