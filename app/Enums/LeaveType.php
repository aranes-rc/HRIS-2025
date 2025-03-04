<?php

namespace App\Enums;

enum LeaveType: string
{
    case SICK = 'sick';
    case ACADEMIC = 'academic';
    case VACATION = 'vacation';
    case MATERNITY = 'maternity';
    case PATERNITY = 'paternity';
    case EMERGENCY = 'emergency';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(LeaveType $case)
    {
        return ucfirst($case->value);
    }

    public static function options()
    {
        $dict = array();
        foreach (self::cases() as $case) {
            $dict[$case->value] = self::getLabel($case);
        }

        return $dict;
    }
}