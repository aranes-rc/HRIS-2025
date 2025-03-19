<?php

namespace App\Models;

use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyInformation extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyInformationFactory> */
    use HasFactory;

    protected $casts = [
        'marital_status' => MaritalStatus::class,
    ];

    protected $fillable = [
        'employee_id',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'number_of_siblings',
        'marital_status',
        'spouse_name',
        'spouse_occupation',
        'number_of_children',
        // Add any other family-related fields
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
