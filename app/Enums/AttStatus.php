<?php

namespace App\Enums;

enum AttStatus: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PENDING => 'Pending',
        };
    }

    public static function getLabel(self|string $status): string
    {
        if (is_string($status)) {
            return match ($status) {
                self::APPROVED->value => 'Approved',
                self::REJECTED->value => 'Rejected',
                self::PENDING->value => 'Pending',
                default => 'Unknown',
            };
        }
        return $status->label();
    }
}