<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    case Pending    = 'pending';
    case Approved   = 'approved';
    case Rejected   = 'rejected';

    public function label(): string
    {
        return match($this) {
            ClearanceStatus::Pending  => 'Pending',
            ClearanceStatus::Approved => 'Approved',
            ClearanceStatus::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            ClearanceStatus::Pending  => 'yellow',
            ClearanceStatus::Approved => 'green',
            ClearanceStatus::Rejected => 'red',
        };
    }
}