<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterReference extends Model
{
    /** @use HasFactory<\Database\Factories\CharacterReferenceFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'contact_number',
        'relationship',
        'position',
        'name_of_employer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
