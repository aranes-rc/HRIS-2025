<?php

namespace App\Enums;

enum WorkType: string
{
    case SATURDAY = 'saturday';
    case HOLIDAY = 'holiday';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(self $case): string
    {
        return match ($case) {
            self::SATURDAY => 'Saturday Work',
            self::HOLIDAY => 'Holiday Work',
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