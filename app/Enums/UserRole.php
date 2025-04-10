<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case HR = 'hr';
    case EMPLOYEE = 'employee';
    case TEAM_LEADER = 'team_leader';
    case GROUP_LEADER = 'group_leader';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabel(self $case): string
    {
        return match ($case) {
            self::HR => "HR",
            self::TEAM_LEADER => "Team Leader",
            self::GROUP_LEADER => "Group Leader",
            default => ucfirst($case->value)
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