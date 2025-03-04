<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(ShiftType $case)
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

    
    public static function getCurrentShiftType(): ?ShiftType {
        $MORNING_START = Carbon::createFromTime(7, 45);
        $MORNING_END = Carbon::createFromTime(12, 30);
        $NOON_START = Carbon::createFromTime(12, 45);
        $NOON_END = Carbon::createFromTime(17, 30);

        $now = Carbon::now();

        if($now->between($NOON_START, $NOON_END)){
            return ShiftType::AFTERNOON;
        } else if ($now->between($MORNING_START, $MORNING_END)) {
            return ShiftType::MORNING;
        }

        return null;
    }
}