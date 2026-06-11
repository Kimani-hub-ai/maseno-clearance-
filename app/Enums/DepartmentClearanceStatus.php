<?php

namespace App\Enums;

enum DepartmentClearanceStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Awaiting Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
