<?php

namespace App\Enums;

enum ShiftRequest: string
{
    case MORNING_ONLY = 'morning';
    case AFTERNOON_ONLY = 'afternoon';
    case FULL_DAY = 'full_day';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(self $case): string
    {
        return match ($case) {
            self::MORNING_ONLY => 'Morning Shift Only',
            self::AFTERNOON_ONLY => 'Afternoon Shift Only',
            self::FULL_DAY => 'Full Day',
        };
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = self::getLabel($case);
            return $carry;
        }, []);
    }
}