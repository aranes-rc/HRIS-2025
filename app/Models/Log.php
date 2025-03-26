<?php

namespace App\Models;

use App\Enums\LogAction;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $casts = [
        'action' => LogAction::class
    ];
    
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
