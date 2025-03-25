<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'employee_id', 
        'action', 
        'ip_address', 
        'user_agent', 
        'loggable_id', 
        'loggable_type', 
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
