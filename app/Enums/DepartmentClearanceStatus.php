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
            DepartmentClearanceStatus::Pending  => 'Pending',
            DepartmentClearanceStatus::Approved => 'Approved',
            DepartmentClearanceStatus::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            DepartmentClearanceStatus::Pending  => 'yellow',
            DepartmentClearanceStatus::Approved => 'green',
            DepartmentClearanceStatus::Rejected => 'red',
        };
    }
}