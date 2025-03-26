<?php

namespace App\Services;

use App\Enums\LogAction;
use App\Models\Employee;
use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActionLogger
{
    public static function log(int $employeeId, LogAction $action, ?string $description = null, ?Model $model = null): void
    {
        Log::create([
            'employee_id' => $employeeId,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'loggable_id' => $model?->id,
            'loggable_type' => $model ? get_class($model) : null,
        ]);
    }
}
