<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';
    case SEPARATED = 'separated';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabel(MaritalStatus $case)
    {
        return ucfirst($case->value);
    }
}
