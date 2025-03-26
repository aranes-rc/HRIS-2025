<?php

namespace App\Enums;

enum LogAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case LOG_IN = 'log_in';
    case LOG_OUT = 'log_out';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(LogAction $case)
    {
        return match ($case) {
            self::LOG_IN => "Log in",
            self::LOG_OUT => "Log out",
            default => ucfirst($case->value),
        };
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