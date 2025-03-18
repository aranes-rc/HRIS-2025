<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjtInformation extends Model
{
    /** @use HasFactory<\Database\Factories\OjtInformationFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'coordinator_name',
        'coordinator_email',
        'coordinator_phone',
        // Add any other OJT-related fields
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
