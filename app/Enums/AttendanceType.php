<?php

namespace App\Enums;

use Illuminate\Support\Carbon;


enum AttendanceType: string
{
    case TIME_IN = 'time_in';
    case TIME_OUT = 'time_out';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getLabel(AttendanceType $case) {
        switch ($case) {
            case self::TIME_IN:
                return "Time In";
            case self::TIME_OUT:
                return "Time Out";
        }
    }

    public static function options()
    {
        $dict = array();
        foreach (self::cases() as $case) {
            $dict[$case->value] = self::getLabel($case);
        }

        return $dict;
    }

    public static function getCurrentAttendanceType(): ?AttendanceType {
        $MORNING_IN_START = Carbon::createFromTime(7, 45);
        $MORNING_IN_END = Carbon::createFromTime(8, 30);
        $MORNING_OUT_START = Carbon::createFromTime(12, 0);
        $MORNING_OUT_END = Carbon::createFromTime(12, 30);

        $NOON_IN_START = Carbon::createFromTime(12, 45);
        $NOON_IN_END = Carbon::createFromTime(13, 0);
        $NOON_OUT_START = Carbon::createFromTime(16, 45);
        $NOON_OUT_END = Carbon::createFromTime(17, 30);

        $now = Carbon::now();

        $shouldTimeIn = $now->between($MORNING_IN_START, $MORNING_IN_END)
            || $now->between($NOON_IN_START, $NOON_IN_END);
        $shouldTimeOut = $now->between($MORNING_OUT_START, $MORNING_OUT_END)
            || $now->between($NOON_OUT_START, $NOON_OUT_END);

        if($shouldTimeIn){
            return AttendanceType::TIME_IN;
        } else if ($shouldTimeOut) {
            return AttendanceType::TIME_OUT;
        }

        return null;
    }
}